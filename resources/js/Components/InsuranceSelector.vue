<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Produits d'assurance</h3>
            <div v-if="totalMonthlyPremium > 0" class="text-sm text-gray-600">
                Total mensuel: <span class="font-semibold text-indigo-600">{{ formatCurrency(totalMonthlyPremium) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="product in insuranceProducts"
                :key="product.id"
                class="relative border rounded-lg p-4 transition-all duration-200"
                :class="{
                    'border-indigo-500 bg-indigo-50': isSelected(product.id),
                    'border-gray-300 bg-white hover:border-gray-400': !isSelected(product.id),
                    'border-red-500 bg-red-50': product.is_mandatory
                }"
            >
                <!-- Checkbox -->
                <div class="flex items-start">
                    <input
                        :id="`insurance-${product.id}`"
                        type="checkbox"
                        :value="product.id"
                        :checked="isSelected(product.id)"
                        :disabled="product.is_mandatory"
                        @change="toggleInsurance(product.id)"
                        class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        :class="{ 'opacity-50 cursor-not-allowed': product.is_mandatory }"
                    />
                    <label
                        :for="`insurance-${product.id}`"
                        class="ml-3 flex-1 cursor-pointer"
                        :class="{ 'cursor-not-allowed': product.is_mandatory }"
                    >
                        <!-- Product Name & Badge -->
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ product.name }}</span>
                            <span
                                v-if="product.is_mandatory"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"
                            >
                                Obligatoire
                            </span>
                        </div>

                        <!-- Description -->
                        <p v-if="product.description" class="mt-1 text-sm text-gray-600">
                            {{ product.description }}
                        </p>

                        <!-- Pricing -->
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-lg font-semibold text-gray-900">
                                {{ formatCurrency(product.monthly_price) }}
                            </span>
                            <span class="text-sm text-gray-500">/mois</span>
                        </div>

                        <!-- Yearly Savings -->
                        <div v-if="product.yearly_price && showYearlySavings" class="mt-1 text-xs text-green-600">
                            Économisez {{ formatCurrency(calculateYearlySavings(product)) }}/an avec paiement annuel
                        </div>

                        <!-- Coverage Amount -->
                        <div class="mt-2 text-sm text-gray-700">
                            <span class="font-medium">Couverture:</span>
                            {{ formatCurrency(product.max_coverage_amount) }}
                        </div>

                        <!-- Coverage Details (collapsed by default) -->
                        <div v-if="showDetails && product.coverage_details" class="mt-2">
                            <button
                                type="button"
                                @click.prevent="toggleDetails(product.id)"
                                class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center"
                            >
                                <svg
                                    class="w-4 h-4 mr-1 transition-transform"
                                    :class="{ 'rotate-90': expandedDetails[product.id] }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                Détails garanties
                            </button>
                            <div v-if="expandedDetails[product.id]" class="mt-2 text-xs text-gray-600 space-y-1">
                                <div class="bg-green-50 p-2 rounded">
                                    <div class="font-medium text-green-800 mb-1">✓ Garanties incluses:</div>
                                    <div class="whitespace-pre-line">{{ product.coverage_details }}</div>
                                </div>
                                <div v-if="product.exclusions" class="bg-red-50 p-2 rounded">
                                    <div class="font-medium text-red-800 mb-1">✗ Exclusions:</div>
                                    <div class="whitespace-pre-line">{{ product.exclusions }}</div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Mandatory Info -->
                <div v-if="product.is_mandatory" class="mt-3 text-xs text-red-600 bg-red-50 p-2 rounded">
                    ℹ Cette assurance est obligatoire et sera automatiquement ajoutée au contrat.
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-if="insuranceProducts.length === 0"
            class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200"
        >
            <p class="text-gray-500">Aucun produit d'assurance disponible pour le moment.</p>
        </div>

        <!-- Summary -->
        <div v-if="selectedCount > 0" class="mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-medium text-gray-900">{{ selectedCount }} assurance(s) sélectionnée(s)</span>
                    <span v-if="mandatoryCount > 0" class="ml-2 text-sm text-gray-600">
                        (dont {{ mandatoryCount }} obligatoire(s))
                    </span>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Total mensuel des primes</div>
                    <div class="text-xl font-bold text-indigo-600">{{ formatCurrency(totalMonthlyPremium) }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue';

const props = defineProps({
    insuranceProducts: {
        type: Array,
        required: true,
        default: () => [],
    },
    mandatoryInsurances: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: Array,
        default: () => [],
    },
    showYearlySavings: {
        type: Boolean,
        default: true,
    },
    showDetails: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const selectedInsurances = ref([...props.modelValue]);
const expandedDetails = ref({});

// Auto-select mandatory insurances on mount
onMounted(() => {
    if (props.mandatoryInsurances.length > 0) {
        const allMandatory = [...new Set([...selectedInsurances.value, ...props.mandatoryInsurances])];
        if (JSON.stringify(allMandatory.sort()) !== JSON.stringify(selectedInsurances.value.sort())) {
            selectedInsurances.value = allMandatory;
            emit('update:modelValue', selectedInsurances.value);
        }
    }
});

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
    selectedInsurances.value = [...newValue];
}, { deep: true });

// Watch for mandatory changes
watch(() => props.mandatoryInsurances, (newMandatory) => {
    const combined = [...new Set([...selectedInsurances.value, ...newMandatory])];
    if (JSON.stringify(combined.sort()) !== JSON.stringify(selectedInsurances.value.sort())) {
        selectedInsurances.value = combined;
        emit('update:modelValue', selectedInsurances.value);
    }
}, { deep: true });

const isSelected = (productId) => {
    return selectedInsurances.value.includes(productId);
};

const toggleInsurance = (productId) => {
    if (props.mandatoryInsurances.includes(productId)) {
        return; // Cannot toggle mandatory insurances
    }

    const index = selectedInsurances.value.indexOf(productId);
    if (index > -1) {
        selectedInsurances.value.splice(index, 1);
    } else {
        selectedInsurances.value.push(productId);
    }

    emit('update:modelValue', selectedInsurances.value);
};

const toggleDetails = (productId) => {
    expandedDetails.value[productId] = !expandedDetails.value[productId];
};

const selectedCount = computed(() => {
    return selectedInsurances.value.length;
});

const mandatoryCount = computed(() => {
    return props.mandatoryInsurances.length;
});

const totalMonthlyPremium = computed(() => {
    return selectedInsurances.value.reduce((total, productId) => {
        const product = props.insuranceProducts.find(p => p.id === productId);
        return total + (product ? product.monthly_price : 0);
    }, 0);
});

const calculateYearlySavings = (product) => {
    if (!product.yearly_price) return 0;
    return (product.monthly_price * 12) - product.yearly_price;
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};
</script>
