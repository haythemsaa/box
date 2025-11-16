<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Customer::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount('contracts')
            ->latest()
            ->paginate(15)
            ->through(function ($customer) {
                return [
                    'id' => $customer->id,
                    'type' => $customer->type,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'company_name' => $customer->company_name,
                    'city' => $customer->city,
                    'country' => $customer->country,
                    'status' => $customer->status,
                    'contracts_count' => $customer->contracts_count,
                    'created_at' => $customer->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['type', 'status', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'type' => 'required|in:individual,company',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            'status' => 'required|in:active,inactive,suspended',
        ];

        // Rules for individual customers
        if ($request->type === 'individual') {
            $rules = array_merge($rules, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'date_of_birth' => 'nullable|date',
            ]);
        }

        // Rules for company customers
        if ($request->type === 'company') {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
                'siret' => 'nullable|string|max:50',
                'vat_number' => 'nullable|string|max:50',
            ]);
        }

        $validated = $request->validate($rules);

        // Set name based on type
        if ($request->type === 'individual') {
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        } else {
            $validated['name'] = $validated['company_name'];
        }

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): Response
    {
        $customer->load(['contracts.box.floor.building.site']);

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): Response
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $rules = [
            'type' => 'required|in:individual,company',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            'status' => 'required|in:active,inactive,suspended',
        ];

        // Rules for individual customers
        if ($request->type === 'individual') {
            $rules = array_merge($rules, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'date_of_birth' => 'nullable|date',
            ]);
        }

        // Rules for company customers
        if ($request->type === 'company') {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
                'siret' => 'nullable|string|max:50',
                'vat_number' => 'nullable|string|max:50',
            ]);
        }

        $validated = $request->validate($rules);

        // Set name based on type
        if ($request->type === 'individual') {
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        } else {
            $validated['name'] = $validated['company_name'];
        }

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
