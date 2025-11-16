<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-900">
                    Modifier le contrat {{ contract.contract_number }}
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
                    <!-- Client et Box (read-only) -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations principales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="font-medium text-gray-900">{{ contract.customer.name }}</span>
                                    <span class="ml-2 text-sm text-gray-500">(non modifiable)</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Box</label>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="font-medium text-gray-900">{{ contract.box.number }}</span>
                                    <span class="ml-2 text-sm text-gray-500">(non modifiable)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Période du contrat -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Période du contrat</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="font-medium text-gray-900">{{ contract.start_date }}</span>
                                    <span class="ml-2 text-sm text-gray-500">(non modifiable)</span>
                                </div>
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
                                <p class="mt-1 text-xs text-gray-500">
                                    Le changement de statut peut affecter la disponibilité du box
                                </p>
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

                    <!-- Assurances (lecture seule) -->
                    <div v-if="contract.insurances && contract.insurances.length > 0" class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Assurances actives</h3>
                            <Link
                                :href="`/contracts/${contract.id}`"
                                class="text-sm text-indigo-600 hover:text-indigo-900 font-medium"
                            >
                                Gérer les assurances →
                            </Link>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div
                                v-for="insurance in contract.insurances.filter(i => i.status === 'active')"
                                :key="insurance.id"
                                class="flex justify-between items-center text-sm"
                            >
                                <span class="text-gray-900 font-medium">{{ insurance.product_name }}</span>
                                <span class="text-gray-600">{{ formatCurrency(insurance.monthly_premium) }}/mois</span>
                            </div>
                            <div v-if="activeInsurancesCount === 0" class="text-sm text-gray-500 text-center py-2">
                                Aucune assurance active
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Pour ajouter ou annuler des assurances, rendez-vous sur la page de détails du contrat.
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button
                        type="button"
                        @click="deleteContract"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700"
                    >
                        Supprimer
                    </button>
                    <div class="flex gap-3">
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
                            {{ processing ? 'Mise à jour...' : 'Mettre à jour' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    contract: {
        type: Object,
        required: true,
    },
});

const form = reactive({
    end_date: props.contract.end_date || '',
    monthly_amount: props.contract.monthly_amount || 0,
    deposit_amount: props.contract.deposit_amount || 0,
    billing_frequency: props.contract.billing_frequency || 'monthly',
    status: props.contract.status || 'draft',
    notes: props.contract.notes || '',
});

const processing = ref(false);
const errors = ref({});

const activeInsurancesCount = computed(() => {
    if (!props.contract.insurances) return 0;
    return props.contract.insurances.filter(i => i.status === 'active').length;
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const submitForm = () => {
    processing.value = true;
    errors.value = {};

    useForm(form).put(`/contracts/${props.contract.id}`, {
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

const deleteContract = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce contrat ? Cette action est irréversible et le box sera marqué comme disponible.')) {
        router.delete(`/contracts/${props.contract.id}`);
    }
};
</script>
