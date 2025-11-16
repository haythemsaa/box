<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ClientPortalController extends Controller
{
    /**
     * Display the client portal dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get customer associated with this user (via email)
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$customer) {
            return Inertia::render('ClientPortal/NoAccount', [
                'message' => 'Aucun compte client trouvé pour cet email.'
            ]);
        }

        // Get customer's contracts with box information
        $activeContracts = Contract::with(['box.site', 'box.building', 'box.floor'])
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['active', 'pending'])
            ->get();

        // Get recent invoices
        $recentInvoices = Invoice::where('customer_id', $customer->id)
            ->orderBy('issue_date', 'desc')
            ->limit(5)
            ->get();

        // Get recent payments
        $recentPayments = Payment::where('customer_id', $customer->id)
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        // Calculate stats
        $totalContracts = Contract::where('customer_id', $customer->id)->count();
        $activeContractsCount = Contract::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->count();

        $unpaidInvoices = Invoice::where('customer_id', $customer->id)
            ->where('status', 'unpaid')
            ->sum('total_amount');

        $monthlyTotal = Contract::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->sum('monthly_amount');

        return Inertia::render('ClientPortal/Dashboard', [
            'customer' => $customer,
            'activeContracts' => $activeContracts,
            'recentInvoices' => $recentInvoices,
            'recentPayments' => $recentPayments,
            'stats' => [
                'totalContracts' => $totalContracts,
                'activeContracts' => $activeContractsCount,
                'unpaidInvoices' => $unpaidInvoices,
                'monthlyTotal' => $monthlyTotal,
            ],
        ]);
    }

    /**
     * Display all customer contracts.
     */
    public function contracts(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $query = Contract::with(['box.site', 'box.building', 'box.floor'])
            ->where('customer_id', $customer->id);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('contract_number', 'like', '%' . $request->search . '%');
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('ClientPortal/Contracts', [
            'customer' => $customer,
            'contracts' => $contracts,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display a specific contract.
     */
    public function showContract(Contract $contract)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        // Verify this contract belongs to the customer
        if ($contract->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this contract.');
        }

        $contract->load(['box.site', 'box.building', 'box.floor']);

        // Get invoices for this contract
        $invoices = Invoice::where('contract_id', $contract->id)
            ->orderBy('issue_date', 'desc')
            ->get();

        // Get payments for this contract
        $payments = Payment::where('contract_id', $contract->id)
            ->orderBy('payment_date', 'desc')
            ->get();

        return Inertia::render('ClientPortal/ContractDetail', [
            'customer' => $customer,
            'contract' => $contract,
            'invoices' => $invoices,
            'payments' => $payments,
        ]);
    }

    /**
     * Display all customer invoices.
     */
    public function invoices(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $query = Invoice::with('contract.box')
            ->where('customer_id', $customer->id);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $invoices = $query->orderBy('issue_date', 'desc')->paginate(15);

        // Calculate totals
        $totalUnpaid = Invoice::where('customer_id', $customer->id)
            ->where('status', 'unpaid')
            ->sum('total_amount');

        $totalPaid = Invoice::where('customer_id', $customer->id)
            ->where('status', 'paid')
            ->sum('total_amount');

        return Inertia::render('ClientPortal/Invoices', [
            'customer' => $customer,
            'invoices' => $invoices,
            'filters' => $request->only(['status', 'search']),
            'totals' => [
                'unpaid' => $totalUnpaid,
                'paid' => $totalPaid,
            ],
        ]);
    }

    /**
     * Display all customer payments.
     */
    public function payments(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $query = Payment::with(['contract.box', 'invoice'])
            ->where('customer_id', $customer->id);

        // Filter by payment method
        if ($request->has('method') && $request->method !== 'all') {
            $query->where('payment_method', $request->method);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('transaction_id', 'like', '%' . $request->search . '%');
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        // Calculate total
        $totalPaid = Payment::where('customer_id', $customer->id)
            ->sum('amount');

        return Inertia::render('ClientPortal/Payments', [
            'customer' => $customer,
            'payments' => $payments,
            'filters' => $request->only(['method', 'search']),
            'totalPaid' => $totalPaid,
        ]);
    }

    /**
     * Display customer profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        return Inertia::render('ClientPortal/Profile', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
        ]);

        $customer->update($validated);

        return redirect()->route('client.profile')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice(Invoice $invoice)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        // Verify this invoice belongs to the customer
        if ($invoice->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        // TODO: Generate PDF
        // For now, return JSON
        return response()->json([
            'message' => 'PDF generation coming soon',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Download contract PDF.
     */
    public function downloadContract(Contract $contract)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        // Verify this contract belongs to the customer
        if ($contract->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this contract.');
        }

        // TODO: Generate PDF
        // For now, return JSON
        return response()->json([
            'message' => 'PDF generation coming soon',
            'contract' => $contract,
        ]);
    }
}
