<template>
    <ClientPortalLayout>
        <template #header>
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $t('clientPortal.welcome') }}, {{ customer.first_name || customer.company_name }}
            </h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Contracts -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            {{ $t('clientPortal.totalContracts') }}
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ stats.totalContracts }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Contracts -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            {{ $t('clientPortal.activeContracts') }}
                                        </dt>
                                        <dd class="text-lg font-semibold text-green-600">
                                            {{ stats.activeContracts }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Total -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            {{ $t('clientPortal.monthlyTotal') }}
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ formatCurrency(stats.monthlyTotal) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Unpaid Invoices -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            {{ $t('clientPortal.unpaidInvoices') }}
                                        </dt>
                                        <dd class="text-lg font-semibold text-red-600">
                                            {{ formatCurrency(stats.unpaidInvoices) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Contracts -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            {{ $t('clientPortal.myBoxes') }}
                        </h3>
                        <div v-if="activeContracts.length > 0" class="space-y-4">
                            <div v-for="contract in activeContracts" :key="contract.id"
                                 class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                Box {{ contract.box.number }}
                                            </h4>
                                            <span :class="{
                                                'bg-green-100 text-green-800': contract.status === 'active',
                                                'bg-yellow-100 text-yellow-800': contract.status === 'pending'
                                            }" class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ $t(`status.${contract.status}`) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ contract.box.site.name }} -
                                            {{ $t('boxes.building') }} {{ contract.box.building.name }},
                                            {{ $t('boxes.floor') }} {{ contract.box.floor.number }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-2">
                                            {{ contract.box.length }}m × {{ contract.box.width }}m × {{ contract.box.height }}m
                                            ({{ contract.box.volume }}m³)
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-indigo-600">
                                            {{ formatCurrency(contract.monthly_amount) }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $t('common.per_month') }}</p>
                                        <Link :href="`/client/contracts/${contract.id}`"
                                              class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                                            {{ $t('common.view') }} →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            {{ $t('clientPortal.noActiveContracts') }}
                        </div>
                    </div>
                </div>

                <!-- Recent Activity - 2 columns -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Recent Invoices -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $t('clientPortal.recentInvoices') }}
                                </h3>
                                <Link href="/client/invoices" class="text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ $t('common.view') }} {{ $t('common.all') }}
                                </Link>
                            </div>
                            <div v-if="recentInvoices.length > 0" class="space-y-3">
                                <div v-for="invoice in recentInvoices" :key="invoice.id"
                                     class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ invoice.invoice_number }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(invoice.issue_date) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900">{{ formatCurrency(invoice.total_amount) }}</p>
                                        <span :class="{
                                            'text-green-600': invoice.status === 'paid',
                                            'text-red-600': invoice.status === 'unpaid',
                                            'text-yellow-600': invoice.status === 'overdue'
                                        }" class="text-xs font-medium">
                                            {{ $t(`status.${invoice.status}`) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4 text-sm text-gray-500">
                                {{ $t('contracts.no_invoices') }}
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $t('clientPortal.recentPayments') }}
                                </h3>
                                <Link href="/client/payments" class="text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ $t('common.view') }} {{ $t('common.all') }}
                                </Link>
                            </div>
                            <div v-if="recentPayments.length > 0" class="space-y-3">
                                <div v-for="payment in recentPayments" :key="payment.id"
                                     class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ payment.payment_method }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(payment.payment_date) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-green-600">{{ formatCurrency(payment.amount) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4 text-sm text-gray-500">
                                {{ $t('contracts.no_payments') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientPortalLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ClientPortalLayout from '@/Layouts/ClientPortalLayout.vue';

const props = defineProps({
    customer: Object,
    activeContracts: Array,
    recentInvoices: Array,
    recentPayments: Array,
    stats: Object,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};
</script>
