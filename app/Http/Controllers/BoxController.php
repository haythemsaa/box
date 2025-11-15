<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Site;
use App\Models\Floor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Box::with(['floor.building.site']);

        // Apply filters
        if ($request->filled('site_id')) {
            $query->whereHas('floor.building.site', function ($q) use ($request) {
                $q->where('id', $request->site_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $boxes = $query->paginate(20)
            ->through(function ($box) {
                return [
                    'id' => $box->id,
                    'number' => $box->number,
                    'length' => $box->length,
                    'width' => $box->width,
                    'height' => $box->height,
                    'volume' => $box->volume,
                    'area' => $box->area,
                    'monthly_price' => $box->monthly_price,
                    'status' => $box->status,
                    'type' => $box->type,
                    'features' => $box->features,
                    'site_name' => $box->floor->building->site->name,
                    'floor_label' => $box->floor->building->name . ' - ' . $box->floor->name,
                ];
            });

        // Get sites for filters
        $sites = Site::select('id', 'name')->get();

        return Inertia::render('Boxes/Index', [
            'boxes' => $boxes,
            'filters' => [
                'sites' => $sites,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $sites = Site::with('buildings.floors')->get();

        return Inertia::render('Boxes/Create', [
            'sites' => $sites,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'number' => 'required|string|max:50',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'monthly_price' => 'required|numeric|min:0',
            'type' => 'required|in:standard,climat,premium',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'features' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        Box::create($validated);

        return redirect()->route('boxes.index')
            ->with('success', 'Box créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box): Response
    {
        $box->load(['floor.building.site', 'contracts.customer']);

        return Inertia::render('Boxes/Show', [
            'box' => $box,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Box $box): Response
    {
        $sites = Site::with('buildings.floors')->get();

        return Inertia::render('Boxes/Edit', [
            'box' => $box,
            'sites' => $sites,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Box $box): RedirectResponse
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'number' => 'required|string|max:50',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'monthly_price' => 'required|numeric|min:0',
            'type' => 'required|in:standard,climat,premium',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'features' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        $box->update($validated);

        return redirect()->route('boxes.index')
            ->with('success', 'Box mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Box $box): RedirectResponse
    {
        $box->delete();

        return redirect()->route('boxes.index')
            ->with('success', 'Box supprimée avec succès.');
    }
}
