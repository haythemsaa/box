<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Box;
use App\Models\Contract;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): Response
    {
        // Get statistics
        $stats = [
            'total_sites' => Site::count(),
            'total_boxes' => Box::count(),
            'available_boxes' => Box::where('status', 'available')->count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
        ];

        // Get recent contracts with customer names
        $recentContracts = Contract::with('customer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'customer_name' => $contract->customer->name,
                    'status' => $contract->status,
                    'created_at' => $contract->created_at->toISOString(),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentContracts' => $recentContracts,
        ]);
    }
}
