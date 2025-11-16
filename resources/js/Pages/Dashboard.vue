<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import EmptyState from '@/Components/EmptyState.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import KpiCard from '@/Components/KpiCard.vue';

const props = defineProps({
    stats: Object,
    revenueChartData: Array,
    occupationData: Object,
    topInsurances: Array,
    recentContracts: Array,
});

// Prepare revenue chart data
const revenueChart = computed(() => ({
    labels: props.revenueChartData.map(d => d.month),
    datasets: [
        {
            label: 'Chiffre d\'affaires mensuel',
            data: props.revenueChartData.map(d => d.revenue),
            borderColor: '#0ea5e9',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            fill: true,
            tension: 0.4,
        }
    ]
}));

// Prepare occupation chart data
const occupationChart = computed(() => ({
    labels: ['Boxes occupés', 'Boxes disponibles'],
    datasets: [
        {
            data: [props.occupationData.occupied, props.occupationData.available],
            backgroundColor: ['#0ea5e9', '#e5e7eb'],
            borderWidth: 0,
        }
    ]
}));

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};

// Format number
const formatNumber = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value);
};

// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Tableau de bord
                        <span class="text-base font-normal text-gray-500 ml-2">Vue globale de l'activité</span>
                    </h1>
                    <button
                        @click="$inertia.reload()"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Réinitialiser le tableau de bord
                    </button>
                </div>
            </div>
        </div>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Quick Navigation -->
                <div class="mb-8">
                    <div class="flex flex-wrap gap-3">
                        <Link
                            href="/prospects"
                            class="group inline-flex items-center px-6 py-3 bg-yellow-100 hover:bg-yellow-200 rounded-full font-semibold text-gray-800 transition-all duration-200 hover:scale-105 hover:shadow-md"
                        >
                            <span class="text-lg mr-2">→</span>
                            Prospects
                        </Link>
                        <Link
                            href="/customers"
                            class="group inline-flex items-center px-6 py-3 bg-cyan-100 hover:bg-cyan-200 rounded-full font-semibold text-gray-800 transition-all duration-200 hover:scale-105 hover:shadow-md"
                        >
                            <span class="text-lg mr-2">→</span>
                            Clients
                            <span class="ml-2 px-2 py-1 text-xs bg-white rounded-full">{{ stats.total_customers }}</span>
                        </Link>
                        <Link
                            href="/contracts"
                            class="group inline-flex items-center px-6 py-3 bg-orange-100 hover:bg-orange-200 rounded-full font-semibold text-gray-800 transition-all duration-200 hover:scale-105 hover:shadow-md"
                        >
                            <span class="text-lg mr-2">→</span>
                            Contrats
                            <span class="ml-2 px-2 py-1 text-xs bg-white rounded-full">{{ stats.active_contracts }}</span>
                        </Link>
                        <Link
                            href="/boxes"
                            class="group inline-flex items-center px-6 py-3 bg-green-100 hover:bg-green-200 rounded-full font-semibold text-gray-800 transition-all duration-200 hover:scale-105 hover:shadow-md"
                        >
                            <span class="text-lg mr-2">→</span>
                            Plan des boxes
                            <span class="ml-2 px-2 py-1 text-xs bg-white rounded-full">{{ stats.total_boxes }}</span>
                        </Link>
                        <Link
                            href="/disponibilites"
                            class="group inline-flex items-center px-6 py-3 bg-indigo-100 hover:bg-indigo-200 rounded-full font-semibold text-gray-800 transition-all duration-200 hover:scale-105 hover:shadow-md"
                        >
                            <span class="text-lg mr-2">→</span>
                            Mes dispo
                        </Link>
                        <Link
                            href="/signatures"
                            class="group inline-flex items-center px-6 py-3 bg-cyan-100 hover:bg-cyan-200 rounded-full font-semibold text-gray-800 transition-all duration-200 hover:scale-105 hover:shadow-md"
                        >
                            <span class="text-lg mr-2">→</span>
                            Signatures
                        </Link>
                        <Link
                            href="/mandats"
                            class="group inline-flex items-center px-6 py-3 bg-teal-100 hover:bg-teal-200 rounded-full font-semibold text-gray-800 transition-all duration-200 hover:scale-105 hover:shadow-md"
                        >
                            <span class="text-lg mr-2">→</span>
                            Mandats
                        </Link>
                    </div>
                </div>

                <!-- KPI Cards -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistiques d'occupation</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-6 mb-8">
                    <KpiCard :value="stats.total_boxes" label="Nb de box" color="cyan" />
                    <KpiCard :value="stats.available_boxes" label="Nb de box sans contrat" color="cyan" />
                    <KpiCard :value="`${stats.occupation_percentage}%`" label="% d'occup. en nombre" color="teal" />
                    <KpiCard :value="stats.active_contracts" label="Nb de contrat" color="cyan" />
                    <KpiCard :value="stats.total_customers" label="Clients Actifs" color="cyan" />
                    <KpiCard :value="formatNumber(stats.total_surface)" label="Surface totale en m²" color="teal" />
                </div>

                <!-- Second Row KPIs -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-6 mb-8">
                    <KpiCard :value="formatNumber(stats.total_volume)" label="Volume total en m³" color="cyan" />
                    <KpiCard value="0.00%" label="Surface occupée" color="cyan" />
                    <KpiCard :value="`${stats.occupation_percentage}%`" label="% d'occup. en nombre" color="teal" />
                    <KpiCard :value="formatCurrency(0)" label="CA théorique HT mensuel" color="cyan" />
                    <KpiCard :value="formatCurrency(stats.insurance_monthly_revenue)" label="Montant assuré" color="cyan" />
                    <KpiCard :value="formatCurrency(0)" label="CA théo. HT pour Occup. max" color="teal" />
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Revenue Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution du chiffre d'affaires</h3>
                        <div class="h-80">
                            <LineChart :data="revenueChart" />
                        </div>
                    </div>

                    <!-- Occupation Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux d'occupation des boxes</h3>
                        <div class="h-80 flex items-center justify-center">
                            <div class="w-64 h-64 relative">
                                <DoughnutChart :data="occupationChart" />
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-4xl font-bold text-cyan-600">{{ stats.occupation_percentage }}%</div>
                                        <div class="text-sm text-gray-500">Occupé</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Résumé du mois en cours -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 uppercase text-cyan-600">Résumé du mois en cours</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Chiffre d'affaires TTC</div>
                            <div class="text-3xl font-bold text-red-600">{{ formatCurrency(0) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 mb-1">31 Factures</div>
                            <div class="text-3xl font-bold text-cyan-600">{{ formatCurrency(0) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 mb-1">0 avoirs</div>
                            <div class="text-3xl font-bold text-cyan-600">{{ formatCurrency(0) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 mb-1">5 encaissements</div>
                            <div class="text-3xl font-bold text-cyan-600">{{ formatCurrency(0) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Top Insurances & Recent Contracts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Insurances -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Assurances</h3>
                        <div v-if="topInsurances.length > 0" class="space-y-3">
                            <div
                                v-for="insurance in topInsurances"
                                :key="insurance.name"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150"
                            >
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ insurance.name }}</div>
                                    <div class="text-sm text-gray-500">{{ insurance.subscriptions }} souscription(s)</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-cyan-600">{{ formatCurrency(insurance.monthly_revenue) }}</div>
                                    <div class="text-xs text-gray-500">/ mois</div>
                                </div>
                            </div>
                        </div>
                        <EmptyState
                            v-else
                            icon="shield"
                            title="Aucune assurance souscrite"
                            description="Proposez des assurances à vos clients"
                            action-text="Voir les produits"
                            action-href="/insurance-products"
                        />
                    </div>

                    <!-- Recent Contracts -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contrats récents</h3>
                        <div v-if="recentContracts.length > 0" class="space-y-3">
                            <div
                                v-for="contract in recentContracts"
                                :key="contract.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150"
                            >
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ contract.customer_name }}</div>
                                    <div class="text-sm text-gray-500">{{ contract.contract_number }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-cyan-600">{{ formatCurrency(contract.monthly_rent) }}</div>
                                    <StatusBadge :status="contract.status" type="contract" />
                                </div>
                            </div>
                        </div>
                        <EmptyState
                            v-else
                            icon="document"
                            title="Aucun contrat récent"
                            description="Créez votre premier contrat"
                            action-text="Créer un contrat"
                            action-href="/contracts/create"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
