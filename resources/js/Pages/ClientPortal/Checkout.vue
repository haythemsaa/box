<template>
    <ClientPortalLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $t('payment.secureCheckout') }}</h1>
                    <p class="mt-2 text-sm text-gray-600">{{ $t('payment.completePayment') }}</p>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Order Summary (Left Column) -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('payment.orderSummary') }}</h2>

                            <!-- Invoice Details -->
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $t('invoices.invoiceNumber') }}</span>
                                    <span class="font-medium text-gray-900">{{ invoice.invoice_number }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $t('invoices.issueDate') }}</span>
                                    <span class="text-gray-900">{{ formatDate(invoice.issue_date) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $t('invoices.dueDate') }}</span>
                                    <span class="text-gray-900">{{ formatDate(invoice.due_date) }}</span>
                                </div>
                            </div>

                            <!-- Box Information -->
                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ $t('boxes.box') }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ invoice.contract?.box?.box_number }}<br>
                                    {{ invoice.contract?.box?.site?.name }}
                                </p>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="border-t border-gray-200 pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $t('invoices.subtotal') }}</span>
                                    <span class="text-gray-900">{{ formatCurrency(invoice.subtotal_amount) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $t('invoices.vat') }} ({{ invoice.vat_rate }}%)</span>
                                    <span class="text-gray-900">{{ formatCurrency(invoice.vat_amount) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                    <span class="text-gray-900">{{ $t('invoices.total') }}</span>
                                    <span class="text-indigo-600">{{ formatCurrency(invoice.total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form (Right Column) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-6">{{ $t('payment.paymentMethod') }}</h2>

                            <!-- Loading State -->
                            <div v-if="loading" class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                            </div>

                            <!-- Payment Form -->
                            <form v-else @submit.prevent="handleSubmit" id="payment-form">
                                <!-- Cardholder Name -->
                                <div class="mb-4">
                                    <label for="cardholder-name" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $t('payment.cardholderName') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="cardholder-name"
                                        v-model="cardholderName"
                                        required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :placeholder="invoice.customer?.first_name + ' ' + invoice.customer?.last_name"
                                    />
                                </div>

                                <!-- Stripe Card Element -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $t('payment.cardDetails') }}
                                    </label>
                                    <div
                                        id="card-element"
                                        class="p-3 border border-gray-300 rounded-md shadow-sm"
                                    ></div>
                                    <div v-if="cardError" class="mt-2 text-sm text-red-600">
                                        {{ cardError }}
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-800">{{ errorMessage }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Secure Payment Notice -->
                                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-800">{{ $t('payment.securePaymentNotice') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="!processing">
                                        {{ $t('payment.payNow', { amount: formatCurrency(invoice.total_amount) }) }}
                                    </span>
                                    <span v-else class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ $t('payment.processing') }}
                                    </span>
                                </button>

                                <!-- Cancel Link -->
                                <div class="mt-4 text-center">
                                    <Link
                                        :href="route('client.invoices')"
                                        class="text-sm text-gray-600 hover:text-gray-900"
                                    >
                                        {{ $t('common.cancel') }}
                                    </Link>
                                </div>
                            </form>
                        </div>

                        <!-- Powered by Stripe -->
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500">
                                Powered by <span class="font-semibold">Stripe</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientPortalLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import ClientPortalLayout from '@/Layouts/ClientPortalLayout.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    invoice: Object,
    stripeKey: String,
});

// State
const loading = ref(true);
const processing = ref(false);
const cardholderName = ref(props.invoice.customer?.first_name + ' ' + props.invoice.customer?.last_name || '');
const cardError = ref('');
const errorMessage = ref('');

// Stripe instances
let stripe = null;
let elements = null;
let cardElement = null;

// Initialize Stripe
onMounted(async () => {
    try {
        // Load Stripe.js
        if (!window.Stripe) {
            const script = document.createElement('script');
            script.src = 'https://js.stripe.com/v3/';
            script.async = true;
            document.head.appendChild(script);

            await new Promise((resolve, reject) => {
                script.onload = resolve;
                script.onerror = reject;
            });
        }

        // Initialize Stripe
        stripe = window.Stripe(props.stripeKey);

        // Create Elements instance
        elements = stripe.elements();

        // Create Card Element
        cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#9e2146',
                },
            },
        });

        // Mount Card Element
        cardElement.mount('#card-element');

        // Handle real-time validation errors
        cardElement.on('change', (event) => {
            if (event.error) {
                cardError.value = event.error.message;
            } else {
                cardError.value = '';
            }
        });

        loading.value = false;
    } catch (error) {
        console.error('Error initializing Stripe:', error);
        errorMessage.value = t('payment.initializationError');
        loading.value = false;
    }
});

// Handle form submission
const handleSubmit = async () => {
    if (processing.value) return;

    processing.value = true;
    errorMessage.value = '';
    cardError.value = '';

    try {
        // Create payment intent
        const response = await fetch(route('client.invoices.payment-intent', props.invoice.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Failed to create payment intent');
        }

        // Confirm payment with Stripe
        const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: cardholderName.value,
                    email: props.invoice.customer?.email,
                },
            },
        });

        if (error) {
            throw new Error(error.message);
        }

        if (paymentIntent.status === 'succeeded') {
            // Redirect to confirmation
            router.post(route('client.payments.confirm'), {
                payment_intent_id: paymentIntent.id,
                invoice_id: props.invoice.id,
            });
        }
    } catch (error) {
        console.error('Payment error:', error);
        errorMessage.value = error.message || t('payment.processingError');
        processing.value = false;
    }
};

// Format currency
const formatCurrency = (amount) => {
    const currency = props.invoice.currency?.code || 'EUR';
    const symbol = props.invoice.currency?.symbol || '€';
    return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount) + ' ' + symbol;
};

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>
