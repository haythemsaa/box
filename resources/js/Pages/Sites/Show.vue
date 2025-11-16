<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        {{ site.name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">{{ site.city }}, {{ site.country }}</p>
                </div>
                <div class="flex gap-3">
                    <Link :href="`/sites/${site.id}/edit`" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                        Modifier
                    </Link>
                    <Link href="/sites" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Site Overview -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Vue d'ensemble</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ site.buildings.length }}</div>
                            <div class="text-sm text-gray-500 mt-1">Bâtiments</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ totalFloors }}</div>
                            <div class="text-sm text-gray-500 mt-1">Étages</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ totalBoxes }}</div>
                            <div class="text-sm text-gray-500 mt-1">Boxes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ availableBoxes }}</div>
                            <div class="text-sm text-gray-500 mt-1">Disponibles</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Site Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informations du site</h3>
                </div>
                <div class="p-6">
                    <!-- Address -->
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Adresse</h4>
                            <div class="text-sm text-gray-700">
                                <p>{{ site.address }}</p>
                                <p>{{ site.postal_code }} {{ site.city }}, {{ site.country }}</p>
                            </div>
                        </div>

                        <!-- GPS Coordinates -->
                        <div v-if="site.gps_latitude && site.gps_longitude" class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Coordonnées GPS</h4>
                            <div class="text-sm text-gray-700">
                                <p>Latitude: {{ site.gps_latitude }}</p>
                                <p>Longitude: {{ site.gps_longitude }}</p>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Contact</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ site.phone || 'Non renseigné' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ site.email || 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="site.description" class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Description</h4>
                            <p class="text-sm text-gray-700">{{ site.description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buildings & Boxes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Bâtiments et boxes</h3>
                </div>
                <div v-if="site.buildings.length > 0" class="divide-y divide-gray-200">
                    <div v-for="building in site.buildings" :key="building.id" class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">{{ building.name }}</h4>
                            <span class="text-sm text-gray-500">{{ getBuildingBoxCount(building) }} boxes</span>
                        </div>

                        <div v-if="building.floors.length > 0" class="space-y-3">
                            <div v-for="floor in building.floors" :key="floor.id" class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-medium text-gray-900">{{ floor.name }}</h5>
                                    <span class="text-xs text-gray-500">{{ floor.boxes.length }} boxes</span>
                                </div>

                                <div v-if="floor.boxes.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                    <Link
                                        v-for="box in floor.boxes"
                                        :key="box.id"
                                        :href="`/boxes/${box.id}`"
                                        class="flex flex-col items-center justify-center p-3 bg-white rounded border-2 hover:border-indigo-500 transition-colors"
                                        :class="getBoxBorderClass(box.status)"
                                    >
                                        <div class="text-sm font-medium text-gray-900">{{ box.number }}</div>
                                        <div class="text-xs text-gray-500">{{ box.volume }}m³</div>
                                        <div class="mt-1">
                                            <StatusBadge :status="box.status" type="box" size="small" />
                                        </div>
                                    </Link>
                                </div>
                                <EmptyState
                                    v-else
                                    icon="box"
                                    title="Aucun box sur cet étage"
                                    size="small"
                                />
                            </div>
                        </div>
                        <EmptyState
                            v-else
                            icon="folder"
                            title="Aucun étage dans ce bâtiment"
                            size="small"
                        />
                    </div>
                </div>
                <EmptyState
                    v-else
                    icon="building"
                    title="Aucun bâtiment sur ce site"
                    description="Ajoutez des bâtiments pour organiser vos boxes"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps({
    site: {
        type: Object,
        required: true,
    },
});

const totalFloors = computed(() => {
    return props.site.buildings.reduce((sum, building) => sum + building.floors.length, 0);
});

const totalBoxes = computed(() => {
    return props.site.buildings.reduce((sum, building) => {
        return sum + building.floors.reduce((floorSum, floor) => floorSum + floor.boxes.length, 0);
    }, 0);
});

const availableBoxes = computed(() => {
    return props.site.buildings.reduce((sum, building) => {
        return sum + building.floors.reduce((floorSum, floor) => {
            return floorSum + floor.boxes.filter(box => box.status === 'available').length;
        }, 0);
    }, 0);
});

const getBuildingBoxCount = (building) => {
    return building.floors.reduce((sum, floor) => sum + floor.boxes.length, 0);
};

const getBoxBorderClass = (status) => {
    const classes = {
        available: 'border-green-300',
        occupied: 'border-red-300',
        maintenance: 'border-yellow-300',
        reserved: 'border-blue-300',
    };
    return classes[status] || 'border-gray-300';
};
</script>
