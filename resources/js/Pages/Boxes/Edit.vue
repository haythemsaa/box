<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-900">
                    Modifier la box: {{ box.number }}
                </h2>
                <Link href="/boxes" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow">
            <form @submit.prevent="submitForm">
                <div class="p-6 space-y-6">
                    <!-- Localisation -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Localisation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Site -->
                            <div>
                                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Site <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="site_id"
                                    v-model="selectedSite"
                                    @change="onSiteChange"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="">Sélectionner un site</option>
                                    <option v-for="site in sites" :key="site.id" :value="site.id">
                                        {{ site.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Building -->
                            <div>
                                <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bâtiment <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="building_id"
                                    v-model="selectedBuilding"
                                    @change="onBuildingChange"
                                    :disabled="!selectedSite"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100"
                                    required
                                >
                                    <option value="">Sélectionner un bâtiment</option>
                                    <option v-for="building in availableBuildings" :key="building.id" :value="building.id">
                                        {{ building.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Floor -->
                            <div>
                                <label for="floor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Étage <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="floor_id"
                                    v-model="form.floor_id"
                                    :disabled="!selectedBuilding"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100"
                                    :class="{ 'border-red-500': errors.floor_id }"
                                    required
                                >
                                    <option value="">Sélectionner un étage</option>
                                    <option v-for="floor in availableFloors" :key="floor.id" :value="floor.id">
                                        {{ floor.name }}
                                    </option>
                                </select>
                                <p v-if="errors.floor_id" class="mt-1 text-sm text-red-600">{{ errors.floor_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de base -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de base</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Number -->
                            <div>
                                <label for="number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Numéro <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="number"
                                    v-model="form.number"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.number }"
                                    required
                                />
                                <p v-if="errors.number" class="mt-1 text-sm text-red-600">{{ errors.number }}</p>
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="standard">Standard</option>
                                    <option value="climat">Climatisée</option>
                                    <option value="premium">Premium</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Statut <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="available">Disponible</option>
                                    <option value="occupied">Occupée</option>
                                    <option value="reserved">Réservée</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dimensions -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dimensions (en cm)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Length -->
                            <div>
                                <label for="length" class="block text-sm font-medium text-gray-700 mb-2">
                                    Longueur <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="length"
                                    v-model.number="form.length"
                                    type="number"
                                    step="0.01"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.length }"
                                    required
                                />
                                <p v-if="errors.length" class="mt-1 text-sm text-red-600">{{ errors.length }}</p>
                            </div>

                            <!-- Width -->
                            <div>
                                <label for="width" class="block text-sm font-medium text-gray-700 mb-2">
                                    Largeur <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="width"
                                    v-model.number="form.width"
                                    type="number"
                                    step="0.01"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.width }"
                                    required
                                />
                                <p v-if="errors.width" class="mt-1 text-sm text-red-600">{{ errors.width }}</p>
                            </div>

                            <!-- Height -->
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hauteur <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="height"
                                    v-model.number="form.height"
                                    type="number"
                                    step="0.01"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.height }"
                                    required
                                />
                                <p v-if="errors.height" class="mt-1 text-sm text-red-600">{{ errors.height }}</p>
                            </div>
                        </div>

                        <!-- Calculated values -->
                        <div v-if="form.length && form.width && form.height" class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Volume:</span>
                                    <span class="ml-2 font-semibold text-blue-900">{{ calculatedVolume }} m³</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Surface au sol:</span>
                                    <span class="ml-2 font-semibold text-blue-900">{{ calculatedArea }} m²</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tarification</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="monthly_price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prix mensuel (€) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="monthly_price"
                                    v-model.number="form.monthly_price"
                                    type="number"
                                    step="0.01"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.monthly_price }"
                                    required
                                />
                                <p v-if="errors.monthly_price" class="mt-1 text-sm text-red-600">{{ errors.monthly_price }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                        <div>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Description de la box..."
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Caractéristiques</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <label class="flex items-center">
                                <input
                                    v-model="selectedFeatures"
                                    type="checkbox"
                                    value="Accès 24/7"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Accès 24/7</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    v-model="selectedFeatures"
                                    type="checkbox"
                                    value="Surveillance"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Surveillance</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    v-model="selectedFeatures"
                                    type="checkbox"
                                    value="Alarme"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Alarme</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    v-model="selectedFeatures"
                                    type="checkbox"
                                    value="Climatisé"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Climatisé</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    v-model="selectedFeatures"
                                    type="checkbox"
                                    value="Assurance incluse"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Assurance incluse</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button
                        type="button"
                        @click="deleteBox"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700"
                    >
                        Supprimer
                    </button>
                    <div class="flex gap-3">
                        <Link
                            href="/boxes"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ processing ? 'Mise à jour...' : 'Mettre à jour' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    box: {
        type: Object,
        required: true,
    },
    sites: {
        type: Array,
        default: () => [],
    },
});

// Initialize location hierarchy from box data
const selectedSite = ref('');
const selectedBuilding = ref('');
const selectedFeatures = ref(props.box.features || []);

const form = reactive({
    floor_id: props.box.floor_id,
    number: props.box.number,
    length: props.box.length,
    width: props.box.width,
    height: props.box.height,
    monthly_price: props.box.monthly_price,
    type: props.box.type,
    status: props.box.status,
    features: props.box.features || [],
    description: props.box.description || '',
});

const processing = ref(false);
const errors = ref({});

// Initialize location hierarchy
onMounted(() => {
    // Find which site/building/floor this box belongs to
    for (const site of props.sites) {
        for (const building of site.buildings || []) {
            for (const floor of building.floors || []) {
                if (floor.id === props.box.floor_id) {
                    selectedSite.value = site.id;
                    selectedBuilding.value = building.id;
                    return;
                }
            }
        }
    }
});

const availableBuildings = computed(() => {
    if (!selectedSite.value) return [];
    const site = props.sites.find(s => s.id === selectedSite.value);
    return site?.buildings || [];
});

const availableFloors = computed(() => {
    if (!selectedBuilding.value) return [];
    const building = availableBuildings.value.find(b => b.id === selectedBuilding.value);
    return building?.floors || [];
});

const calculatedVolume = computed(() => {
    if (!form.length || !form.width || !form.height) return 0;
    return ((form.length * form.width * form.height) / 1000000).toFixed(2);
});

const calculatedArea = computed(() => {
    if (!form.length || !form.width) return 0;
    return ((form.length * form.width) / 10000).toFixed(2);
});

const onSiteChange = () => {
    selectedBuilding.value = '';
    form.floor_id = '';
};

const onBuildingChange = () => {
    form.floor_id = '';
};

const submitForm = () => {
    processing.value = true;
    errors.value = {};

    // Update features from checkboxes
    form.features = selectedFeatures.value;

    useForm(form).put(`/boxes/${props.box.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            // Form submitted successfully
        },
        onError: (err) => {
            errors.value = err;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};

const deleteBox = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette box ? Cette action est irréversible.')) {
        router.delete(`/boxes/${props.box.id}`);
    }
};
</script>
