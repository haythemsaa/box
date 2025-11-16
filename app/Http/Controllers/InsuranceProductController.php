<?php

namespace App\Http\Controllers;

use App\Models\InsuranceProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class InsuranceProductController extends Controller
{
    /**
     * Display a listing of insurance products.
     */
    public function index(Request $request): Response
    {
        $query = InsuranceProduct::query()->with('tenant');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by mandatory
        if ($request->has('is_mandatory') && $request->input('is_mandatory') !== '') {
            $query->where('is_mandatory', $request->boolean('is_mandatory'));
        }

        // Filter by tenant
        if ($tenantId = $request->input('tenant_id')) {
            if ($tenantId === 'global') {
                $query->whereNull('tenant_id');
            } else {
                $query->where('tenant_id', $tenantId);
            }
        }

        $insuranceProducts = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('InsuranceProducts/Index', [
            'insuranceProducts' => $insuranceProducts,
            'filters' => $request->only(['search', 'is_active', 'is_mandatory', 'tenant_id']),
        ]);
    }

    /**
     * Show the form for creating a new insurance product.
     */
    public function create(): Response
    {
        return Inertia::render('InsuranceProducts/Create');
    }

    /**
     * Store a newly created insurance product.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_coverage_amount' => 'required|numeric|min:0',
            'coverage_details' => 'nullable|string',
            'exclusions' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_mandatory' => 'boolean',
        ]);

        // Ensure booleans have default values
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_mandatory'] = $validated['is_mandatory'] ?? false;

        InsuranceProduct::create($validated);

        return redirect()->route('insurance-products.index')
            ->with('success', 'Produit d\'assurance créé avec succès.');
    }

    /**
     * Display the specified insurance product.
     */
    public function show(InsuranceProduct $insuranceProduct): Response
    {
        $insuranceProduct->load(['tenant', 'contractInsurances.contract.customer']);

        // Calculate statistics
        $stats = [
            'total_subscriptions' => $insuranceProduct->contractInsurances()->count(),
            'active_subscriptions' => $insuranceProduct->contractInsurances()->active()->count(),
            'total_premium_revenue' => $insuranceProduct->contractInsurances()
                ->active()
                ->get()
                ->sum(function ($contractInsurance) {
                    return $contractInsurance->getTotalPremiumPaid();
                }),
            'total_commission_earned' => $insuranceProduct->contractInsurances()
                ->active()
                ->get()
                ->sum(function ($contractInsurance) {
                    return $contractInsurance->getTotalCommissionEarned();
                }),
        ];

        // Get recent subscriptions
        $recentSubscriptions = $insuranceProduct->contractInsurances()
            ->with(['contract.customer', 'contract.box'])
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('InsuranceProducts/Show', [
            'insuranceProduct' => $insuranceProduct,
            'stats' => $stats,
            'recentSubscriptions' => $recentSubscriptions,
        ]);
    }

    /**
     * Show the form for editing the specified insurance product.
     */
    public function edit(InsuranceProduct $insuranceProduct): Response
    {
        $insuranceProduct->load('tenant');

        return Inertia::render('InsuranceProducts/Edit', [
            'insuranceProduct' => $insuranceProduct,
        ]);
    }

    /**
     * Update the specified insurance product.
     */
    public function update(Request $request, InsuranceProduct $insuranceProduct): RedirectResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_coverage_amount' => 'required|numeric|min:0',
            'coverage_details' => 'nullable|string',
            'exclusions' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_mandatory' => 'boolean',
        ]);

        // Ensure booleans are set
        $validated['is_active'] = $validated['is_active'] ?? $insuranceProduct->is_active;
        $validated['is_mandatory'] = $validated['is_mandatory'] ?? $insuranceProduct->is_mandatory;

        $insuranceProduct->update($validated);

        return redirect()->route('insurance-products.index')
            ->with('success', 'Produit d\'assurance mis à jour avec succès.');
    }

    /**
     * Remove the specified insurance product.
     */
    public function destroy(InsuranceProduct $insuranceProduct): RedirectResponse
    {
        // Check if product has active subscriptions
        $activeSubscriptions = $insuranceProduct->contractInsurances()->active()->count();

        if ($activeSubscriptions > 0) {
            return redirect()->route('insurance-products.index')
                ->with('error', 'Impossible de supprimer ce produit car il a des souscriptions actives.');
        }

        $insuranceProduct->delete();

        return redirect()->route('insurance-products.index')
            ->with('success', 'Produit d\'assurance supprimé avec succès.');
    }

    /**
     * Get active insurance products for contract creation.
     */
    public function getActive(Request $request)
    {
        $query = InsuranceProduct::query()->active();

        // Filter by tenant if provided
        if ($tenantId = $request->input('tenant_id')) {
            $query->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)
                  ->orWhereNull('tenant_id'); // Include global products
            });
        } else {
            $query->whereNull('tenant_id'); // Only global products if no tenant specified
        }

        $products = $query->orderBy('monthly_price')->get();

        return response()->json($products);
    }
}
