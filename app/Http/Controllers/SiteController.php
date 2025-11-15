<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $sites = Site::withCount(['boxes', 'boxes as available_boxes_count' => function ($query) {
                $query->where('status', 'available');
            }])
            ->paginate(12)
            ->through(function ($site) {
                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'code' => $site->code,
                    'address' => $site->address,
                    'postal_code' => $site->postal_code,
                    'city' => $site->city,
                    'country' => $site->country,
                    'status' => $site->status,
                    'boxes_count' => $site->boxes_count,
                    'available_boxes_count' => $site->available_boxes_count,
                    'occupancy_rate' => $site->boxes_count > 0
                        ? round((($site->boxes_count - $site->available_boxes_count) / $site->boxes_count) * 100)
                        : 0,
                ];
            });

        return Inertia::render('Sites/Index', [
            'sites' => $sites,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Sites/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:sites',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site): Response
    {
        $site->load(['buildings.floors.boxes']);

        return Inertia::render('Sites/Show', [
            'site' => $site,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site): Response
    {
        return Inertia::render('Sites/Edit', [
            'site' => $site,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:sites,code,' . $site->id,
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $site->update($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site): RedirectResponse
    {
        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé avec succès.');
    }
}
