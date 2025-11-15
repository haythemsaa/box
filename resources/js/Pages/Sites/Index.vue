<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    Sites de stockage
                </h2>
                <Link href="/sites/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau site
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Sites Grid -->
            <div v-if="sites.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                <div v-for="site in sites.data" :key="site.id" class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-150">
                    <!-- Site Image -->
                    <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>

                    <!-- Site Info -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ site.name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ site.code }}</p>
                            </div>
                            <span :class="getStatusClass(site.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                {{ getStatusLabel(site.status) }}
                            </span>
                        </div>

                        <!-- Address -->
                        <div class="flex items-start text-sm text-gray-600 mb-3">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div>
                                <p>{{ site.address }}</p>
                                <p>{{ site.postal_code }} {{ site.city }}, {{ site.country }}</p>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-2xl font-semibold text-gray-900">{{ site.boxes_count || 0 }}</p>
                                <p class="text-xs text-gray-600 mt-1">Box</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-semibold text-green-600">{{ site.available_boxes_count || 0 }}</p>
                                <p class="text-xs text-gray-600 mt-1">Disponibles</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-semibold text-indigo-600">{{ site.occupancy_rate || 0 }}%</p>
                                <p class="text-xs text-gray-600 mt-1">Taux</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-4 flex gap-2">
                            <Link :href="`/sites/${site.id}`" class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition duration-150">
                                Voir
                            </Link>
                            <Link :href="`/sites/${site.id}/edit`" class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition duration-150">
                                Modifier
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun site</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer votre premier site de stockage.</p>
                <div class="mt-6">
                    <Link href="/sites/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Créer un site
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="sites.data.length > 0 && (sites.prev_page_url || sites.next_page_url)" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">{{ sites.from }}</span> à <span class="font-medium">{{ sites.to }}</span> sur <span class="font-medium">{{ sites.total }}</span> sites
                    </div>
                    <div class="flex gap-2">
                        <Link v-if="sites.prev_page_url" :href="sites.prev_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Précédent
                        </Link>
                        <Link v-if="sites.next_page_url" :href="sites.next_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Suivant
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    sites: {
        type: Object,
        required: true,
    },
});

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        maintenance: 'bg-yellow-100 text-yellow-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        active: 'Actif',
        inactive: 'Inactif',
        maintenance: 'Maintenance',
    };
    return labels[status] || status;
};
</script>
