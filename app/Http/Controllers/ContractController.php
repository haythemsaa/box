<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Box;
use App\Models\InsuranceProduct;
use App\Models\ContractInsurance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Contract::with(['customer', 'box.floor.building.site']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by contract number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $contracts = $query->latest()
            ->paginate(15)
            ->through(function ($contract) {
                return [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'customer_name' => $contract->customer->name,
                    'customer_id' => $contract->customer->id,
                    'box_number' => $contract->box->number,
                    'box_id' => $contract->box->id,
                    'site_name' => $contract->box->floor->building->site->name,
                    'start_date' => $contract->start_date->format('d/m/Y'),
                    'end_date' => $contract->end_date->format('d/m/Y'),
                    'monthly_amount' => $contract->monthly_amount,
                    'status' => $contract->status,
                    'created_at' => $contract->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('Contracts/Index', [
            'contracts' => $contracts,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $customers = Customer::where('status', 'active')
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        $availableBoxes = Box::where('status', 'available')
            ->with('floor.building.site')
            ->get()
            ->map(function ($box) {
                return [
                    'id' => $box->id,
                    'number' => $box->number,
                    'site_name' => $box->floor->building->site->name,
                    'floor_name' => $box->floor->name,
                    'monthly_price' => $box->monthly_price,
                    'volume' => $box->volume,
                    'area' => $box->area,
                ];
            });

        // Get active insurance products (global products + tenant-specific if applicable)
        $insuranceProducts = InsuranceProduct::active()
            ->whereNull('tenant_id') // For now, only global products
            ->orderBy('monthly_price')
            ->get()
            ->map(function ($product) {
                return [
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
                ];
            });

        // Get mandatory insurance products
        $mandatoryInsurances = InsuranceProduct::active()
            ->mandatory()
            ->whereNull('tenant_id')
            ->get()
            ->pluck('id')
            ->toArray();

        return Inertia::render('Contracts/Create', [
            'customers' => $customers,
            'availableBoxes' => $availableBoxes,
            'insuranceProducts' => $insuranceProducts,
            'mandatoryInsurances' => $mandatoryInsurances,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'box_id' => 'required|exists:boxes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'billing_frequency' => 'required|in:monthly,quarterly,yearly',
            'status' => 'required|in:draft,pending,active,expired,cancelled',
            'notes' => 'nullable|string',
            'insurance_products' => 'nullable|array',
            'insurance_products.*' => 'exists:insurance_products,id',
        ]);

        // Get tenant_id from customer
        $customer = Customer::findOrFail($validated['customer_id']);
        $validated['tenant_id'] = $customer->tenant_id;

        DB::transaction(function () use ($validated, $request, &$contract) {
            $contract = Contract::create($validated);

            // Update box status to occupied if contract is active
            if ($validated['status'] === 'active') {
                Box::find($validated['box_id'])->update(['status' => 'occupied']);
            }

            // Create insurance subscriptions if any selected
            if ($request->has('insurance_products') && is_array($request->insurance_products)) {
                foreach ($request->insurance_products as $productId) {
                    $product = InsuranceProduct::findOrFail($productId);

                    // Calculate commission
                    $commission = $product->monthly_price * ($product->commission_rate / 100);

                    ContractInsurance::create([
                        'contract_id' => $contract->id,
                        'insurance_product_id' => $product->id,
                        'monthly_premium' => $product->monthly_price,
                        'commission_amount' => $commission,
                        'coverage_amount' => $product->max_coverage_amount,
                        'start_date' => $validated['start_date'],
                        'end_date' => null, // Open-ended, will follow contract
                        'status' => $validated['status'] === 'active' ? 'active' : 'pending',
                    ]);
                }
            }
        });

        return redirect()->route('contracts.show', $contract->id)
            ->with('success', 'Contrat créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract): Response
    {
        $contract->load([
            'customer',
            'box.floor.building.site',
            'contractInsurances.insuranceProduct',
            'invoices' => function ($query) {
                $query->latest()->limit(10);
            },
            'payments' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);

        // Calculate total monthly with insurances
        $totalMonthlyWithInsurance = (float) $contract->monthly_amount;
        $activeInsurances = $contract->contractInsurances->where('status', 'active');
        foreach ($activeInsurances as $insurance) {
            $totalMonthlyWithInsurance += (float) $insurance->monthly_premium;
        }

        return Inertia::render('Contracts/Show', [
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'status' => $contract->status,
                'start_date' => $contract->start_date->format('d/m/Y'),
                'end_date' => $contract->end_date->format('d/m/Y'),
                'monthly_amount' => $contract->monthly_amount,
                'deposit_amount' => $contract->deposit_amount,
                'billing_frequency' => $contract->billing_frequency,
                'access_code' => $contract->access_code,
                'signed_at' => $contract->signed_at?->format('d/m/Y H:i'),
                'signature_method' => $contract->signature_method,
                'stored_items' => $contract->stored_items,
                'notes' => $contract->notes,
                'created_at' => $contract->created_at->format('d/m/Y H:i'),
                'total_monthly_with_insurance' => $totalMonthlyWithInsurance,
                'customer' => [
                    'id' => $contract->customer->id,
                    'name' => $contract->customer->name,
                    'type' => $contract->customer->type,
                    'email' => $contract->customer->email,
                    'phone' => $contract->customer->phone,
                ],
                'box' => [
                    'id' => $contract->box->id,
                    'number' => $contract->box->number,
                    'volume' => $contract->box->volume,
                    'area' => $contract->box->area,
                    'site_name' => $contract->box->floor->building->site->name,
                    'floor_name' => $contract->box->floor->name,
                ],
                'insurances' => $contract->contractInsurances->map(function ($insurance) {
                    return [
                        'id' => $insurance->id,
                        'product_name' => $insurance->insuranceProduct->name,
                        'product_id' => $insurance->insurance_product_id,
                        'monthly_premium' => (float) $insurance->monthly_premium,
                        'commission_amount' => (float) $insurance->commission_amount,
                        'coverage_amount' => (float) $insurance->coverage_amount,
                        'status' => $insurance->status,
                        'start_date' => $insurance->start_date->format('d/m/Y'),
                        'end_date' => $insurance->end_date?->format('d/m/Y'),
                        'total_premium_paid' => $insurance->getTotalPremiumPaid(),
                        'total_commission_earned' => $insurance->getTotalCommissionEarned(),
                    ];
                }),
                'invoices' => $contract->invoices->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'amount' => $invoice->total_amount,
                        'status' => $invoice->status,
                        'due_date' => $invoice->due_date->format('d/m/Y'),
                    ];
                }),
                'payments' => $contract->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'payment_reference' => $payment->payment_reference,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'status' => $payment->status,
                        'paid_at' => $payment->paid_at?->format('d/m/Y'),
                    ];
                }),
            ],
            'availableInsuranceProducts' => InsuranceProduct::active()
                ->whereNull('tenant_id')
                ->orderBy('monthly_price')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'monthly_price' => (float) $product->monthly_price,
                        'max_coverage_amount' => (float) $product->max_coverage_amount,
                        'is_mandatory' => $product->is_mandatory,
                    ];
                }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract): Response
    {
        return Inertia::render('Contracts/Edit', [
            'contract' => $contract->load(['customer', 'box']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $validated = $request->validate([
            'end_date' => 'required|date|after:start_date',
            'monthly_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'billing_frequency' => 'required|in:monthly,quarterly,yearly',
            'status' => 'required|in:draft,pending,active,expired,cancelled',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $contract->status;
        $contract->update($validated);

        // Update box status based on contract status change
        if ($oldStatus !== $validated['status']) {
            if ($validated['status'] === 'active') {
                $contract->box->update(['status' => 'occupied']);
            } elseif (in_array($validated['status'], ['expired', 'cancelled'])) {
                $contract->box->update(['status' => 'available']);
            }
        }

        return redirect()->route('contracts.show', $contract->id)
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract): RedirectResponse
    {
        // Set box back to available
        $contract->box->update(['status' => 'available']);

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contrat supprimé avec succès.');
    }

    /**
     * Add an insurance to a contract.
     */
    public function addInsurance(Request $request, Contract $contract): RedirectResponse
    {
        $validated = $request->validate([
            'insurance_product_id' => 'required|exists:insurance_products,id',
        ]);

        // Check if insurance already exists for this contract
        $existing = ContractInsurance::where('contract_id', $contract->id)
            ->where('insurance_product_id', $validated['insurance_product_id'])
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Cette assurance est déjà active pour ce contrat.');
        }

        $product = InsuranceProduct::findOrFail($validated['insurance_product_id']);
        $commission = $product->monthly_price * ($product->commission_rate / 100);

        ContractInsurance::create([
            'contract_id' => $contract->id,
            'insurance_product_id' => $product->id,
            'monthly_premium' => $product->monthly_price,
            'commission_amount' => $commission,
            'coverage_amount' => $product->max_coverage_amount,
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'status' => 'active',
        ]);

        return redirect()->back()
            ->with('success', 'Assurance ajoutée avec succès au contrat.');
    }

    /**
     * Cancel an insurance on a contract.
     */
    public function cancelInsurance(Contract $contract, ContractInsurance $contractInsurance): RedirectResponse
    {
        // Verify the insurance belongs to this contract
        if ($contractInsurance->contract_id !== $contract->id) {
            abort(403, 'Cette assurance n\'appartient pas à ce contrat.');
        }

        $contractInsurance->cancel();

        return redirect()->back()
            ->with('success', 'Assurance annulée avec succès.');
    }
}
