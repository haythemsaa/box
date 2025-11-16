<?php

namespace Tests\Unit;

use App\Models\InsuranceProduct;
use App\Models\ContractInsurance;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsuranceProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_calculate_commission_correctly()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create([
            'monthly_price' => 100.00,
            'commission_rate' => 25.00,
        ]);

        // Act
        $commission = $product->calculateCommission();

        // Assert
        $this->assertEquals(25.00, $commission);
    }

    /** @test */
    public function it_formats_monthly_price_correctly()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create([
            'monthly_price' => 19.90,
        ]);

        // Act
        $formatted = $product->formatMonthlyPrice();

        // Assert
        $this->assertStringContainsString('19,90', $formatted);
        $this->assertStringContainsString('€', $formatted);
    }

    /** @test */
    public function it_calculates_yearly_savings_correctly()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create([
            'monthly_price' => 10.00,
            'yearly_price' => 100.00, // 2 months free (10*12 = 120, savings = 20)
        ]);

        // Act
        $savings = $product->getYearlySavings();

        // Assert
        $this->assertEquals(20.00, $savings);
    }

    /** @test */
    public function it_returns_zero_savings_when_no_yearly_price()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create([
            'monthly_price' => 10.00,
            'yearly_price' => null,
        ]);

        // Act
        $savings = $product->getYearlySavings();

        // Assert
        $this->assertEquals(0, $savings);
    }

    /** @test */
    public function active_scope_returns_only_active_products()
    {
        // Arrange
        InsuranceProduct::factory()->create(['is_active' => true]);
        InsuranceProduct::factory()->create(['is_active' => true]);
        InsuranceProduct::factory()->create(['is_active' => false]);

        // Act
        $activeProducts = InsuranceProduct::active()->get();

        // Assert
        $this->assertCount(2, $activeProducts);
        $this->assertTrue($activeProducts->every(fn($p) => $p->is_active === true));
    }

    /** @test */
    public function mandatory_scope_returns_only_mandatory_products()
    {
        // Arrange
        InsuranceProduct::factory()->mandatory()->create();
        InsuranceProduct::factory()->mandatory()->create();
        InsuranceProduct::factory()->optional()->create();

        // Act
        $mandatoryProducts = InsuranceProduct::mandatory()->get();

        // Assert
        $this->assertCount(2, $mandatoryProducts);
        $this->assertTrue($mandatoryProducts->every(fn($p) => $p->is_mandatory === true));
    }

    /** @test */
    public function for_tenant_scope_returns_only_tenant_products()
    {
        // Arrange
        $tenant = Tenant::factory()->create();
        InsuranceProduct::factory()->forTenant($tenant)->create();
        InsuranceProduct::factory()->forTenant($tenant)->create();
        InsuranceProduct::factory()->create(['tenant_id' => null]); // Global

        // Act
        $tenantProducts = InsuranceProduct::forTenant($tenant->id)->get();

        // Assert
        $this->assertCount(2, $tenantProducts);
        $this->assertTrue($tenantProducts->every(fn($p) => $p->tenant_id === $tenant->id));
    }

    /** @test */
    public function it_has_contract_insurances_relationship()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create();
        ContractInsurance::factory()->count(3)->create([
            'insurance_product_id' => $product->id,
        ]);

        // Act
        $insurances = $product->contractInsurances;

        // Assert
        $this->assertCount(3, $insurances);
        $this->assertInstanceOf(ContractInsurance::class, $insurances->first());
    }

    /** @test */
    public function it_has_tenant_relationship()
    {
        // Arrange
        $tenant = Tenant::factory()->create();
        $product = InsuranceProduct::factory()->forTenant($tenant)->create();

        // Act
        $productTenant = $product->tenant;

        // Assert
        $this->assertInstanceOf(Tenant::class, $productTenant);
        $this->assertEquals($tenant->id, $productTenant->id);
    }

    /** @test */
    public function global_products_have_null_tenant()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create([
            'tenant_id' => null,
        ]);

        // Act & Assert
        $this->assertNull($product->tenant_id);
        $this->assertNull($product->tenant);
    }
}
