<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();

        foreach ($buildings as $building) {
            // Create 2-4 floors per building
            $floorCount = rand(2, 4);

            for ($i = 0; $i < $floorCount; $i++) {
                Floor::create([
                    'building_id' => $building->id,
                    'name' => $i === 0 ? 'Rez-de-chaussée' : 'Étage ' . $i,
                    'floor_number' => $i,
                    'has_elevator_access' => $building->has_elevator && $i > 0,
                    'description' => $i === 0 ? 'Accès direct depuis l\'entrée' : 'Accès par escalier ou ascenseur',
                ]);
            }
        }
    }
}
