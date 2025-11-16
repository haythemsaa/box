<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        {{ customer.name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Client depuis le {{ customer.created_at }}</p>
                </div>
                <div class="flex gap-3">
                    <Link :href="`/customers/${customer.id}/edit`" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                        Modifier
                    </Link>
                    <Link href="/customers" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Type & Status Badge -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
                        <p class="mt-1 text-sm text-gray-500">Type de client et statut actuel</p>
                    </div>
                    <div class="flex gap-2">
                        <span :class="customer.type === 'company' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                            {{ customer.type === 'company' ? 'Entreprise' : 'Particulier' }}
                        </span>
                        <span :class="getStatusClass(customer.status)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                            {{ getStatusLabel(customer.status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Détails du client</h3>
                </div>
                <div class="p-6">
                    <!-- Individual Info -->
                    <div v-if="customer.type === 'individual'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Prénom</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.first_name || 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nom</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.last_name || 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date de naissance</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.date_of_birth || 'Non renseignée' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div v-if="customer.type === 'company'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Raison sociale</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.company_name || 'Non renseignée' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">SIRET</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.siret || 'Non renseigné' }}</p>
                            </div>
                            <div class="md:col-span-3">
                                <p class="text-sm font-medium text-gray-500">Numéro de TVA</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.vat_number || 'Non renseigné' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="pt-6 border-t border-gray-200 mt-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Contact</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                <p class="mt-1 text-sm text-gray-900">{{ customer.phone || 'Non renseigné' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Info -->
                    <div class="pt-6 border-t border-gray-200 mt-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Adresse</h4>
                        <div class="grid grid-cols-1 gap-2">
                            <p class="text-sm text-gray-900">{{ customer.address }}</p>
                            <p class="text-sm text-gray-900">{{ customer.postal_code }} {{ customer.city }}</p>
                            <p class="text-sm text-gray-900">{{ customer.country }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Contracts -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Contrats ({{ customer.contracts.length }})</h3>
                </div>
                <div v-if="customer.contracts.length > 0" class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Box</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="contract in customer.contracts" :key="contract.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ contract.contract_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contract.box.number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ contract.box.floor.building.site.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ formatDate(contract.start_date) }}</div>
                                    <div class="text-xs text-gray-500">au {{ formatDate(contract.end_date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ formatCurrency(contract.monthly_amount) }}/mois
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getContractStatusClass(contract.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ getContractStatusLabel(contract.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="`/contracts/${contract.id}`" class="text-indigo-600 hover:text-indigo-900">
                                        Voir
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="p-6 text-center text-sm text-gray-500">
                    Aucun contrat pour le moment
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    customer: {
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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        suspended: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        active: 'Actif',
        inactive: 'Inactif',
        suspended: 'Suspendu',
    };
    return labels[status] || status;
};

const getContractStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        pending: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        expired: 'bg-orange-100 text-orange-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getContractStatusLabel = (status) => {
    const labels = {
        draft: 'Brouillon',
        pending: 'En attente',
        active: 'Actif',
        expired: 'Expiré',
        cancelled: 'Annulé',
    };
    return labels[status] || status;
};
</script>
