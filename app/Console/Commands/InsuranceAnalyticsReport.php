<?php

namespace App\Console\Commands;

use App\Models\InsuranceProduct;
use App\Models\ContractInsurance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsuranceAnalyticsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insurance:analytics
                            {--product= : Specific product ID to analyze}
                            {--period= : Period in months (default: 12)}
                            {--export= : Export to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate analytics report for insurance products performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->option('product');
        $period = $this->option('period') ?? 12;
        $exportFile = $this->option('export');

        $this->info("🛡️  Insurance Products Analytics Report");
        $this->info("📅 Period: Last {$period} months");
        $this->newLine();

        // Get products
        $query = InsuranceProduct::query();
        if ($productId) {
            $query->where('id', $productId);
        }
        $products = $query->with('contractInsurances')->get();

        if ($products->isEmpty()) {
            $this->error('No insurance products found');
            return Command::FAILURE;
        }

        $reportData = [];
        $totalRevenue = 0;
        $totalCommissions = 0;
        $totalActiveSubscriptions = 0;

        foreach ($products as $product) {
            $stats = $this->calculateProductStats($product, $period);
            $reportData[] = $stats;
            $totalRevenue += $stats['total_premium'];
            $totalCommissions += $stats['total_commission'];
            $totalActiveSubscriptions += $stats['active_subscriptions'];
        }

        // Display summary
        $this->displaySummary($reportData);

        // Display totals
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 GLOBAL TOTALS');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Active Subscriptions', number_format($totalActiveSubscriptions)],
                ['Total Premium Revenue', number_format($totalRevenue, 2) . ' €'],
                ['Total Commissions Earned', number_format($totalCommissions, 2) . ' €'],
                ['Commission %', $totalRevenue > 0 ? number_format(($totalCommissions / $totalRevenue) * 100, 2) . '%' : 'N/A'],
            ]
        );

        // Export to CSV if requested
        if ($exportFile) {
            $this->exportToCSV($reportData, $exportFile);
            $this->info("✓ Report exported to: {$exportFile}");
        }

        // Performance insights
        $this->displayInsights($reportData);

        return Command::SUCCESS;
    }

    /**
     * Calculate statistics for a product.
     */
    private function calculateProductStats(InsuranceProduct $product, int $period): array
    {
        $cutoffDate = now()->subMonths($period);

        // Active subscriptions
        $activeSubscriptions = $product->contractInsurances()
            ->active()
            ->count();

        // All subscriptions (including cancelled/expired)
        $totalSubscriptions = $product->contractInsurances()->count();

        // Subscriptions in period
        $newSubscriptions = $product->contractInsurances()
            ->where('start_date', '>=', $cutoffDate)
            ->count();

        // Cancelled in period
        $cancelledSubscriptions = $product->contractInsurances()
            ->where('status', 'cancelled')
            ->where('cancelled_at', '>=', $cutoffDate)
            ->count();

        // Calculate revenue and commissions
        $totalPremium = 0;
        $totalCommission = 0;

        foreach ($product->contractInsurances()->active()->get() as $contractInsurance) {
            $totalPremium += $contractInsurance->getTotalPremiumPaid();
            $totalCommission += $contractInsurance->getTotalCommissionEarned();
        }

        // Calculate retention rate
        $retentionRate = $totalSubscriptions > 0
            ? (($totalSubscriptions - $cancelledSubscriptions) / $totalSubscriptions) * 100
            : 0;

        // Calculate average monthly premium
        $avgMonthlyPremium = $activeSubscriptions > 0
            ? $product->contractInsurances()->active()->avg('monthly_premium')
            : 0;

        return [
            'product_name' => $product->name,
            'product_id' => $product->id,
            'is_active' => $product->is_active,
            'is_mandatory' => $product->is_mandatory,
            'monthly_price' => (float) $product->monthly_price,
            'commission_rate' => (float) $product->commission_rate,
            'active_subscriptions' => $activeSubscriptions,
            'total_subscriptions' => $totalSubscriptions,
            'new_subscriptions' => $newSubscriptions,
            'cancelled_subscriptions' => $cancelledSubscriptions,
            'retention_rate' => $retentionRate,
            'total_premium' => $totalPremium,
            'total_commission' => $totalCommission,
            'avg_monthly_premium' => (float) $avgMonthlyPremium,
            'max_coverage' => (float) $product->max_coverage_amount,
        ];
    }

    /**
     * Display summary table.
     */
    private function displaySummary(array $reportData): void
    {
        $this->table(
            ['Product', 'Active', 'New', 'Cancelled', 'Retention %', 'Premium Revenue', 'Commissions', 'Avg Monthly'],
            array_map(function ($stats) {
                return [
                    $stats['product_name'] . ($stats['is_mandatory'] ? ' *' : ''),
                    $stats['active_subscriptions'],
                    $stats['new_subscriptions'],
                    $stats['cancelled_subscriptions'],
                    number_format($stats['retention_rate'], 1) . '%',
                    number_format($stats['total_premium'], 2) . ' €',
                    number_format($stats['total_commission'], 2) . ' €',
                    number_format($stats['avg_monthly_premium'], 2) . ' €',
                ];
            }, $reportData)
        );

        $this->line('* Mandatory product');
    }

    /**
     * Display performance insights.
     */
    private function displayInsights(array $reportData): void
    {
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('💡 INSIGHTS');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Best performing product by revenue
        $bestRevenue = collect($reportData)->sortByDesc('total_premium')->first();
        if ($bestRevenue) {
            $this->line("🏆 Best Revenue: {$bestRevenue['product_name']} (" . number_format($bestRevenue['total_premium'], 2) . " €)");
        }

        // Best performing product by subscriptions
        $bestSubs = collect($reportData)->sortByDesc('active_subscriptions')->first();
        if ($bestSubs && $bestSubs['product_id'] !== $bestRevenue['product_id']) {
            $this->line("👥 Most Subscriptions: {$bestSubs['product_name']} ({$bestSubs['active_subscriptions']} active)");
        }

        // Highest retention
        $bestRetention = collect($reportData)->sortByDesc('retention_rate')->first();
        if ($bestRetention) {
            $this->line("🔒 Best Retention: {$bestRetention['product_name']} (" . number_format($bestRetention['retention_rate'], 1) . "%)");
        }

        // Products needing attention
        $lowPerformers = collect($reportData)->filter(function ($stats) {
            return $stats['retention_rate'] < 80 && $stats['total_subscriptions'] > 5;
        });

        if ($lowPerformers->isNotEmpty()) {
            $this->newLine();
            $this->warn('⚠️  Products needing attention (low retention):');
            foreach ($lowPerformers as $product) {
                $this->line("   - {$product['product_name']}: " . number_format($product['retention_rate'], 1) . "% retention");
            }
        }

        // Inactive products with subscriptions
        $inactiveWithSubs = collect($reportData)->filter(function ($stats) {
            return !$stats['is_active'] && $stats['active_subscriptions'] > 0;
        });

        if ($inactiveWithSubs->isNotEmpty()) {
            $this->newLine();
            $this->warn('⚠️  Inactive products still have active subscriptions:');
            foreach ($inactiveWithSubs as $product) {
                $this->line("   - {$product['product_name']}: {$product['active_subscriptions']} active subscriptions");
            }
        }
    }

    /**
     * Export report to CSV.
     */
    private function exportToCSV(array $reportData, string $filename): void
    {
        $fp = fopen($filename, 'w');

        // Headers
        fputcsv($fp, [
            'Product Name',
            'Product ID',
            'Is Active',
            'Is Mandatory',
            'Monthly Price',
            'Commission Rate %',
            'Active Subscriptions',
            'Total Subscriptions',
            'New Subscriptions',
            'Cancelled Subscriptions',
            'Retention Rate %',
            'Total Premium Revenue',
            'Total Commissions',
            'Average Monthly Premium',
            'Max Coverage',
        ]);

        // Data
        foreach ($reportData as $stats) {
            fputcsv($fp, [
                $stats['product_name'],
                $stats['product_id'],
                $stats['is_active'] ? 'Yes' : 'No',
                $stats['is_mandatory'] ? 'Yes' : 'No',
                $stats['monthly_price'],
                $stats['commission_rate'],
                $stats['active_subscriptions'],
                $stats['total_subscriptions'],
                $stats['new_subscriptions'],
                $stats['cancelled_subscriptions'],
                number_format($stats['retention_rate'], 2),
                number_format($stats['total_premium'], 2),
                number_format($stats['total_commission'], 2),
                number_format($stats['avg_monthly_premium'], 2),
                $stats['max_coverage'],
            ]);
        }

        fclose($fp);
    }
}
