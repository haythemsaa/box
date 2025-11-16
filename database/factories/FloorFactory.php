<?php

namespace Database\Factories;

use App\Models\Floor;
use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'floor_number' => $this->faker->numberBetween(0, 5),
            'name' => $this->faker->randomElement(['RDC', '1er étage', '2ème étage', 'Sous-sol']),
        ];
    }
}
