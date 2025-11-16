<?php

namespace Tests\Unit;

use App\Models\ContractInsurance;
use App\Models\Contract;
use App\Models\InsuranceProduct;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractInsuranceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_total_premium_paid_for_active_insurance()
    {
        // Arrange
        Carbon::setTestNow('2025-12-01');
        $insurance = ContractInsurance::factory()->create([
            'monthly_premium' => 10.00,
            'start_date' => '2025-06-01', // 6 months ago
            'status' => 'active',
        ]);

        // Act
        $totalPaid = $insurance->getTotalPremiumPaid();

        // Assert
        // 6 full months: June, July, August, September, October, November
        $this->assertEquals(60.00, $totalPaid);
    }

    /** @test */
    public function it_calculates_total_premium_paid_for_cancelled_insurance()
    {
        // Arrange
        $insurance = ContractInsurance::factory()->create([
            'monthly_premium' => 15.00,
            'start_date' => '2025-01-01',
            'end_date' => '2025-04-01', // 3 months
            'status' => 'cancelled',
        ]);

        // Act
        $totalPaid = $insurance->getTotalPremiumPaid();

        // Assert
        // 3 months: January, February, March
        $this->assertEquals(45.00, $totalPaid);
    }

    /** @test */
    public function it_calculates_minimum_one_month_premium()
    {
        // Arrange
        Carbon::setTestNow('2025-06-15');
        $insurance = ContractInsurance::factory()->create([
            'monthly_premium' => 20.00,
            'start_date' => '2025-06-10', // Less than 1 month
            'status' => 'active',
        ]);

        // Act
        $totalPaid = $insurance->getTotalPremiumPaid();

        // Assert
        // Should be at least 1 month even if less than 1 month has passed
        $this->assertEquals(20.00, $totalPaid);
    }

    /** @test */
    public function it_calculates_total_commission_earned()
    {
        // Arrange
        Carbon::setTestNow('2025-12-01');
        $insurance = ContractInsurance::factory()->create([
            'commission_amount' => 5.00,
            'start_date' => '2025-08-01', // 4 months ago
            'status' => 'active',
        ]);

        // Act
        $totalCommission = $insurance->getTotalCommissionEarned();

        // Assert
        // 4 months: August, September, October, November
        $this->assertEquals(20.00, $totalCommission);
    }

    /** @test */
    public function is_active_returns_true_for_active_status()
    {
        // Arrange
        $insurance = ContractInsurance::factory()->active()->create();

        // Act & Assert
        $this->assertTrue($insurance->isActive());
    }

    /** @test */
    public function is_active_returns_false_for_cancelled_status()
    {
        // Arrange
        $insurance = ContractInsurance::factory()->cancelled()->create();

        // Act & Assert
        $this->assertFalse($insurance->isActive());
    }

    /** @test */
    public function cancel_method_sets_correct_fields()
    {
        // Arrange
        Carbon::setTestNow('2025-11-16 14:30:00');
        $insurance = ContractInsurance::factory()->active()->create();

        // Act
        $insurance->cancel();

        // Assert
        $this->assertEquals('cancelled', $insurance->status);
        $this->assertEquals('2025-11-16', $insurance->end_date);
        $this->assertNotNull($insurance->cancelled_at);
        $this->assertEquals('2025-11-16 14:30:00', $insurance->cancelled_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function active_scope_returns_only_active_insurances()
    {
        // Arrange
        ContractInsurance::factory()->active()->count(2)->create();
        ContractInsurance::factory()->cancelled()->create();
        ContractInsurance::factory()->pending()->create();

        // Act
        $activeInsurances = ContractInsurance::active()->get();

        // Assert
        $this->assertCount(2, $activeInsurances);
        $this->assertTrue($activeInsurances->every(fn($i) => $i->status === 'active'));
    }

    /** @test */
    public function it_has_contract_relationship()
    {
        // Arrange
        $contract = Contract::factory()->create();
        $insurance = ContractInsurance::factory()->create([
            'contract_id' => $contract->id,
        ]);

        // Act
        $insuranceContract = $insurance->contract;

        // Assert
        $this->assertInstanceOf(Contract::class, $insuranceContract);
        $this->assertEquals($contract->id, $insuranceContract->id);
    }

    /** @test */
    public function it_has_insurance_product_relationship()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create();
        $insurance = ContractInsurance::factory()->create([
            'insurance_product_id' => $product->id,
        ]);

        // Act
        $insuranceProduct = $insurance->insuranceProduct;

        // Assert
        $this->assertInstanceOf(InsuranceProduct::class, $insuranceProduct);
        $this->assertEquals($product->id, $insuranceProduct->id);
    }

    /** @test */
    public function it_stores_historical_pricing()
    {
        // Arrange
        $product = InsuranceProduct::factory()->create([
            'monthly_price' => 19.90,
            'commission_rate' => 30.00,
        ]);

        $insurance = ContractInsurance::factory()->create([
            'insurance_product_id' => $product->id,
            'monthly_premium' => 19.90,
            'commission_amount' => 5.97,
        ]);

        // Act - Change product price
        $product->update(['monthly_price' => 29.90]);

        // Assert - Insurance should keep original price
        $insurance->refresh();
        $this->assertEquals(19.90, $insurance->monthly_premium);
        $this->assertNotEquals($product->monthly_price, $insurance->monthly_premium);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset Carbon time
        parent::tearDown();
    }
}
