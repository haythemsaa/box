<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Box;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\ContractInsurance;
use App\Models\InsuranceProduct;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): Response
    {
        // Get basic statistics
        $totalBoxes = Box::count();
        $availableBoxes = Box::where('status', 'available')->count();
        $occupiedBoxes = $totalBoxes - $availableBoxes;
        $activeContracts = Contract::where('status', 'active')->count();
        $totalCustomers = Customer::count();

        // Calculate occupation percentage
        $occupationPercentage = $totalBoxes > 0
            ? round(($occupiedBoxes / $totalBoxes) * 100, 2)
            : 0;

        // Get insurance statistics
        $activeInsurances = ContractInsurance::where('status', 'active')->count();
        $totalInsuranceRevenue = ContractInsurance::where('status', 'active')
            ->sum('monthly_premium');

        $stats = [
            'total_sites' => Site::count(),
            'total_boxes' => $totalBoxes,
            'available_boxes' => $availableBoxes,
            'occupied_boxes' => $occupiedBoxes,
            'occupation_percentage' => $occupationPercentage,
            'active_contracts' => $activeContracts,
            'total_customers' => $totalCustomers,
            'active_insurances' => $activeInsurances,
            'insurance_monthly_revenue' => $totalInsuranceRevenue,
        ];

        // Calculate total volume and surface
        $boxStats = Box::select(
            DB::raw('SUM(volume) as total_volume'),
            DB::raw('SUM(surface) as total_surface')
        )->first();

        $stats['total_volume'] = $boxStats->total_volume ?? 0;
        $stats['total_surface'] = $boxStats->total_surface ?? 0;

        // Get monthly revenue for the last 6 months
        $monthlyRevenue = Contract::where('status', 'active')
            ->select(
                DB::raw('DATE_FORMAT(start_date, "%Y-%m") as month'),
                DB::raw('SUM(monthly_rent) as revenue')
            )
            ->where('start_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare data for revenue chart (last 6 months)
        $revenueChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthLabel = now()->subMonths($i)->format('M Y');
            $revenue = $monthlyRevenue->firstWhere('month', $month);
            $revenueChartData[] = [
                'month' => $monthLabel,
                'revenue' => $revenue ? (float) $revenue->revenue : 0
            ];
        }

        // Get occupation data for doughnut chart
        $occupationData = [
            'occupied' => $occupiedBoxes,
            'available' => $availableBoxes,
        ];

        // Get top insurance products
        $topInsurances = InsuranceProduct::withCount(['contractInsurances' => function($query) {
            $query->where('status', 'active');
        }])
            ->where('is_active', true)
            ->orderByDesc('contract_insurances_count')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'subscriptions' => $product->contract_insurances_count,
                    'monthly_revenue' => $product->contract_insurances_count * $product->monthly_price,
                ];
            });

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
                    'monthly_rent' => $contract->monthly_rent,
                    'created_at' => $contract->created_at->toISOString(),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'revenueChartData' => $revenueChartData,
            'occupationData' => $occupationData,
            'topInsurances' => $topInsurances,
            'recentContracts' => $recentContracts,
        ]);
    }
}
