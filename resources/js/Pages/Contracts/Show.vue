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
                    <span :class="getStatusClass(contract.status)" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium">
                        {{ getStatusLabel(contract.status) }}
                    </span>
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
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    contract: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        pending: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        expired: 'bg-orange-100 text-orange-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        draft: 'Brouillon',
        pending: 'En attente',
        active: 'Actif',
        expired: 'Expiré',
        cancelled: 'Annulé',
    };
    return labels[status] || status;
};

const getBillingFrequencyLabel = (frequency) => {
    const labels = {
        monthly: 'mensuelle',
        quarterly: 'trimestrielle',
        yearly: 'annuelle',
    };
    return labels[frequency] || frequency;
};

const getInvoiceStatusClass = (status) => {
    const classes = {
        paid: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        overdue: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentStatusClass = (status) => {
    const classes = {
        completed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        failed: 'bg-red-100 text-red-800',
        refunded: 'bg-orange-100 text-orange-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>
