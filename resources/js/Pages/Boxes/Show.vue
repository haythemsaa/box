<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        Box {{ box.number }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ box.floor.building.site.name }} - {{ box.floor.building.name }} - {{ box.floor.name }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link :href="`/boxes/${box.id}/edit`" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                        Modifier
                    </Link>
                    <Link href="/boxes" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Status Badge -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Statut du box</h3>
                        <p class="mt-1 text-sm text-gray-500">Disponibilité actuelle</p>
                    </div>
                    <StatusBadge :status="box.status" type="box" size="large" />
                </div>
            </div>

            <!-- Box Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Caractéristiques</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Numéro</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ box.number }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Volume</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ box.volume }} m³</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Surface</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ box.area }} m²</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Prix mensuel</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ formatCurrency(box.monthly_price) }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Longueur</p>
                            <p class="mt-1 text-sm text-gray-900">{{ box.length }} m</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Largeur</p>
                            <p class="mt-1 text-sm text-gray-900">{{ box.width }} m</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Hauteur</p>
                            <p class="mt-1 text-sm text-gray-900">{{ box.height }} m</p>
                        </div>
                    </div>

                    <div v-if="box.description" class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="mt-1 text-sm text-gray-900">{{ box.description }}</p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Équipements</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <svg :class="box.has_electricity ? 'text-green-500' : 'text-gray-300'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-700">Électricité</span>
                        </div>
                        <div class="flex items-center">
                            <svg :class="box.has_light ? 'text-green-500' : 'text-gray-300'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-700">Éclairage</span>
                        </div>
                        <div class="flex items-center">
                            <svg :class="box.is_climate_controlled ? 'text-green-500' : 'text-gray-300'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-700">Climatisé</span>
                        </div>
                        <div class="flex items-center">
                            <svg :class="box.is_ground_floor ? 'text-green-500' : 'text-gray-300'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-700">Rez-de-chaussée</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Localisation</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ box.floor.building.site.name }}</p>
                                <p class="text-sm text-gray-500">{{ box.floor.building.site.address }}</p>
                                <p class="text-sm text-gray-500">{{ box.floor.building.site.postal_code }} {{ box.floor.building.site.city }}, {{ box.floor.building.site.country }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ box.floor.building.name }}</p>
                                <p class="text-sm text-gray-500">{{ box.floor.name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <Link :href="`/sites/${box.floor.building.site.id}`" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Voir le site complet →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Contracts -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Contrats de location ({{ box.contracts.length }})</h3>
                </div>
                <div v-if="box.contracts.length > 0" class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="contract in box.contracts" :key="contract.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ contract.contract_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contract.customer.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ formatDate(contract.start_date) }}</div>
                                    <div class="text-xs text-gray-500">au {{ formatDate(contract.end_date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ formatCurrency(contract.monthly_amount) }}/mois
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <StatusBadge :status="contract.status" type="contract" size="small" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="`/contracts/${contract.id}`" class="text-indigo-600 hover:text-indigo-900">
                                        Voir
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <EmptyState
                    v-else
                    icon="document"
                    title="Aucun contrat"
                    description="Ce box n'a pas encore été loué."
                    size="small"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';

defineProps({
    box: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};
</script>
