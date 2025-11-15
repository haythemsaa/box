<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    Catalogue des Box
                </h2>
                <Link href="/boxes/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle box
                </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les sites</option>
                        <option v-for="site in filters.sites" :key="site.id" :value="site.id">{{ site.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous</option>
                        <option value="available">Disponible</option>
                        <option value="occupied">Occupée</option>
                        <option value="reserved">Réservée</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous</option>
                        <option value="standard">Standard</option>
                        <option value="climat">Climatisée</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition duration-150">
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Boxes Grid -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div v-if="boxes.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                <div v-for="box in boxes.data" :key="box.id" class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-150">
                    <!-- Box Header -->
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 text-white">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold">{{ box.number }}</h3>
                            <span :class="getStatusBadgeClass(box.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ getStatusLabel(box.status) }}
                            </span>
                        </div>
                        <p class="text-sm opacity-90">{{ box.site_name }}</p>
                        <p class="text-xs opacity-75">{{ box.floor_label }}</p>
                    </div>

                    <!-- Box Details -->
                    <div class="p-4">
                        <!-- Dimensions -->
                        <div class="mb-4">
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <span class="font-medium">Dimensions</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="bg-gray-50 rounded p-2">
                                    <p class="text-xs text-gray-600">L</p>
                                    <p class="font-semibold text-gray-900">{{ box.length }}cm</p>
                                </div>
                                <div class="bg-gray-50 rounded p-2">
                                    <p class="text-xs text-gray-600">l</p>
                                    <p class="font-semibold text-gray-900">{{ box.width }}cm</p>
                                </div>
                                <div class="bg-gray-50 rounded p-2">
                                    <p class="text-xs text-gray-600">H</p>
                                    <p class="font-semibold text-gray-900">{{ box.height }}cm</p>
                                </div>
                            </div>
                        </div>

                        <!-- Volume & Area -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-blue-50 rounded-lg p-3">
                                <p class="text-xs text-blue-600 font-medium mb-1">Volume</p>
                                <p class="text-lg font-bold text-blue-900">{{ box.volume }} m³</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3">
                                <p class="text-xs text-green-600 font-medium mb-1">Surface</p>
                                <p class="text-lg font-bold text-green-900">{{ box.area }} m²</p>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="bg-indigo-50 rounded-lg p-3 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-indigo-600 font-medium">Prix mensuel</span>
                                <span class="text-2xl font-bold text-indigo-900">{{ box.monthly_price }}€</span>
                            </div>
                        </div>

                        <!-- Features -->
                        <div v-if="box.features && box.features.length > 0" class="mb-4">
                            <div class="flex flex-wrap gap-1">
                                <span v-for="(feature, index) in box.features" :key="index" class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                    {{ feature }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <Link :href="`/boxes/${box.id}`" class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition duration-150">
                                Voir
                            </Link>
                            <Link :href="`/boxes/${box.id}/edit`" class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition duration-150">
                                Modifier
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune box</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer votre première box de stockage.</p>
                <div class="mt-6">
                    <Link href="/boxes/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Créer une box
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="boxes.data.length > 0 && (boxes.prev_page_url || boxes.next_page_url)" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">{{ boxes.from }}</span> à <span class="font-medium">{{ boxes.to }}</span> sur <span class="font-medium">{{ boxes.total }}</span> boxes
                    </div>
                    <div class="flex gap-2">
                        <Link v-if="boxes.prev_page_url" :href="boxes.prev_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Précédent
                        </Link>
                        <Link v-if="boxes.next_page_url" :href="boxes.next_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
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
    boxes: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            sites: [],
        }),
    },
});

const getStatusBadgeClass = (status) => {
    const classes = {
        available: 'bg-green-500 text-white',
        occupied: 'bg-red-500 text-white',
        reserved: 'bg-yellow-500 text-white',
        maintenance: 'bg-gray-500 text-white',
    };
    return classes[status] || 'bg-gray-500 text-white';
};

const getStatusLabel = (status) => {
    const labels = {
        available: 'Disponible',
        occupied: 'Occupée',
        reserved: 'Réservée',
        maintenance: 'Maintenance',
    };
    return labels[status] || status;
};
</script>
