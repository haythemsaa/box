<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Box;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $startDate = now();
        $endDate = $startDate->copy()->addYear();

        return [
            'customer_id' => Customer::factory(),
            'box_id' => Box::factory(),
            'currency_id' => Currency::factory(),
            'contract_number' => 'CNT-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'monthly_amount' => $this->faker->randomFloat(2, 50, 500),
            'deposit_amount' => $this->faker->randomFloat(2, 100, 500),
            'billing_frequency' => $this->faker->randomElement(['monthly', 'quarterly', 'yearly']),
            'status' => 'active',
            'access_code' => $this->faker->numerify('####'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
