<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all sites
        $sites = Site::all();

        foreach ($sites as $site) {
            // Create 1-2 buildings per site
            $buildingCount = rand(1, 2);

            for ($i = 1; $i <= $buildingCount; $i++) {
                Building::create([
                    'site_id' => $site->id,
                    'name' => 'Bâtiment ' . chr(64 + $i), // A, B, C...
                    'code' => $site->code . '-B' . $i,
                    'description' => 'Bâtiment principal avec accès sécurisé',
                    'has_elevator' => $i === 1, // First building has elevator
                    'has_climate_control' => true,
                    'status' => 'active',
                ]);
            }
        }
    }
}
