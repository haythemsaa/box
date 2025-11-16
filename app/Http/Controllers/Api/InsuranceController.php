<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsuranceProduct;
use App\Models\Contract;
use App\Models\ContractInsurance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InsuranceController extends Controller
{
    /**
     * Get all active insurance products
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = InsuranceProduct::active();

        // Filter by tenant if provided
        if ($request->has('tenant_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('tenant_id', $request->tenant_id)
                  ->orWhereNull('tenant_id'); // Include global products
            });
        }

        // Filter by mandatory status
        if ($request->has('mandatory_only') && $request->boolean('mandatory_only')) {
            $query->mandatory();
        }

        $products = $query->orderBy('monthly_price')->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'monthly_price' => (float) $product->monthly_price,
                'yearly_price' => $product->yearly_price ? (float) $product->yearly_price : null,
                'max_coverage_amount' => (float) $product->max_coverage_amount,
                'coverage_details' => $product->coverage_details,
                'exclusions' => $product->exclusions,
                'commission_rate' => (float) $product->commission_rate,
                'is_mandatory' => $product->is_mandatory,
                'yearly_savings' => $product->getYearlySavings(),
            ]),
        ]);
    }

    /**
     * Get insurance details by ID
     *
     * @param InsuranceProduct $product
     * @return JsonResponse
     */
    public function show(InsuranceProduct $product): JsonResponse
    {
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit d\'assurance n\'est plus disponible.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'monthly_price' => (float) $product->monthly_price,
                'yearly_price' => $product->yearly_price ? (float) $product->yearly_price : null,
                'max_coverage_amount' => (float) $product->max_coverage_amount,
                'coverage_details' => $product->coverage_details,
                'exclusions' => $product->exclusions,
                'commission_rate' => (float) $product->commission_rate,
                'is_mandatory' => $product->is_mandatory,
                'is_active' => $product->is_active,
                'yearly_savings' => $product->getYearlySavings(),
                'formatted_monthly_price' => $product->formatMonthlyPrice(),
            ],
        ]);
    }

    /**
     * Get contract insurances
     *
     * @param Contract $contract
     * @return JsonResponse
     */
    public function contractInsurances(Contract $contract): JsonResponse
    {
        $insurances = $contract->contractInsurances()
            ->with('insuranceProduct')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'contract_id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'insurances' => $insurances->map(fn($insurance) => [
                    'id' => $insurance->id,
                    'product_id' => $insurance->insurance_product_id,
                    'product_name' => $insurance->insuranceProduct->name,
                    'monthly_premium' => (float) $insurance->monthly_premium,
                    'commission_amount' => (float) $insurance->commission_amount,
                    'coverage_amount' => (float) $insurance->coverage_amount,
                    'start_date' => $insurance->start_date,
                    'end_date' => $insurance->end_date,
                    'status' => $insurance->status,
                    'is_active' => $insurance->isActive(),
                    'total_premium_paid' => $insurance->getTotalPremiumPaid(),
                    'total_commission_earned' => $insurance->getTotalCommissionEarned(),
                    'cancelled_at' => $insurance->cancelled_at?->toIso8601String(),
                ]),
                'total_monthly_premium' => $insurances->where('status', 'active')
                    ->sum('monthly_premium'),
                'active_count' => $insurances->where('status', 'active')->count(),
            ],
        ]);
    }

    /**
     * Add insurance to contract (API endpoint)
     *
     * @param Request $request
     * @param Contract $contract
     * @return JsonResponse
     */
    public function addToContract(Request $request, Contract $contract): JsonResponse
    {
        $validated = $request->validate([
            'insurance_product_id' => 'required|exists:insurance_products,id',
        ]);

        $product = InsuranceProduct::findOrFail($validated['insurance_product_id']);

        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit d\'assurance n\'est plus disponible.',
            ], 422);
        }

        // Check for duplicates
        $existing = ContractInsurance::where('contract_id', $contract->id)
            ->where('insurance_product_id', $product->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Cette assurance est déjà active pour ce contrat.',
            ], 422);
        }

        // Calculate commission
        $commission = $product->monthly_price * ($product->commission_rate / 100);

        // Create insurance
        $contractInsurance = ContractInsurance::create([
            'contract_id' => $contract->id,
            'insurance_product_id' => $product->id,
            'monthly_premium' => $product->monthly_price,
            'commission_amount' => $commission,
            'coverage_amount' => $product->max_coverage_amount,
            'start_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assurance ajoutée avec succès.',
            'data' => [
                'id' => $contractInsurance->id,
                'product_name' => $product->name,
                'monthly_premium' => (float) $contractInsurance->monthly_premium,
                'commission_amount' => (float) $contractInsurance->commission_amount,
                'coverage_amount' => (float) $contractInsurance->coverage_amount,
                'start_date' => $contractInsurance->start_date,
                'status' => $contractInsurance->status,
            ],
        ], 201);
    }

    /**
     * Cancel contract insurance (API endpoint)
     *
     * @param Contract $contract
     * @param ContractInsurance $insurance
     * @return JsonResponse
     */
    public function cancelFromContract(Contract $contract, ContractInsurance $insurance): JsonResponse
    {
        // Security check
        if ($insurance->contract_id !== $contract->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cette assurance n\'appartient pas à ce contrat.',
            ], 403);
        }

        if ($insurance->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cette assurance n\'est plus active.',
            ], 422);
        }

        // Cancel insurance
        $insurance->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Assurance annulée avec succès.',
            'data' => [
                'id' => $insurance->id,
                'status' => $insurance->status,
                'end_date' => $insurance->end_date,
                'cancelled_at' => $insurance->cancelled_at->toIso8601String(),
            ],
        ]);
    }
}
