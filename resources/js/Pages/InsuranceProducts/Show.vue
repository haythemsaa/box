<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link
                        :href="route('insurance-products.index')"
                        class="text-sm text-gray-600 hover:text-gray-900 mb-4 inline-flex items-center"
                    >
                        ← Retour aux produits d'assurance
                    </Link>
                    <div class="mt-2 flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900">{{ insuranceProduct.name }}</h2>
                            <p class="mt-1 text-sm text-gray-600" v-if="insuranceProduct.description">
                                {{ insuranceProduct.description }}
                            </p>
                            <div class="mt-2 flex gap-2">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        insuranceProduct.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ insuranceProduct.is_active ? 'Actif' : 'Inactif' }}
                                </span>
                                <span
                                    v-if="insuranceProduct.is_mandatory"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                >
                                    Obligatoire
                                </span>
                                <span
                                    v-if="!insuranceProduct.tenant_id"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                                >
                                    Global
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                :href="route('insurance-products.edit', insuranceProduct.id)"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
                            >
                                Modifier
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Souscriptions totales</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ stats.total_subscriptions }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Souscriptions actives</div>
                        <div class="mt-2 text-3xl font-semibold text-green-600">{{ stats.active_subscriptions }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Primes totales</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ formatCurrency(stats.total_premium_revenue) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Commissions gagnées</div>
                        <div class="mt-2 text-3xl font-semibold text-indigo-600">{{ formatCurrency(stats.total_commission_earned) }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Product Details -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Détails du produit</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Couverture maximale</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ formatCurrency(insuranceProduct.max_coverage_amount) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Prix mensuel</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ formatCurrency(insuranceProduct.monthly_price) }}/mois</dd>
                                </div>
                                <div v-if="insuranceProduct.yearly_price">
                                    <dt class="text-sm font-medium text-gray-500">Prix annuel</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-semibold">
                                        {{ formatCurrency(insuranceProduct.yearly_price) }}/an
                                        <span class="ml-2 text-green-600 text-xs">(économie de {{ formatCurrency(yearlySavings) }})</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Taux de commission</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ insuranceProduct.commission_rate }}%
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Coverage Details -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Garanties et exclusions</h3>
                            <div class="space-y-4">
                                <div v-if="insuranceProduct.coverage_details">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">✓ Garanties incluses</dt>
                                    <dd class="text-sm text-gray-900 whitespace-pre-line bg-green-50 p-3 rounded">{{ insuranceProduct.coverage_details }}</dd>
                                </div>
                                <div v-if="insuranceProduct.exclusions">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">✗ Exclusions</dt>
                                    <dd class="text-sm text-gray-900 whitespace-pre-line bg-red-50 p-3 rounded">{{ insuranceProduct.exclusions }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Subscriptions -->
                <div class="mt-6 bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Souscriptions récentes</h3>
                        <div v-if="recentSubscriptions.length > 0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Client
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Contrat
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Box
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Prime mensuelle
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date de début
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="subscription in recentSubscriptions" :key="subscription.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ subscription.contract.customer.first_name }} {{ subscription.contract.customer.last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ subscription.contract.customer.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link
                                                :href="route('contracts.show', subscription.contract.id)"
                                                class="text-sm text-indigo-600 hover:text-indigo-900"
                                            >
                                                {{ subscription.contract.contract_number }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ subscription.contract.box?.box_number || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatCurrency(subscription.monthly_premium) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    subscription.status === 'active'
                                                        ? 'bg-green-100 text-green-800'
                                                        : subscription.status === 'cancelled'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-gray-100 text-gray-800'
                                                ]"
                                            >
                                                {{ getStatusLabel(subscription.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(subscription.start_date) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            Aucune souscription pour ce produit
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    insuranceProduct: Object,
    stats: Object,
    recentSubscriptions: Array,
});

const yearlySavings = computed(() => {
    if (!props.insuranceProduct.yearly_price) return 0;
    return (props.insuranceProduct.monthly_price * 12) - props.insuranceProduct.yearly_price;
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getStatusLabel = (status) => {
    const labels = {
        active: 'Actif',
        cancelled: 'Annulé',
        expired: 'Expiré',
    };
    return labels[status] || status;
};
</script>
