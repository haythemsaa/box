<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class ExpireOldReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:expire-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire old pending reservations that have passed their expiration date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired reservations...');

        $expiredCount = Reservation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            $this->info("✓ Expired {$expiredCount} reservation(s)");
        } else {
            $this->info('No reservations to expire');
        }

        return Command::SUCCESS;
    }
}
