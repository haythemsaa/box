<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition(): array
    {
        return [
            'site_id' => Site::factory(),
            'name' => $this->faker->randomElement(['Bâtiment A', 'Bâtiment B', 'Entrepôt Principal']),
        ];
    }
}
