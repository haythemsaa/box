<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tenant::create([
            'name' => 'BoxStore Paris',
            'subdomain' => 'boxstore-paris',
            'email' => 'contact@boxstore-paris.fr',
            'phone' => '+33 1 23 45 67 89',
            'plan' => 'professional',
            'status' => 'active',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addYear(),
            'max_sites' => 5,
            'max_boxes' => 500,
        ]);

        Tenant::create([
            'name' => 'StockSecure Belgium',
            'subdomain' => 'stocksecure-be',
            'email' => 'info@stocksecure.be',
            'phone' => '+32 2 123 45 67',
            'plan' => 'starter',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(14),
            'subscription_ends_at' => now()->addMonth(),
            'max_sites' => 1,
            'max_boxes' => 100,
        ]);

        Tenant::create([
            'name' => 'EuroStorage Group',
            'subdomain' => 'eurostorage',
            'custom_domain' => 'eurostorage.com',
            'email' => 'hello@eurostorage.com',
            'phone' => '+49 30 12345678',
            'plan' => 'enterprise',
            'status' => 'active',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addYears(2),
            'max_sites' => null, // unlimited
            'max_boxes' => null, // unlimited
        ]);
    }
}
