<?php

namespace Database\Factories;

use App\Models\InsuranceProduct;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsuranceProductFactory extends Factory
{
    protected $model = InsuranceProduct::class;

    public function definition(): array
    {
        return [
            'tenant_id' => null, // Global by default
            'name' => $this->faker->randomElement(['Assurance Essentielle', 'Assurance Confort', 'Assurance Premium']),
            'description' => $this->faker->sentence(),
            'max_coverage_amount' => $this->faker->randomFloat(2, 1000, 15000),
            'coverage_details' => 'Vol avec effraction, Incendie, Dégâts des eaux',
            'exclusions' => 'Objets de valeur > 500€, Bijoux',
            'monthly_price' => $this->faker->randomFloat(2, 4.90, 59.90),
            'yearly_price' => null,
            'commission_rate' => $this->faker->randomFloat(2, 20, 40),
            'is_active' => true,
            'is_mandatory' => false,
        ];
    }

    public function mandatory(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_mandatory' => true,
        ]);
    }

    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_mandatory' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
