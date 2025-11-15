<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Box;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get occupied boxes
        $occupiedBoxes = Box::where('status', 'occupied')->get();

        foreach ($occupiedBoxes as $box) {
            // Get a random customer from the same tenant
            $site = $box->floor->building->site;
            $customer = Customer::where('tenant_id', $site->tenant_id)
                ->inRandomOrder()
                ->first();

            if ($customer) {
                $startDate = now()->subMonths(rand(1, 12));
                $endDate = $startDate->copy()->addMonths(rand(6, 24));

                Contract::create([
                    'tenant_id' => $site->tenant_id,
                    'customer_id' => $customer->id,
                    'box_id' => $box->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'monthly_amount' => $box->monthly_price,
                    'deposit_amount' => $box->monthly_price * 2, // 2 months deposit
                    'billing_frequency' => 'monthly',
                    'status' => 'active',
                    'signed_at' => $startDate,
                    'signature_method' => 'electronic',
                    'stored_items' => ['Meubles', 'Cartons', 'Archives'],
                    'notes' => 'Contrat standard avec accès 24/7',
                ]);
            }
        }

        // Get reserved boxes
        $reservedBoxes = Box::where('status', 'reserved')->get();

        foreach ($reservedBoxes as $box) {
            $site = $box->floor->building->site;
            $customer = Customer::where('tenant_id', $site->tenant_id)
                ->inRandomOrder()
                ->first();

            if ($customer) {
                $startDate = now()->addDays(rand(1, 30));

                Contract::create([
                    'tenant_id' => $site->tenant_id,
                    'customer_id' => $customer->id,
                    'box_id' => $box->id,
                    'start_date' => $startDate,
                    'end_date' => $startDate->copy()->addMonths(12),
                    'monthly_amount' => $box->monthly_price,
                    'deposit_amount' => $box->monthly_price * 2,
                    'billing_frequency' => 'monthly',
                    'status' => 'pending',
                    'notes' => 'Réservation en attente de signature',
                ]);
            }
        }
    }
}
