<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Produits d'Assurance</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Gérez le catalogue de produits d'assurance pour vos contrats
                        </p>
                    </div>
                    <Link
                        :href="route('insurance-products.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        + Nouveau Produit
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Recherche
                            </label>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Nom, description..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @input="debouncedSearch"
                            />
                        </div>

                        <!-- Active Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Statut
                            </label>
                            <select
                                v-model="filters.is_active"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @change="applyFilters"
                            >
                                <option value="">Tous</option>
                                <option value="1">Actif</option>
                                <option value="0">Inactif</option>
                            </select>
                        </div>

                        <!-- Mandatory Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Type
                            </label>
                            <select
                                v-model="filters.is_mandatory"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @change="applyFilters"
                            >
                                <option value="">Tous</option>
                                <option value="1">Obligatoire</option>
                                <option value="0">Optionnel</option>
                            </select>
                        </div>

                        <!-- Tenant Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Portée
                            </label>
                            <select
                                v-model="filters.tenant_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @change="applyFilters"
                            >
                                <option value="">Tous</option>
                                <option value="global">Produits globaux</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produit
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Couverture Max
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Commission
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in insuranceProducts.data" :key="product.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ product.name }}</div>
                                            <div class="text-sm text-gray-500 mt-1" v-if="product.description">
                                                {{ truncateText(product.description, 80) }}
                                            </div>
                                            <div class="mt-1 flex gap-2">
                                                <span
                                                    v-if="product.is_mandatory"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"
                                                >
                                                    Obligatoire
                                                </span>
                                                <span
                                                    v-if="!product.tenant_id"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800"
                                                >
                                                    Global
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ formatCurrency(product.max_coverage_amount) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium">{{ formatCurrency(product.monthly_price) }}/mois</div>
                                        <div v-if="product.yearly_price" class="text-gray-500">
                                            {{ formatCurrency(product.yearly_price) }}/an
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ product.commission_rate }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            product.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ product.is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('insurance-products.show', product.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4"
                                    >
                                        Voir
                                    </Link>
                                    <Link
                                        :href="route('insurance-products.edit', product.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4"
                                    >
                                        Modifier
                                    </Link>
                                    <button
                                        @click="confirmDelete(product)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Empty State -->
                    <div v-if="insuranceProducts.data.length === 0" class="text-center py-12">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit d'assurance</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau produit.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('insurance-products.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                            >
                                + Nouveau Produit
                            </Link>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="insuranceProducts.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="insuranceProducts.prev_page_url"
                                    :href="insuranceProducts.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Précédent
                                </Link>
                                <Link
                                    v-if="insuranceProducts.next_page_url"
                                    :href="insuranceProducts.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Suivant
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Affichage de
                                        <span class="font-medium">{{ insuranceProducts.from }}</span>
                                        à
                                        <span class="font-medium">{{ insuranceProducts.to }}</span>
                                        sur
                                        <span class="font-medium">{{ insuranceProducts.total }}</span>
                                        résultats
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-for="link in insuranceProducts.links"
                                            :key="link.label"
                                            :href="link.url"
                                            :class="[
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                link.active
                                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                !link.url ? 'cursor-not-allowed opacity-50' : ''
                                            ]"
                                            v-html="link.label"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    insuranceProducts: Object,
    filters: Object,
});

const filters = reactive({
    search: props.filters.search || '',
    is_active: props.filters.is_active || '',
    is_mandatory: props.filters.is_mandatory || '',
    tenant_id: props.filters.tenant_id || '',
});

let debounceTimeout = null;

const debouncedSearch = () => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

const applyFilters = () => {
    router.get(route('insurance-products.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const truncateText = (text, maxLength) => {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const confirmDelete = (product) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${product.name}" ?`)) {
        router.delete(route('insurance-products.destroy', product.id), {
            preserveScroll: true,
        });
    }
};
</script>
