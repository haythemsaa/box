<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\Floor;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoxFactory extends Factory
{
    protected $model = Box::class;

    public function definition(): array
    {
        $width = $this->faker->randomFloat(2, 1.5, 5);
        $length = $this->faker->randomFloat(2, 2, 8);
        $height = $this->faker->randomFloat(2, 2, 3);

        return [
            'floor_id' => Floor::factory(),
            'box_number' => $this->faker->unique()->numerify('BOX-###'),
            'width' => $width,
            'length' => $length,
            'height' => $height,
            'area' => round($width * $length, 2),
            'volume' => round($width * $length * $height, 2),
            'monthly_price' => $this->faker->randomFloat(2, 50, 500),
            'status' => 'available',
            'has_electricity' => $this->faker->boolean(60),
            'has_lighting' => $this->faker->boolean(80),
            'is_climate_controlled' => $this->faker->boolean(40),
            'access_code' => $this->faker->numerify('####'),
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
        ]);
    }
}
