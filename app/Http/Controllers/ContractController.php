<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Box;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

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

        return Inertia::render('Contracts/Create', [
            'customers' => $customers,
            'availableBoxes' => $availableBoxes,
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
        ]);

        // Get tenant_id from customer
        $customer = Customer::findOrFail($validated['customer_id']);
        $validated['tenant_id'] = $customer->tenant_id;

        $contract = Contract::create($validated);

        // Update box status to occupied if contract is active
        if ($validated['status'] === 'active') {
            Box::find($validated['box_id'])->update(['status' => 'occupied']);
        }

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
            'invoices' => function ($query) {
                $query->latest()->limit(10);
            },
            'payments' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);

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
}
