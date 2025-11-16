<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VatRate;

class VatRateSeeder extends Seeder
{
    public function run(): void
    {
        $vatRates = [
            // EU Countries
            ['country_code' => 'FR', 'country_name' => 'France', 'standard_rate' => 20.00, 'reduced_rate' => 10.00, 'super_reduced_rate' => 5.50, 'is_eu_member' => true],
            ['country_code' => 'BE', 'country_name' => 'Belgium', 'standard_rate' => 21.00, 'reduced_rate' => 12.00, 'super_reduced_rate' => 6.00, 'is_eu_member' => true],
            ['country_code' => 'NL', 'country_name' => 'Netherlands', 'standard_rate' => 21.00, 'reduced_rate' => 9.00, 'super_reduced_rate' => null, 'is_eu_member' => true],
            ['country_code' => 'DE', 'country_name' => 'Germany', 'standard_rate' => 19.00, 'reduced_rate' => 7.00, 'super_reduced_rate' => null, 'is_eu_member' => true],
            ['country_code' => 'ES', 'country_name' => 'Spain', 'standard_rate' => 21.00, 'reduced_rate' => 10.00, 'super_reduced_rate' => 4.00, 'is_eu_member' => true],
            ['country_code' => 'IT', 'country_name' => 'Italy', 'standard_rate' => 22.00, 'reduced_rate' => 10.00, 'super_reduced_rate' => 5.00, 'is_eu_member' => true],
            ['country_code' => 'PT', 'country_name' => 'Portugal', 'standard_rate' => 23.00, 'reduced_rate' => 13.00, 'super_reduced_rate' => 6.00, 'is_eu_member' => true],
            ['country_code' => 'AT', 'country_name' => 'Austria', 'standard_rate' => 20.00, 'reduced_rate' => 13.00, 'super_reduced_rate' => 10.00, 'is_eu_member' => true],
            ['country_code' => 'PL', 'country_name' => 'Poland', 'standard_rate' => 23.00, 'reduced_rate' => 8.00, 'super_reduced_rate' => 5.00, 'is_eu_member' => true],
            ['country_code' => 'CZ', 'country_name' => 'Czech Republic', 'standard_rate' => 21.00, 'reduced_rate' => 12.00, 'super_reduced_rate' => null, 'is_eu_member' => true],
            ['country_code' => 'DK', 'country_name' => 'Denmark', 'standard_rate' => 25.00, 'reduced_rate' => null, 'super_reduced_rate' => null, 'is_eu_member' => true],
            ['country_code' => 'SE', 'country_name' => 'Sweden', 'standard_rate' => 25.00, 'reduced_rate' => 12.00, 'super_reduced_rate' => 6.00, 'is_eu_member' => true],
            ['country_code' => 'FI', 'country_name' => 'Finland', 'standard_rate' => 24.00, 'reduced_rate' => 14.00, 'super_reduced_rate' => 10.00, 'is_eu_member' => true],
            ['country_code' => 'IE', 'country_name' => 'Ireland', 'standard_rate' => 23.00, 'reduced_rate' => 13.50, 'super_reduced_rate' => 9.00, 'is_eu_member' => true],
            ['country_code' => 'GR', 'country_name' => 'Greece', 'standard_rate' => 24.00, 'reduced_rate' => 13.00, 'super_reduced_rate' => 6.00, 'is_eu_member' => true],
            // Non-EU
            ['country_code' => 'GB', 'country_name' => 'United Kingdom', 'standard_rate' => 20.00, 'reduced_rate' => 5.00, 'super_reduced_rate' => null, 'is_eu_member' => false],
            ['country_code' => 'CH', 'country_name' => 'Switzerland', 'standard_rate' => 7.70, 'reduced_rate' => 3.70, 'super_reduced_rate' => 2.50, 'is_eu_member' => false],
            ['country_code' => 'NO', 'country_name' => 'Norway', 'standard_rate' => 25.00, 'reduced_rate' => 15.00, 'super_reduced_rate' => 12.00, 'is_eu_member' => false],
        ];

        foreach ($vatRates as $rate) {
            VatRate::updateOrCreate(['country_code' => $rate['country_code']], $rate);
        }
        $this->command->info('✓ VAT rates seeded for ' . count($vatRates) . ' countries!');
    }
}
