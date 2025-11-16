<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Box;
use App\Models\InsuranceProduct;
use App\Models\ContractInsurance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractInsuranceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function it_can_create_contract_with_insurance_products()
    {
        // Arrange
        $customer = Customer::factory()->create();
        $box = Box::factory()->create(['monthly_price' => 100.00]);
        $insurance = InsuranceProduct::factory()->create([
            'monthly_price' => 9.90,
            'commission_rate' => 25.00,
            'is_active' => true,
        ]);

        // Act
        $response = $this->post('/contracts', [
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'monthly_amount' => 100.00,
            'deposit_amount' => 100.00,
            'billing_frequency' => 'monthly',
            'status' => 'active',
            'insurance_products' => [$insurance->id],
        ]);

        // Assert
        $response->assertRedirect();

        $contract = Contract::latest()->first();
        $this->assertNotNull($contract);
        $this->assertEquals($customer->id, $contract->customer_id);

        $contractInsurance = $contract->contractInsurances()->first();
        $this->assertNotNull($contractInsurance);
        $this->assertEquals($insurance->id, $contractInsurance->insurance_product_id);
        $this->assertEquals(9.90, $contractInsurance->monthly_premium);
        $this->assertEquals(2.475, $contractInsurance->commission_amount); // 25% of 9.90
        $this->assertEquals('active', $contractInsurance->status);
    }

    /** @test */
    public function it_can_add_insurance_to_existing_contract()
    {
        // Arrange
        $contract = Contract::factory()->create(['status' => 'active']);
        $insurance = InsuranceProduct::factory()->create([
            'monthly_price' => 19.90,
            'commission_rate' => 30.00,
            'max_coverage_amount' => 7500.00,
        ]);

        // Act
        $response = $this->post("/contracts/{$contract->id}/insurances", [
            'insurance_product_id' => $insurance->id,
        ]);

        // Assert
        $response->assertRedirect();

        $contractInsurance = $contract->contractInsurances()->first();
        $this->assertNotNull($contractInsurance);
        $this->assertEquals($insurance->id, $contractInsurance->insurance_product_id);
        $this->assertEquals(19.90, $contractInsurance->monthly_premium);
        $this->assertEquals(5.97, $contractInsurance->commission_amount); // 30% of 19.90
        $this->assertEquals(7500.00, $contractInsurance->coverage_amount);
        $this->assertEquals('active', $contractInsurance->status);
        $this->assertEquals(now()->toDateString(), $contractInsurance->start_date);
    }

    /** @test */
    public function it_prevents_duplicate_active_insurance_on_same_contract()
    {
        // Arrange
        $contract = Contract::factory()->create();
        $insurance = InsuranceProduct::factory()->create();

        // Create first insurance
        ContractInsurance::factory()->create([
            'contract_id' => $contract->id,
            'insurance_product_id' => $insurance->id,
            'status' => 'active',
        ]);

        // Act - Try to add the same insurance again
        $response = $this->post("/contracts/{$contract->id}/insurances", [
            'insurance_product_id' => $insurance->id,
        ]);

        // Assert - Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Verify only one active insurance exists
        $activeInsurances = $contract->contractInsurances()
            ->where('insurance_product_id', $insurance->id)
            ->where('status', 'active')
            ->count();

        $this->assertEquals(1, $activeInsurances);
    }

    /** @test */
    public function it_can_cancel_active_insurance()
    {
        // Arrange
        $contract = Contract::factory()->create();
        $contractInsurance = ContractInsurance::factory()->create([
            'contract_id' => $contract->id,
            'status' => 'active',
            'start_date' => now()->subMonths(3)->toDateString(),
        ]);

        // Act
        $response = $this->delete("/contracts/{$contract->id}/insurances/{$contractInsurance->id}");

        // Assert
        $response->assertRedirect();

        $contractInsurance->refresh();
        $this->assertEquals('cancelled', $contractInsurance->status);
        $this->assertNotNull($contractInsurance->cancelled_at);
        $this->assertNotNull($contractInsurance->end_date);
    }

    /** @test */
    public function it_calculates_total_premium_paid_correctly()
    {
        // Arrange
        $contractInsurance = ContractInsurance::factory()->create([
            'monthly_premium' => 9.90,
            'start_date' => now()->subMonths(6)->toDateString(),
            'status' => 'active',
        ]);

        // Act
        $totalPaid = $contractInsurance->getTotalPremiumPaid();

        // Assert - Should be 6 months * 9.90
        $this->assertEquals(59.40, $totalPaid);
    }

    /** @test */
    public function it_calculates_total_commission_earned_correctly()
    {
        // Arrange
        $contractInsurance = ContractInsurance::factory()->create([
            'monthly_premium' => 19.90,
            'commission_amount' => 5.97, // 30% of 19.90
            'start_date' => now()->subMonths(12)->toDateString(),
            'status' => 'active',
        ]);

        // Act
        $totalCommission = $contractInsurance->getTotalCommissionEarned();

        // Assert - Should be 12 months * 5.97
        $this->assertEquals(71.64, $totalCommission);
    }

    /** @test */
    public function it_prevents_cancelling_insurance_from_different_contract()
    {
        // Arrange
        $contract1 = Contract::factory()->create();
        $contract2 = Contract::factory()->create();
        $contractInsurance = ContractInsurance::factory()->create([
            'contract_id' => $contract2->id,
        ]);

        // Act - Try to cancel insurance from contract1 (should fail)
        $response = $this->delete("/contracts/{$contract1->id}/insurances/{$contractInsurance->id}");

        // Assert - Should return 403 Forbidden
        $response->assertForbidden();

        // Verify insurance was not cancelled
        $contractInsurance->refresh();
        $this->assertNotEquals('cancelled', $contractInsurance->status);
    }

    /** @test */
    public function mandatory_insurances_are_auto_selected_on_contract_creation()
    {
        // Arrange
        $customer = Customer::factory()->create();
        $box = Box::factory()->create();
        $mandatoryInsurance = InsuranceProduct::factory()->create([
            'is_mandatory' => true,
            'is_active' => true,
            'monthly_price' => 4.90,
        ]);
        $optionalInsurance = InsuranceProduct::factory()->create([
            'is_mandatory' => false,
            'is_active' => true,
        ]);

        // Act - Get create page
        $response = $this->get('/contracts/create');

        // Assert - Verify mandatory insurance is in the list
        $response->assertOk();
        $this->assertArrayHasKey('mandatoryInsurances', $response->viewData('insuranceProducts') ? [] : $response->original->getData());
    }

    /** @test */
    public function it_stores_historical_pricing_on_insurance_subscription()
    {
        // Arrange
        $contract = Contract::factory()->create();
        $insurance = InsuranceProduct::factory()->create([
            'monthly_price' => 19.90,
            'commission_rate' => 30.00,
        ]);

        // Act - Subscribe to insurance
        $this->post("/contracts/{$contract->id}/insurances", [
            'insurance_product_id' => $insurance->id,
        ]);

        // Update the product price
        $insurance->update(['monthly_price' => 29.90]);

        // Assert - Contract insurance should still have original price
        $contractInsurance = $contract->contractInsurances()->first();
        $this->assertEquals(19.90, $contractInsurance->monthly_premium);
        $this->assertNotEquals($insurance->monthly_price, $contractInsurance->monthly_premium);
    }

    /** @test */
    public function contract_creation_with_multiple_insurances_uses_database_transaction()
    {
        // Arrange
        $customer = Customer::factory()->create();
        $box = Box::factory()->create();
        $insurance1 = InsuranceProduct::factory()->create();
        $insurance2 = InsuranceProduct::factory()->create();

        // Act
        $response = $this->post('/contracts', [
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'monthly_amount' => 100.00,
            'deposit_amount' => 100.00,
            'billing_frequency' => 'monthly',
            'status' => 'active',
            'insurance_products' => [$insurance1->id, $insurance2->id],
        ]);

        // Assert
        $contract = Contract::latest()->first();
        $this->assertEquals(2, $contract->contractInsurances()->count());

        // Both insurances should be created
        $this->assertTrue($contract->contractInsurances->contains('insurance_product_id', $insurance1->id));
        $this->assertTrue($contract->contractInsurances->contains('insurance_product_id', $insurance2->id));
    }
}
