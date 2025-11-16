<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-900">
                    Créer un nouveau contrat
                </h2>
                <Link href="/contracts" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow">
            <form @submit.prevent="submitForm">
                <div class="p-6 space-y-6">
                    <!-- Client et Box -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations principales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Client <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="customer_id"
                                    v-model="form.customer_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.customer_id }"
                                    required
                                >
                                    <option value="">Sélectionner un client</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.name }} ({{ customer.type === 'individual' ? 'Particulier' : 'Entreprise' }})
                                    </option>
                                </select>
                                <p v-if="errors.customer_id" class="mt-1 text-sm text-red-600">{{ errors.customer_id }}</p>
                            </div>

                            <div>
                                <label for="box_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Box <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="box_id"
                                    v-model="form.box_id"
                                    @change="updateMonthlyAmount"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.box_id }"
                                    required
                                >
                                    <option value="">Sélectionner un box</option>
                                    <option v-for="box in availableBoxes" :key="box.id" :value="box.id">
                                        {{ box.number }} - {{ box.site_name }} ({{ box.floor_name }}) - {{ formatCurrency(box.monthly_price) }}/mois
                                    </option>
                                </select>
                                <p v-if="errors.box_id" class="mt-1 text-sm text-red-600">{{ errors.box_id }}</p>
                                <p v-if="selectedBox" class="mt-1 text-xs text-gray-500">
                                    Volume: {{ selectedBox.volume }}m³ | Surface: {{ selectedBox.area }}m²
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Période du contrat -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Période du contrat</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date de début <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="start_date"
                                    v-model="form.start_date"
                                    type="date"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.start_date }"
                                    required
                                />
                                <p v-if="errors.start_date" class="mt-1 text-sm text-red-600">{{ errors.start_date }}</p>
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date de fin <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="end_date"
                                    v-model="form.end_date"
                                    type="date"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.end_date }"
                                    required
                                />
                                <p v-if="errors.end_date" class="mt-1 text-sm text-red-600">{{ errors.end_date }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Montants -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Montants et facturation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="monthly_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant mensuel (€) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="monthly_amount"
                                    v-model.number="form.monthly_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.monthly_amount }"
                                    required
                                />
                                <p v-if="errors.monthly_amount" class="mt-1 text-sm text-red-600">{{ errors.monthly_amount }}</p>
                            </div>

                            <div>
                                <label for="deposit_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Caution (€) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="deposit_amount"
                                    v-model.number="form.deposit_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': errors.deposit_amount }"
                                    required
                                />
                                <p v-if="errors.deposit_amount" class="mt-1 text-sm text-red-600">{{ errors.deposit_amount }}</p>
                            </div>

                            <div>
                                <label for="billing_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fréquence de facturation <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="billing_frequency"
                                    v-model="form.billing_frequency"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="monthly">Mensuelle</option>
                                    <option value="quarterly">Trimestrielle</option>
                                    <option value="yearly">Annuelle</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Statut et notes -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut et notes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Statut du contrat <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="draft">Brouillon</option>
                                    <option value="pending">En attente</option>
                                    <option value="active">Actif</option>
                                    <option value="expired">Expiré</option>
                                    <option value="cancelled">Annulé</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Le statut "Actif" marquera le box comme occupé</p>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Notes additionnelles sur le contrat..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Assurances -->
                    <div class="pt-6 border-t border-gray-200">
                        <InsuranceSelector
                            v-model="form.insurance_products"
                            :insurance-products="insuranceProducts"
                            :mandatory-insurances="mandatoryInsurances"
                        />
                    </div>

                    <!-- Récapitulatif des montants -->
                    <div v-if="form.monthly_amount > 0" class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif mensuel</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Loyer du box</span>
                                <span class="font-medium">{{ formatCurrency(form.monthly_amount) }}</span>
                            </div>
                            <div v-if="totalInsurancePremium > 0" class="flex justify-between text-sm">
                                <span class="text-gray-600">Assurances ({{ form.insurance_products.length }})</span>
                                <span class="font-medium">{{ formatCurrency(totalInsurancePremium) }}</span>
                            </div>
                            <div class="pt-2 border-t border-gray-300 flex justify-between">
                                <span class="font-semibold text-gray-900">Total mensuel</span>
                                <span class="text-xl font-bold text-indigo-600">{{ formatCurrency(totalMonthlyAmount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <Link
                        href="/contracts"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                        Annuler
                    </Link>
                    <button
                        type="submit"
                        :disabled="processing"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ processing ? 'Création...' : 'Créer le contrat' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InsuranceSelector from '@/Components/InsuranceSelector.vue';

const props = defineProps({
    customers: {
        type: Array,
        required: true,
    },
    availableBoxes: {
        type: Array,
        required: true,
    },
    insuranceProducts: {
        type: Array,
        default: () => [],
    },
    mandatoryInsurances: {
        type: Array,
        default: () => [],
    },
});

const form = reactive({
    customer_id: '',
    box_id: '',
    start_date: '',
    end_date: '',
    monthly_amount: 0,
    deposit_amount: 0,
    billing_frequency: 'monthly',
    status: 'draft',
    notes: '',
    insurance_products: [],
});

const processing = ref(false);
const errors = ref({});

const selectedBox = computed(() => {
    if (!form.box_id) return null;
    return props.availableBoxes.find(box => box.id === form.box_id);
});

const totalInsurancePremium = computed(() => {
    if (!form.insurance_products || form.insurance_products.length === 0) return 0;

    return form.insurance_products.reduce((total, productId) => {
        const product = props.insuranceProducts.find(p => p.id === productId);
        return total + (product ? product.monthly_price : 0);
    }, 0);
});

const totalMonthlyAmount = computed(() => {
    return form.monthly_amount + totalInsurancePremium.value;
});

const updateMonthlyAmount = () => {
    if (selectedBox.value) {
        form.monthly_amount = selectedBox.value.monthly_price;
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const submitForm = () => {
    processing.value = true;
    errors.value = {};

    useForm(form).post('/contracts', {
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
