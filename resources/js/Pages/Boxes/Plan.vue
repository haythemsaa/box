<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    boxes: Array,
    stats: Object,
});

const selectedBox = ref(null);
const showModal = ref(false);
const searchQuery = ref('');
const statusFilter = ref('all');

// Filter boxes
const filteredBoxes = computed(() => {
    let filtered = props.boxes;

    if (searchQuery.value) {
        filtered = filtered.filter(box =>
            box.number.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(box => box.status === statusFilter.value);
    }

    return filtered;
});

// Open modal with box details
const openBoxDetails = (box) => {
    selectedBox.value = box;
    showModal.value = true;
};

// Close modal
const closeModal = () => {
    showModal.value = false;
    selectedBox.value = null;
};

// Get box class based on status
const getBoxClass = (box) => {
    if (box.status === 'available') {
        return 'bg-green-400 hover:bg-green-500 border-green-600';
    }
    return 'bg-cyan-500 hover:bg-cyan-600 border-cyan-700';
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
    <Head title="Plan des boxes" />

    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Plan des boxes</h2>
                    <p class="mt-1 text-sm text-cyan-600 font-medium">
                        NB BOX : {{ stats.total }} - OCCUPÉ : {{ stats.occupied }} - LIBRE : {{ stats.available }}
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Rechercher un box
                        </label>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Numéro de box..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Filtrer par statut
                        </label>
                        <select
                            v-model="statusFilter"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        >
                            <option value="all">Tous les boxes</option>
                            <option value="available">Libres</option>
                            <option value="occupied">Occupés</option>
                            <option value="reserved">Réservés</option>
                        </select>
                    </div>

                    <!-- Legend -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Légende
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-cyan-500 border border-cyan-700 rounded"></div>
                                <span class="text-sm text-gray-600">Occupé</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-green-400 border border-green-600 rounded"></div>
                                <span class="text-sm text-gray-600">Libre</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box Grid -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="box in filteredBoxes"
                        :key="box.id"
                        @click="openBoxDetails(box)"
                        :class="[
                            'cursor-pointer border-2 rounded px-2 py-1 transition-all duration-200 transform hover:scale-105',
                            getBoxClass(box)
                        ]"
                    >
                        <div class="text-xs font-bold text-white text-center">{{ box.number }}</div>
                        <div class="text-xs text-white text-center">{{ box.volume }}m³</div>
                    </div>
                </div>

                <div v-if="filteredBoxes.length === 0" class="text-center py-12 text-gray-500">
                    Aucun box trouvé
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="showModal && selectedBox"
            class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"
                    @click="closeModal"
                ></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                >
                    <div class="bg-cyan-600 px-4 py-3">
                        <h3 class="text-lg font-medium text-white" id="modal-title">
                            Box {{ selectedBox.number }}
                        </h3>
                    </div>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="space-y-4">
                            <!-- Type & Size -->
                            <div>
                                <p class="text-sm font-medium text-gray-500">Type</p>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ selectedBox.volume }}m³ ({{ selectedBox.surface }} m2 - {{ selectedBox.volume }} m3)
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <p class="text-sm font-medium text-gray-500">État</p>
                                <div class="mt-1">
                                    <span
                                        :class="[
                                            'inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            selectedBox.status === 'available'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-cyan-100 text-cyan-800'
                                        ]"
                                    >
                                        {{ selectedBox.status === 'available' ? 'Libre' : 'Occupé' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Contract Info (if occupied) -->
                            <template v-if="selectedBox.contract">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Contrat</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ selectedBox.contract.contract_number }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Début le</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedBox.contract.start_date) }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Client</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ selectedBox.contract.customer_name }}</p>
                                </div>
                            </template>

                            <!-- Floor info -->
                            <div v-if="selectedBox.floor">
                                <p class="text-sm font-medium text-gray-500">Étage</p>
                                <p class="mt-1 text-sm text-gray-900">{{ selectedBox.floor.name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            @click="closeModal"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-cyan-600 text-base font-medium text-white hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
