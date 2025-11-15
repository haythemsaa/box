<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        $individualNames = [
            ['Sophie', 'Martin'],
            ['Pierre', 'Dubois'],
            ['Marie', 'Bernard'],
            ['Jean', 'Petit'],
            ['Emma', 'Durand'],
        ];

        $companies = [
            ['TechStart SAS', 'FR12345678901'],
            ['LogiStore SPRL', 'BE0987654321'],
            ['EuroTrade GmbH', 'DE123456789'],
        ];

        foreach ($tenants as $tenant) {
            // Create 3-5 individual customers per tenant
            foreach (array_slice($individualNames, 0, rand(3, 5)) as $name) {
                Customer::create([
                    'tenant_id' => $tenant->id,
                    'type' => 'individual',
                    'name' => $name[0] . ' ' . $name[1],
                    'first_name' => $name[0],
                    'last_name' => $name[1],
                    'email' => strtolower($name[0]) . '.' . strtolower($name[1]) . '@example.com',
                    'phone' => '+33 6 ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'address' => rand(1, 999) . ' Rue ' . $name[1],
                    'postal_code' => rand(10000, 99999),
                    'city' => 'Paris',
                    'country' => 'FR',
                    'date_of_birth' => now()->subYears(rand(25, 60))->format('Y-m-d'),
                    'status' => 'active',
                ]);
            }

            // Create 1-2 company customers per tenant
            foreach (array_slice($companies, 0, rand(1, 2)) as $company) {
                Customer::create([
                    'tenant_id' => $tenant->id,
                    'type' => 'company',
                    'name' => $company[0],
                    'company_name' => $company[0],
                    'siret' => $company[1],
                    'vat_number' => 'FR' . $company[1],
                    'email' => 'contact@' . strtolower(str_replace(' ', '', explode(' ', $company[0])[0])) . '.com',
                    'phone' => '+33 1 ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'address' => rand(1, 999) . ' Avenue de la République',
                    'postal_code' => rand(10000, 99999),
                    'city' => 'Paris',
                    'country' => 'FR',
                    'status' => 'active',
                ]);
            }
        }
    }
}
