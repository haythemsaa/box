<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-900">
                    Modifier le client: {{ customer.name }}
                </h2>
                <Link href="/customers" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow">
            <form @submit.prevent="submitForm">
                <div class="p-6 space-y-6">
                    <!-- Type de client (read-only display) -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Type de client</h3>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <svg v-if="form.type === 'individual'" class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <svg v-else class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="font-medium text-gray-900">{{ form.type === 'individual' ? 'Particulier' : 'Entreprise' }}</span>
                            <span class="ml-2 text-sm text-gray-500">(le type ne peut pas être modifié)</span>
                        </div>
                    </div>

                    <!-- Informations particulier -->
                    <div v-if="form.type === 'individual'" class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prénom <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.first_name }"
                                    required
                                />
                                <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}</p>
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.last_name }"
                                    required
                                />
                                <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date de naissance
                                </label>
                                <input
                                    id="date_of_birth"
                                    v-model="form.date_of_birth"
                                    type="date"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Informations entreprise -->
                    <div v-if="form.type === 'company'" class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations entreprise</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Raison sociale <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="company_name"
                                    v-model="form.company_name"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.company_name }"
                                    required
                                />
                                <p v-if="errors.company_name" class="mt-1 text-sm text-red-600">{{ errors.company_name }}</p>
                            </div>

                            <div>
                                <label for="siret" class="block text-sm font-medium text-gray-700 mb-2">
                                    SIRET
                                </label>
                                <input
                                    id="siret"
                                    v-model="form.siret"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div class="md:col-span-3">
                                <label for="vat_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Numéro de TVA
                                </label>
                                <input
                                    id="vat_number"
                                    v-model="form.vat_number"
                                    type="text"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.email }"
                                    required
                                />
                                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Adresse</h3>
                        <div class="grid grid-cols-1 gap-6">
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
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut</h3>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut du client <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required
                            >
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="suspended">Suspendu</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button
                        type="button"
                        @click="deleteCustomer"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700"
                    >
                        Supprimer
                    </button>
                    <div class="flex gap-3">
                        <Link
                            href="/customers"
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
import { ref, reactive } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    customer: {
        type: Object,
        required: true,
    },
});

const form = reactive({
    type: props.customer.type,
    first_name: props.customer.first_name || '',
    last_name: props.customer.last_name || '',
    date_of_birth: props.customer.date_of_birth || '',
    company_name: props.customer.company_name || '',
    siret: props.customer.siret || '',
    vat_number: props.customer.vat_number || '',
    email: props.customer.email,
    phone: props.customer.phone || '',
    address: props.customer.address,
    postal_code: props.customer.postal_code,
    city: props.customer.city,
    country: props.customer.country,
    status: props.customer.status,
});

const processing = ref(false);
const errors = ref({});

const submitForm = () => {
    processing.value = true;
    errors.value = {};

    useForm(form).put(`/customers/${props.customer.id}`, {
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

const deleteCustomer = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.')) {
        router.delete(`/customers/${props.customer.id}`);
    }
};
</script>
