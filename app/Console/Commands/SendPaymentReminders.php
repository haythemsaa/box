<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Customer;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders for unpaid invoices (J-7, J-3, J+1, J+3, J+7)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting payment reminders...');

        $today = Carbon::today();
        $remindersSent = 0;

        // Define reminder intervals (days before/after due date)
        $intervals = [
            7 => 'J-7',   // 7 days before
            3 => 'J-3',   // 3 days before
            -1 => 'J+1',  // 1 day after (overdue)
            -3 => 'J+3',  // 3 days after (overdue)
            -7 => 'J+7',  // 7 days after (overdue)
        ];

        foreach ($intervals as $days => $label) {
            $targetDate = $days > 0
                ? $today->copy()->addDays($days)
                : $today->copy()->subDays(abs($days));

            $this->info("Processing reminders for {$label} ({$targetDate->format('Y-m-d')})...");

            // Find unpaid invoices with due date matching target date
            $invoices = Invoice::where('status', 'unpaid')
                ->whereDate('due_date', $targetDate)
                ->with('customer')
                ->get();

            foreach ($invoices as $invoice) {
                try {
                    // Send notification to customer
                    $invoice->customer->notify(new PaymentReminderNotification($invoice, $days));
                    $remindersSent++;

                    $this->line("  → Sent reminder for invoice {$invoice->invoice_number} to {$invoice->customer->email}");
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to send reminder for invoice {$invoice->invoice_number}: {$e->getMessage()}");
                }
            }

            $this->info("  {$invoices->count()} reminder(s) sent for {$label}");
        }

        // Update overdue invoices status
        $overdueCount = Invoice::where('status', 'unpaid')
            ->where('due_date', '<', $today)
            ->update(['status' => 'overdue']);

        $this->info("Updated {$overdueCount} invoice(s) to overdue status");

        $this->info("✓ Payment reminders completed! Total sent: {$remindersSent}");

        return Command::SUCCESS;
    }
}
