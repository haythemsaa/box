<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-900">
                    Créer un nouveau site
                </h2>
                <Link href="/sites" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow">
            <form @submit.prevent="submitForm">
                <div class="p-6 space-y-6">
                    <!-- Informations générales -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom du site <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.name }"
                                    required
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <!-- Code -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Code <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="code"
                                    v-model="form.code"
                                    type="text"
                                    placeholder="PAR-001"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.code }"
                                    required
                                />
                                <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ errors.code }}</p>
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
                                    <option value="active">Actif</option>
                                    <option value="inactive">Inactif</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Adresse</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adresse <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="address"
                                    v-model="form.address"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.address }"
                                    required
                                />
                                <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Postal Code -->
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        Code postal <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        type="text"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        :class="{ 'border-red-500': errors.postal_code }"
                                        required
                                    />
                                    <p v-if="errors.postal_code" class="mt-1 text-sm text-red-600">{{ errors.postal_code }}</p>
                                </div>

                                <!-- City -->
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                        Ville <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        :class="{ 'border-red-500': errors.city }"
                                        required
                                    />
                                    <p v-if="errors.city" class="mt-1 text-sm text-red-600">{{ errors.city }}</p>
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pays <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="country"
                                        v-model="form.country"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                    >
                                        <option value="FR">France</option>
                                        <option value="BE">Belgique</option>
                                        <option value="NL">Pays-Bas</option>
                                        <option value="DE">Allemagne</option>
                                        <option value="ES">Espagne</option>
                                        <option value="IT">Italie</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Coordinates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                        Latitude
                                    </label>
                                    <input
                                        id="latitude"
                                        v-model="form.latitude"
                                        type="number"
                                        step="0.0000001"
                                        placeholder="48.8566"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                        Longitude
                                    </label>
                                    <input
                                        id="longitude"
                                        v-model="form.longitude"
                                        type="number"
                                        step="0.0000001"
                                        placeholder="2.3522"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="+33 1 23 45 67 89"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="contact@site.fr"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <Link
                        href="/sites"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                        Annuler
                    </Link>
                    <button
                        type="submit"
                        :disabled="processing"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ processing ? 'Création...' : 'Créer le site' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = reactive({
    name: '',
    code: '',
    address: '',
    postal_code: '',
    city: '',
    country: 'FR',
    phone: '',
    email: '',
    latitude: null,
    longitude: null,
    status: 'active',
});

const processing = ref(false);
const errors = ref({});

const submitForm = () => {
    processing.value = true;
    errors.value = {};

    useForm(form).post('/sites', {
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
</script>
