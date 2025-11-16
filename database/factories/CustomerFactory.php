<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['individual', 'company']);

        return [
            'tenant_id' => Tenant::factory(),
            'type' => $type,
            'first_name' => $type === 'individual' ? $this->faker->firstName() : null,
            'last_name' => $type === 'individual' ? $this->faker->lastName() : null,
            'company_name' => $type === 'company' ? $this->faker->company() : null,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->countryCode(),
            'is_active' => true,
        ];
    }

    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'individual',
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'company_name' => null,
        ]);
    }

    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'company',
            'company_name' => $this->faker->company(),
            'first_name' => null,
            'last_name' => null,
        ]);
    }
}
