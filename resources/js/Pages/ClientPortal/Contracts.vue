<template>
    <ClientPortalLayout>
        <template #header>
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $t('nav.contracts') }}
            </h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="mb-6 bg-white shadow rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ $t('common.search') }}</label>
                            <input v-model="searchForm.search" type="text"
                                   :placeholder="$t('contracts.contract_number')"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ $t('contracts.status') }}</label>
                            <select v-model="searchForm.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all">{{ $t('common.all') }}</option>
                                <option value="draft">{{ $t('contracts.draft') }}</option>
                                <option value="pending">{{ $t('contracts.pending') }}</option>
                                <option value="active">{{ $t('contracts.active') }}</option>
                                <option value="expired">{{ $t('contracts.expired') }}</option>
                                <option value="cancelled">{{ $t('contracts.cancelled') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button @click="search"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $t('common.search') }}
                        </button>
                    </div>
                </div>

                <!-- Contracts List -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        <div v-for="contract in contracts.data" :key="contract.id"
                             class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ contract.contract_number }}
                                        </h3>
                                        <span :class="{
                                            'bg-gray-100 text-gray-800': contract.status === 'draft',
                                            'bg-yellow-100 text-yellow-800': contract.status === 'pending',
                                            'bg-green-100 text-green-800': contract.status === 'active',
                                            'bg-red-100 text-red-800': contract.status === 'expired',
                                            'bg-gray-100 text-gray-600': contract.status === 'cancelled'
                                        }" class="px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ $t(`contracts.${contract.status}`) }}
                                        </span>
                                    </div>

                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $t('contracts.box') }}</p>
                                            <p class="text-sm font-medium text-gray-900">
                                                Box {{ contract.box.number }} - {{ contract.box.site.name }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $t('contracts.period') }}</p>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ formatDate(contract.start_date) }} → {{ formatDate(contract.end_date) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="ml-4 text-right">
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

                        <div v-if="contracts.data.length === 0" class="p-6 text-center text-gray-500">
                            {{ $t('common.no_results') }}
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="contracts.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                {{ $t('common.showing') }}
                                <span class="font-medium">{{ contracts.from }}</span>
                                {{ $t('common.to') }}
                                <span class="font-medium">{{ contracts.to }}</span>
                                {{ $t('common.of') }}
                                <span class="font-medium">{{ contracts.total }}</span>
                            </div>
                            <div class="flex space-x-2">
                                <Link v-if="contracts.prev_page_url" :href="contracts.prev_page_url"
                                      class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    {{ $t('common.previous') }}
                                </Link>
                                <Link v-if="contracts.next_page_url" :href="contracts.next_page_url"
                                      class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    {{ $t('common.next') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientPortalLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import ClientPortalLayout from '@/Layouts/ClientPortalLayout.vue';

const props = defineProps({
    customer: Object,
    contracts: Object,
    filters: Object,
});

const searchForm = ref({
    search: props.filters.search || '',
    status: props.filters.status || 'all',
});

const search = () => {
    router.get('/client/contracts', searchForm.value, { preserveState: true });
};

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
