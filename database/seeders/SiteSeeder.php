<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant1 = Tenant::where('subdomain', 'boxstore-paris')->first();
        $tenant2 = Tenant::where('subdomain', 'stocksecure-be')->first();
        $tenant3 = Tenant::where('subdomain', 'eurostorage')->first();

        // Sites for BoxStore Paris
        Site::create([
            'tenant_id' => $tenant1->id,
            'name' => 'BoxStore Paris Centre',
            'code' => 'PAR-001',
            'address' => '123 Rue de Rivoli',
            'postal_code' => '75001',
            'city' => 'Paris',
            'country' => 'FR',
            'phone' => '+33 1 23 45 67 89',
            'email' => 'paris-centre@boxstore.fr',
            'latitude' => 48.8606,
            'longitude' => 2.3376,
            'status' => 'active',
            'description' => 'Centre de stockage au cœur de Paris, accessible 24/7',
            'opening_hours' => json_encode([
                'monday' => ['09:00', '19:00'],
                'tuesday' => ['09:00', '19:00'],
                'wednesday' => ['09:00', '19:00'],
                'thursday' => ['09:00', '19:00'],
                'friday' => ['09:00', '19:00'],
                'saturday' => ['10:00', '18:00'],
                'sunday' => null,
            ]),
            'access_hours' => json_encode(['24/7' => true]),
            'has_surveillance' => true,
            'has_alarm' => true,
            'has_access_control' => true,
            'has_insurance' => true,
        ]);

        Site::create([
            'tenant_id' => $tenant1->id,
            'name' => 'BoxStore Montreuil',
            'code' => 'MTR-001',
            'address' => '45 Boulevard de la République',
            'postal_code' => '93100',
            'city' => 'Montreuil',
            'country' => 'FR',
            'phone' => '+33 1 48 58 68 78',
            'email' => 'montreuil@boxstore.fr',
            'latitude' => 48.8631,
            'longitude' => 2.4444,
            'status' => 'active',
            'description' => 'Grand centre de stockage climatisé',
            'has_surveillance' => true,
            'has_alarm' => true,
            'has_access_control' => true,
        ]);

        // Site for StockSecure Belgium
        Site::create([
            'tenant_id' => $tenant2->id,
            'name' => 'StockSecure Bruxelles',
            'code' => 'BRU-001',
            'address' => '78 Avenue Louise',
            'postal_code' => '1050',
            'city' => 'Bruxelles',
            'country' => 'BE',
            'phone' => '+32 2 123 45 67',
            'email' => 'bruxelles@stocksecure.be',
            'latitude' => 50.8333,
            'longitude' => 4.3667,
            'status' => 'active',
            'description' => 'Centre de stockage sécurisé à Bruxelles',
            'has_surveillance' => true,
            'has_alarm' => true,
            'has_access_control' => true,
        ]);

        // Sites for EuroStorage Group
        Site::create([
            'tenant_id' => $tenant3->id,
            'name' => 'EuroStorage Berlin',
            'code' => 'BER-001',
            'address' => '15 Friedrichstraße',
            'postal_code' => '10969',
            'city' => 'Berlin',
            'country' => 'DE',
            'phone' => '+49 30 12345678',
            'email' => 'berlin@eurostorage.com',
            'latitude' => 52.5200,
            'longitude' => 13.4050,
            'status' => 'active',
            'has_surveillance' => true,
            'has_alarm' => true,
            'has_access_control' => true,
        ]);

        Site::create([
            'tenant_id' => $tenant3->id,
            'name' => 'EuroStorage Amsterdam',
            'code' => 'AMS-001',
            'address' => '42 Herengracht',
            'postal_code' => '1015',
            'city' => 'Amsterdam',
            'country' => 'NL',
            'phone' => '+31 20 1234567',
            'email' => 'amsterdam@eurostorage.com',
            'latitude' => 52.3676,
            'longitude' => 4.9041,
            'status' => 'active',
            'has_surveillance' => true,
            'has_alarm' => true,
            'has_access_control' => true,
        ]);
    }
}
