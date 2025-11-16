<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    Contrats
                </h2>
                <Link href="/contracts/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau contrat
                </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input
                        v-model="search"
                        @input="applyFilters"
                        type="text"
                        placeholder="Numéro de contrat, nom du client..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select
                        v-model="statusFilter"
                        @change="applyFilters"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Tous</option>
                        <option value="draft">Brouillon</option>
                        <option value="pending">En attente</option>
                        <option value="active">Actif</option>
                        <option value="expired">Expiré</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Contracts Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div v-if="contracts.data.length > 0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contrat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Box</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="contract in contracts.data" :key="contract.id" class="hover:bg-gray-50">
                            <!-- Contract Number -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ contract.contract_number }}</div>
                                    <div class="text-xs text-gray-400">{{ contract.created_at }}</div>
                                </div>
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ contract.customer_name }}</div>
                            </td>

                            <!-- Box -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ contract.box_number }}</div>
                            </td>

                            <!-- Site -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ contract.site_name }}</div>
                            </td>

                            <!-- Period -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ contract.start_date }}</div>
                                <div class="text-xs text-gray-500">au {{ contract.end_date }}</div>
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ formatCurrency(contract.monthly_amount) }}</div>
                                <div class="text-xs text-gray-500">/ mois</div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <StatusBadge :status="contract.status" type="contract" size="small" />
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="`/contracts/${contract.id}`" class="text-gray-600 hover:text-gray-900 mr-3">
                                    Voir
                                </Link>
                                <Link :href="`/contracts/${contract.id}/edit`" class="text-indigo-600 hover:text-indigo-900">
                                    Modifier
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ contracts.from }}</span> à <span class="font-medium">{{ contracts.to }}</span> sur <span class="font-medium">{{ contracts.total }}</span> contrats
                        </div>
                        <div class="flex gap-2">
                            <Link v-if="contracts.prev_page_url" :href="contracts.prev_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Précédent
                            </Link>
                            <Link v-if="contracts.next_page_url" :href="contracts.next_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Suivant
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <EmptyState
                v-else
                icon="document"
                title="Aucun contrat"
                description="Commencez par créer votre premier contrat."
                action-text="Créer un contrat"
                action-href="/contracts/create"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps({
    contracts: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const applyFilters = () => {
    router.get('/contracts', {
        search: search.value,
        status: statusFilter.value,
    }, {
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
</script>
