<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Tenant;
use App\Models\Currency;
use App\Models\VatRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'currency_id' => Currency::factory(),
            'name' => $this->faker->company() . ' Storage',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->countryCode(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'is_active' => true,
        ];
    }
}
