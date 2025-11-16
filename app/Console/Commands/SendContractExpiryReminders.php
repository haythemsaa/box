<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Notifications\ContractExpiringNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendContractExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:send-expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send expiry reminders for contracts (30, 15, 7 days before)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting contract expiry reminders...');

        $today = Carbon::today();
        $remindersSent = 0;

        // Define reminder intervals (days before expiry)
        $intervals = [
            30 => '30 days',
            15 => '15 days',
            7 => '7 days',
        ];

        foreach ($intervals as $days => $label) {
            $targetDate = $today->copy()->addDays($days);

            $this->info("Processing reminders for {$label} before expiry ({$targetDate->format('Y-m-d')})...");

            // Find active contracts expiring on target date
            $contracts = Contract::where('status', 'active')
                ->whereDate('end_date', $targetDate)
                ->with('customer')
                ->get();

            foreach ($contracts as $contract) {
                try {
                    // Send notification to customer
                    $contract->customer->notify(new ContractExpiringNotification($contract, $days));
                    $remindersSent++;

                    $this->line("  → Sent expiry reminder for contract {$contract->contract_number} to {$contract->customer->email}");
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to send reminder for contract {$contract->contract_number}: {$e->getMessage()}");
                }
            }

            $this->info("  {$contracts->count()} reminder(s) sent for {$label}");
        }

        // Update expired contracts
        $expiredCount = Contract::where('status', 'active')
            ->where('end_date', '<', $today)
            ->update(['status' => 'expired']);

        $this->info("Updated {$expiredCount} contract(s) to expired status");

        $this->info("✓ Contract expiry reminders completed! Total sent: {$remindersSent}");

        return Command::SUCCESS;
    }
}
