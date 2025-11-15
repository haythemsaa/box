<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Box;
use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floors = Floor::all();

        // Standard box sizes with pricing
        $boxSizes = [
            ['length' => 100, 'width' => 100, 'height' => 200, 'price' => 49.99, 'type' => 'standard'], // 1m² XS
            ['length' => 150, 'width' => 100, 'height' => 200, 'price' => 69.99, 'type' => 'standard'], // 1.5m² S
            ['length' => 200, 'width' => 100, 'height' => 200, 'price' => 89.99, 'type' => 'standard'], // 2m² M
            ['length' => 200, 'width' => 150, 'height' => 200, 'price' => 119.99, 'type' => 'climat'], // 3m² L
            ['length' => 300, 'width' => 150, 'height' => 250, 'price' => 159.99, 'type' => 'climat'], // 4.5m² XL
            ['length' => 300, 'width' => 200, 'height' => 250, 'price' => 199.99, 'type' => 'premium'], // 6m² XXL
        ];

        $boxFeatures = [
            ['Accès 24/7', 'Surveillance'],
            ['Accès 24/7', 'Surveillance', 'Alarme'],
            ['Accès 24/7', 'Surveillance', 'Alarme', 'Climatisé'],
            ['Accès 24/7', 'Surveillance', 'Alarme', 'Climatisé', 'Assurance incluse'],
        ];

        $statuses = ['available', 'available', 'available', 'occupied', 'reserved'];

        foreach ($floors as $floor) {
            // Create 5-10 boxes per floor
            $boxCount = rand(5, 10);

            for ($i = 1; $i <= $boxCount; $i++) {
                $sizeConfig = $boxSizes[array_rand($boxSizes)];
                $features = $boxFeatures[array_rand($boxFeatures)];
                $status = $statuses[array_rand($statuses)];

                Box::create([
                    'floor_id' => $floor->id,
                    'number' => $floor->building->code . '-' . str_pad($floor->floor_number, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'length' => $sizeConfig['length'],
                    'width' => $sizeConfig['width'],
                    'height' => $sizeConfig['height'],
                    'monthly_price' => $sizeConfig['price'],
                    'type' => $sizeConfig['type'],
                    'status' => $status,
                    'features' => $features,
                    'description' => 'Box sécurisée avec accès contrôlé',
                ]);
            }
        }
    }
}
