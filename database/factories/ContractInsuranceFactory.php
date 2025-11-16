<?php

namespace Database\Factories;

use App\Models\ContractInsurance;
use App\Models\Contract;
use App\Models\InsuranceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractInsuranceFactory extends Factory
{
    protected $model = ContractInsurance::class;

    public function definition(): array
    {
        $monthlyPremium = $this->faker->randomFloat(2, 4.90, 59.90);
        $commissionRate = $this->faker->randomFloat(2, 20, 40);

        return [
            'contract_id' => Contract::factory(),
            'insurance_product_id' => InsuranceProduct::factory(),
            'monthly_premium' => $monthlyPremium,
            'commission_amount' => round($monthlyPremium * ($commissionRate / 100), 2),
            'coverage_amount' => $this->faker->randomFloat(2, 1000, 15000),
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'status' => 'active',
            'cancelled_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'end_date' => null,
            'cancelled_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'end_date' => now()->toDateString(),
            'cancelled_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}
