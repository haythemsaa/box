<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link
                        :href="route('insurance-products.index')"
                        class="text-sm text-gray-600 hover:text-gray-900 mb-4 inline-flex items-center"
                    >
                        ← Retour aux produits d'assurance
                    </Link>
                    <h2 class="text-2xl font-semibold text-gray-900 mt-2">Nouveau Produit d'Assurance</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Créez un nouveau produit d'assurance pour vos contrats
                    </p>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-lg shadow">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de base</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nom du produit <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Ex: Assurance Standard"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Protection complète pour vos biens stockés..."
                                    />
                                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Coverage Details -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de couverture</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Max Coverage Amount -->
                                <div>
                                    <label for="max_coverage_amount" class="block text-sm font-medium text-gray-700">
                                        Couverture maximale (€) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="max_coverage_amount"
                                        v-model="form.max_coverage_amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="5000.00"
                                    />
                                    <p v-if="form.errors.max_coverage_amount" class="mt-1 text-sm text-red-600">{{ form.errors.max_coverage_amount }}</p>
                                </div>

                                <!-- Coverage Details -->
                                <div>
                                    <label for="coverage_details" class="block text-sm font-medium text-gray-700">
                                        Garanties incluses
                                    </label>
                                    <textarea
                                        id="coverage_details"
                                        v-model="form.coverage_details"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Vol avec effraction, Incendie, Dégâts des eaux..."
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Détaillez ce qui est couvert par cette assurance</p>
                                    <p v-if="form.errors.coverage_details" class="mt-1 text-sm text-red-600">{{ form.errors.coverage_details }}</p>
                                </div>

                                <!-- Exclusions -->
                                <div>
                                    <label for="exclusions" class="block text-sm font-medium text-gray-700">
                                        Exclusions
                                    </label>
                                    <textarea
                                        id="exclusions"
                                        v-model="form.exclusions"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Objets de valeur > 500€, Bijoux, Espèces..."
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Détaillez ce qui n'est PAS couvert</p>
                                    <p v-if="form.errors.exclusions" class="mt-1 text-sm text-red-600">{{ form.errors.exclusions }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Tarification</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Monthly Price -->
                                <div>
                                    <label for="monthly_price" class="block text-sm font-medium text-gray-700">
                                        Prix mensuel (€) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="monthly_price"
                                        v-model="form.monthly_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="9.90"
                                    />
                                    <p v-if="form.errors.monthly_price" class="mt-1 text-sm text-red-600">{{ form.errors.monthly_price }}</p>
                                </div>

                                <!-- Yearly Price -->
                                <div>
                                    <label for="yearly_price" class="block text-sm font-medium text-gray-700">
                                        Prix annuel (€)
                                    </label>
                                    <input
                                        id="yearly_price"
                                        v-model="form.yearly_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="99.00"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Optionnel - généralement avec réduction (2 mois offerts)</p>
                                    <p v-if="form.errors.yearly_price" class="mt-1 text-sm text-red-600">{{ form.errors.yearly_price }}</p>
                                </div>

                                <!-- Commission Rate -->
                                <div>
                                    <label for="commission_rate" class="block text-sm font-medium text-gray-700">
                                        Taux de commission (%) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="commission_rate"
                                        v-model="form.commission_rate"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="25.00"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Commission pour le site (généralement 20-40%)</p>
                                    <p v-if="form.errors.commission_rate" class="mt-1 text-sm text-red-600">{{ form.errors.commission_rate }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Statut et options</h3>
                            <div class="space-y-4">
                                <!-- Is Active -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="is_active"
                                            v-model="form.is_active"
                                            type="checkbox"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                        />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700">Produit actif</label>
                                        <p class="text-gray-500">Ce produit sera disponible pour les nouveaux contrats</p>
                                    </div>
                                </div>

                                <!-- Is Mandatory -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="is_mandatory"
                                            v-model="form.is_mandatory"
                                            type="checkbox"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                        />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_mandatory" class="font-medium text-gray-700">Assurance obligatoire</label>
                                        <p class="text-gray-500">Cette assurance sera automatiquement incluse dans tous les contrats</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-gray-200 pt-6 flex items-center justify-end gap-4">
                            <Link
                                :href="route('insurance-products.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                            >
                                Annuler
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Création...' : 'Créer le produit' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    tenant_id: null,
    name: '',
    description: '',
    max_coverage_amount: '',
    coverage_details: '',
    exclusions: '',
    monthly_price: '',
    yearly_price: '',
    commission_rate: 25.00,
    is_active: true,
    is_mandatory: false,
});

const submit = () => {
    form.post(route('insurance-products.store'), {
        preserveScroll: true,
    });
};
</script>
