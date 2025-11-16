<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Reservation;
use App\Models\Site;
use App\Notifications\ReservationCreatedNotification;
use App\Notifications\ReservationConfirmedNotification;
use App\Notifications\ReservationCancelledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Display the public box search page.
     */
    public function index(Request $request)
    {
        $query = Box::with(['site', 'currentContract'])
            ->where('status', 'available');

        // Filter by site
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Filter by minimum volume
        if ($request->filled('min_volume')) {
            $query->where('volume', '>=', $request->min_volume);
        }

        // Filter by maximum monthly price
        if ($request->filled('max_price')) {
            $query->where('monthly_price', '<=', $request->max_price);
        }

        // Filter by features
        if ($request->boolean('has_electricity')) {
            $query->where('has_electricity', true);
        }

        if ($request->boolean('is_climate_controlled')) {
            $query->where('is_climate_controlled', true);
        }

        if ($request->boolean('is_ground_floor')) {
            $query->where('is_ground_floor', true);
        }

        $boxes = $query->orderBy('monthly_price')->paginate(12);
        $sites = Site::orderBy('name')->get(['id', 'name', 'city']);

        return Inertia::render('Public/BoxSearch', [
            'boxes' => $boxes,
            'sites' => $sites,
            'filters' => $request->only(['site_id', 'min_volume', 'max_price', 'has_electricity', 'is_climate_controlled', 'is_ground_floor']),
        ]);
    }

    /**
     * Show the reservation form for a specific box.
     */
    public function create(Box $box)
    {
        // Verify box is available
        if ($box->status !== 'available') {
            return redirect()->route('reservations.index')
                ->with('error', 'This box is no longer available.');
        }

        $box->load('site');

        return Inertia::render('Public/ReservationForm', [
            'box' => $box,
        ]);
    }

    /**
     * Store a new reservation.
     */
    public function store(Request $request, Box $box)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'desired_start_date' => 'required|date|after_or_equal:today',
            'estimated_duration_months' => 'nullable|integer|min:1|max:120',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify box is still available
        if ($box->status !== 'available') {
            return back()->with('error', 'This box is no longer available.');
        }

        // Check for existing active reservation for this box
        $existingReservation = Reservation::where('box_id', $box->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('expires_at', '>', now())
            ->first();

        if ($existingReservation) {
            return back()->with('error', 'This box already has an active reservation.');
        }

        // Create reservation
        $reservation = Reservation::create([
            'tenant_id' => $box->site->tenant_id,
            'site_id' => $box->site_id,
            'box_id' => $box->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'desired_start_date' => $validated['desired_start_date'],
            'estimated_duration_months' => $validated['estimated_duration_months'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Send confirmation email to customer
        Notification::route('mail', $reservation->email)
            ->notify(new ReservationCreatedNotification($reservation));

        return redirect()->route('reservations.confirmation', $reservation)
            ->with('success', 'Your reservation has been submitted successfully!');
    }

    /**
     * Show the reservation confirmation page.
     */
    public function confirmation(Reservation $reservation)
    {
        $reservation->load(['box.site', 'box']);

        return Inertia::render('Public/ReservationConfirmation', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Admin: List all reservations.
     */
    public function adminIndex(Request $request)
    {
        $query = Reservation::with(['site', 'box', 'contract']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by site
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(20);
        $sites = Site::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => $reservations,
            'sites' => $sites,
            'filters' => $request->only(['status', 'site_id', 'search']),
        ]);
    }

    /**
     * Admin: Show a single reservation.
     */
    public function adminShow(Reservation $reservation)
    {
        $reservation->load(['site', 'box', 'contract']);

        return Inertia::render('Admin/Reservations/Show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Admin: Confirm a reservation.
     */
    public function confirm(Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be confirmed.');
        }

        $reservation->confirm();

        // Send confirmation email to customer
        Notification::route('mail', $reservation->email)
            ->notify(new ReservationConfirmedNotification($reservation));

        return back()->with('success', 'Reservation confirmed successfully.');
    }

    /**
     * Admin: Cancel a reservation.
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        if (in_array($reservation->status, ['converted', 'cancelled'])) {
            return back()->with('error', 'This reservation cannot be cancelled.');
        }

        $reason = $request->input('reason', '');

        $reservation->cancel();

        // Send cancellation email to customer
        Notification::route('mail', $reservation->email)
            ->notify(new ReservationCancelledNotification($reservation, $reason));

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    /**
     * Admin: Convert reservation to contract.
     */
    public function convertToContract(Reservation $reservation)
    {
        if ($reservation->status === 'converted') {
            return redirect()->route('contracts.show', $reservation->converted_to_contract_id)
                ->with('info', 'This reservation has already been converted to a contract.');
        }

        if ($reservation->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed reservations can be converted to contracts.');
        }

        // Redirect to contract creation with pre-filled data
        return redirect()->route('contracts.create', [
            'reservation_id' => $reservation->id,
            'box_id' => $reservation->box_id,
            'email' => $reservation->email,
        ])->with('info', 'Please complete the contract creation form.');
    }

    /**
     * Cron job: Expire old pending reservations.
     */
    public function expireOldReservations()
    {
        $expiredCount = Reservation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        return response()->json([
            'success' => true,
            'expired_count' => $expiredCount,
        ]);
    }
}
