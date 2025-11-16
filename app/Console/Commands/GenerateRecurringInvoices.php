<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\ContractInsurance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-recurring
                            {--dry-run : Run without actually creating invoices}
                            {--date= : Generate invoices for specific date (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring monthly invoices for all active contracts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $targetDate = $this->option('date') ? date('Y-m-d', strtotime($this->option('date'))) : now()->toDateString();

        $this->info("🔄 Starting recurring invoice generation for {$targetDate}");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No invoices will be created');
        }

        // Get all active contracts
        $contracts = Contract::where('status', 'active')
            ->where('start_date', '<=', $targetDate)
            ->where(function ($query) use ($targetDate) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $targetDate);
            })
            ->with(['customer', 'box.site', 'currency', 'contractInsurances' => function ($query) {
                $query->active();
            }])
            ->get();

        $this->info("📋 Found {$contracts->count()} active contracts");

        $invoicesCreated = 0;
        $invoicesSkipped = 0;
        $totalAmount = 0;
        $errors = [];

        foreach ($contracts as $contract) {
            try {
                // Check if invoice already exists for this period
                $invoiceMonth = date('Y-m', strtotime($targetDate));
                $existingInvoice = Invoice::where('contract_id', $contract->id)
                    ->whereYear('issue_date', date('Y', strtotime($targetDate)))
                    ->whereMonth('issue_date', date('m', strtotime($targetDate)))
                    ->first();

                if ($existingInvoice) {
                    $this->line("⏭️  Skipping {$contract->contract_number} - Invoice already exists ({$existingInvoice->invoice_number})");
                    $invoicesSkipped++;
                    continue;
                }

                // Determine billing frequency - skip if not monthly
                if ($contract->billing_frequency !== 'monthly') {
                    // For quarterly, yearly, etc., check if this is the right month to bill
                    if (!$this->shouldBillThisMonth($contract, $targetDate)) {
                        $this->line("⏭️  Skipping {$contract->contract_number} - Not billing month (frequency: {$contract->billing_frequency})");
                        $invoicesSkipped++;
                        continue;
                    }
                }

                // Calculate invoice amount
                $subtotal = (float) $contract->monthly_rent;
                $description = "Loyer mensuel - Box {$contract->box->box_number}";

                // Add active insurances
                $insuranceAmount = 0;
                $insuranceDescriptions = [];
                foreach ($contract->contractInsurances as $insurance) {
                    $insuranceAmount += (float) $insurance->monthly_premium;
                    $insuranceDescriptions[] = $insurance->insuranceProduct->name . ' (' . number_format($insurance->monthly_premium, 2) . ' €)';
                }

                if ($insuranceAmount > 0) {
                    $subtotal += $insuranceAmount;
                    $description .= "\nAssurances: " . implode(', ', $insuranceDescriptions);
                }

                // Calculate VAT
                $vatRate = $contract->box->site->vatRate;
                $vatPercentage = $vatRate ? $vatRate->getRate('standard') : 20.00;
                $vatAmount = $subtotal * ($vatPercentage / 100);
                $totalAmount += $subtotal + $vatAmount;

                // Prepare invoice data
                $invoiceData = [
                    'contract_id' => $contract->id,
                    'customer_id' => $contract->customer_id,
                    'currency_id' => $contract->currency_id ?? $contract->box->site->currency_id,
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'issue_date' => $targetDate,
                    'due_date' => date('Y-m-d', strtotime($targetDate . ' +30 days')),
                    'subtotal_amount' => $subtotal,
                    'vat_rate' => $vatPercentage,
                    'vat_amount' => $vatAmount,
                    'total_amount' => $subtotal + $vatAmount,
                    'amount_paid' => 0,
                    'status' => 'unpaid',
                    'description' => $description,
                    'country_code' => $contract->box->site->country,
                ];

                if (!$dryRun) {
                    DB::transaction(function () use ($invoiceData) {
                        Invoice::create($invoiceData);
                    });
                }

                $this->info("✓ Created invoice for {$contract->contract_number} - Customer: {$contract->customer->first_name} {$contract->customer->last_name} - Amount: " . number_format($subtotal + $vatAmount, 2) . " €");
                $invoicesCreated++;

            } catch (\Exception $e) {
                $error = "✗ Error creating invoice for {$contract->contract_number}: {$e->getMessage()}";
                $this->error($error);
                $errors[] = $error;
                continue;
            }
        }

        // Summary
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 SUMMARY');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Active contracts', $contracts->count()],
                ['Invoices created', $invoicesCreated],
                ['Invoices skipped', $invoicesSkipped],
                ['Total amount', number_format($totalAmount, 2) . ' €'],
                ['Errors', count($errors)],
            ]
        );

        if (!empty($errors)) {
            $this->newLine();
            $this->error('❌ ERRORS:');
            foreach ($errors as $error) {
                $this->line($error);
            }
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  DRY RUN - No changes were made to the database');
        }

        return Command::SUCCESS;
    }

    /**
     * Check if contract should be billed this month based on frequency.
     */
    private function shouldBillThisMonth(Contract $contract, string $targetDate): bool
    {
        $targetMonth = (int) date('m', strtotime($targetDate));
        $startMonth = (int) date('m', strtotime($contract->start_date));

        switch ($contract->billing_frequency) {
            case 'monthly':
                return true;

            case 'quarterly':
                // Bill every 3 months starting from start_date month
                $monthsSinceStart = ($targetMonth - $startMonth + 12) % 12;
                return $monthsSinceStart % 3 === 0;

            case 'yearly':
                // Bill once a year on the same month as start_date
                return $targetMonth === $startMonth;

            default:
                return false;
        }
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -5)) + 1 : 1;

        return 'INV-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}
