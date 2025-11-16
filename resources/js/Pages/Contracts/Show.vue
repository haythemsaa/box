<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        Contrat {{ contract.contract_number }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Créé le {{ contract.created_at }}</p>
                </div>
                <div class="flex gap-3">
                    <Link :href="`/contracts/${contract.id}/edit`" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                        Modifier
                    </Link>
                    <Link href="/contracts" class="text-gray-600 hover:text-gray-900">
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
                        <h3 class="text-lg font-semibold text-gray-900">Statut du contrat</h3>
                        <p class="mt-1 text-sm text-gray-500">État actuel de la location</p>
                    </div>
                    <StatusBadge :status="contract.status" type="contract" size="large" />
                </div>
            </div>

            <!-- Contract Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informations du contrat</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Période de location</p>
                            <p class="mt-1 text-sm text-gray-900">Du {{ contract.start_date }}</p>
                            <p class="text-sm text-gray-900">Au {{ contract.end_date }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Montant mensuel</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ formatCurrency(contract.monthly_amount) }}</p>
                            <p class="text-xs text-gray-500">Facturation {{ getBillingFrequencyLabel(contract.billing_frequency) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Caution</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ formatCurrency(contract.deposit_amount) }}</p>
                        </div>
                    </div>

                    <div v-if="contract.access_code" class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Code d'accès</p>
                                <p class="mt-1 text-sm font-mono text-gray-900">{{ contract.access_code }}</p>
                            </div>
                            <div v-if="contract.signed_at">
                                <p class="text-sm font-medium text-gray-500">Signé le</p>
                                <p class="mt-1 text-sm text-gray-900">{{ contract.signed_at }}</p>
                                <p class="text-xs text-gray-500">{{ contract.signature_method }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="contract.stored_items" class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Objets stockés</p>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ contract.stored_items }}</p>
                    </div>

                    <div v-if="contract.notes" class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Notes</p>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ contract.notes }}</p>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Client</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center">
                                <h4 class="text-lg font-medium text-gray-900">{{ contract.customer.name }}</h4>
                                <span :class="contract.customer.type === 'company' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'" class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ contract.customer.type === 'company' ? 'Entreprise' : 'Particulier' }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ contract.customer.email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ contract.customer.phone || 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>
                        <Link :href="`/customers/${contract.customer.id}`" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Voir le profil
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Box Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Box loué</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Box {{ contract.box.number }}</h4>
                            <p class="mt-1 text-sm text-gray-500">{{ contract.box.site_name }} - {{ contract.box.floor_name }}</p>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Volume</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ contract.box.volume }} m³</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Surface</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ contract.box.area }} m²</p>
                                </div>
                            </div>
                        </div>
                        <Link :href="`/boxes/${contract.box.id}`" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Voir le box
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Insurances -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Assurances</h3>
                        <p v-if="contract.total_monthly_with_insurance" class="text-sm text-gray-500 mt-1">
                            Total mensuel avec assurances:
                            <span class="font-semibold text-indigo-600">{{ formatCurrency(contract.total_monthly_with_insurance) }}</span>
                        </p>
                    </div>
                    <button
                        v-if="availableInsuranceProducts && availableInsuranceProducts.length > 0"
                        @click="showAddInsuranceModal = true"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Ajouter une assurance
                    </button>
                </div>
                <div v-if="contract.insurances && contract.insurances.length > 0" class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prime mensuelle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couverture</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depuis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total payé</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="insurance in contract.insurances" :key="insurance.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ insurance.product_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatCurrency(insurance.monthly_premium) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatCurrency(insurance.coverage_amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ insurance.start_date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <StatusBadge :status="insurance.status" type="insurance" size="small" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ formatCurrency(insurance.total_premium_paid) }}</div>
                                    <div class="text-xs text-green-600">Commission: {{ formatCurrency(insurance.total_commission_earned) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        v-if="insurance.status === 'active'"
                                        @click="confirmCancelInsurance(insurance)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Annuler
                                    </button>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <EmptyState
                    v-else
                    icon="shield"
                    title="Aucune assurance souscrite"
                    description="Ajoutez une assurance pour protéger ce contrat"
                    :action-text="availableInsuranceProducts && availableInsuranceProducts.length > 0 ? 'Ajouter une assurance' : ''"
                    @action="showAddInsuranceModal = true"
                    size="medium"
                />
            </div>

            <!-- Add Insurance Modal -->
            <div v-if="showAddInsuranceModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showAddInsuranceModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Ajouter une assurance
                                    </h3>
                                    <div class="mt-4">
                                        <label for="insurance_product_id" class="block text-sm font-medium text-gray-700">
                                            Produit d'assurance
                                        </label>
                                        <select
                                            id="insurance_product_id"
                                            v-model="addInsuranceForm.insurance_product_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        >
                                            <option value="">Sélectionner un produit</option>
                                            <option
                                                v-for="product in availableInsuranceProducts"
                                                :key="product.id"
                                                :value="product.id"
                                            >
                                                {{ product.name }} - {{ formatCurrency(product.monthly_price) }}/mois
                                                (Couverture: {{ formatCurrency(product.max_coverage_amount) }})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="button"
                                @click="addInsurance"
                                :disabled="!addInsuranceForm.insurance_product_id || addingInsurance"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                {{ addingInsurance ? 'Ajout...' : 'Ajouter' }}
                            </button>
                            <button
                                type="button"
                                @click="showAddInsuranceModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Factures récentes</h3>
                </div>
                <div v-if="contract.invoices.length > 0" class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Échéance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="invoice in contract.invoices" :key="invoice.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ invoice.invoice_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(invoice.amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ invoice.due_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getInvoiceStatusClass(invoice.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ invoice.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="p-6 text-center text-sm text-gray-500">
                    Aucune facture pour le moment
                </div>
            </div>

            <!-- Payments -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Paiements récents</h3>
                </div>
                <div v-if="contract.payments.length > 0" class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Méthode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="payment in contract.payments" :key="payment.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ payment.payment_reference }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(payment.amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ payment.payment_method }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ payment.paid_at || 'En attente' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getPaymentStatusClass(payment.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ payment.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="p-6 text-center text-sm text-gray-500">
                    Aucun paiement pour le moment
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps({
    contract: {
        type: Object,
        required: true,
    },
    availableInsuranceProducts: {
        type: Array,
        default: () => [],
    },
});

const showAddInsuranceModal = ref(false);
const addingInsurance = ref(false);
const addInsuranceForm = reactive({
    insurance_product_id: '',
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const getBillingFrequencyLabel = (frequency) => {
    const labels = {
        monthly: 'mensuelle',
        quarterly: 'trimestrielle',
        yearly: 'annuelle',
    };
    return labels[frequency] || frequency;
};

const addInsurance = () => {
    if (!addInsuranceForm.insurance_product_id) return;

    addingInsurance.value = true;

    router.post(
        `/contracts/${props.contract.id}/insurances`,
        {
            insurance_product_id: addInsuranceForm.insurance_product_id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showAddInsuranceModal.value = false;
                addInsuranceForm.insurance_product_id = '';
            },
            onFinish: () => {
                addingInsurance.value = false;
            },
        }
    );
};

const confirmCancelInsurance = (insurance) => {
    if (confirm(`Êtes-vous sûr de vouloir annuler l'assurance "${insurance.product_name}" ? Cette action est irréversible.`)) {
        cancelInsurance(insurance);
    }
};

const cancelInsurance = (insurance) => {
    router.delete(`/contracts/${props.contract.id}/insurances/${insurance.id}`, {
        preserveScroll: true,
    });
};
</script>
