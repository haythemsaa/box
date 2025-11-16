<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'card',
        validator: (value) => ['card', 'table', 'list', 'stats', 'custom'].includes(value)
    },
    rows: {
        type: Number,
        default: 3
    },
    columns: {
        type: Number,
        default: 4
    }
});
</script>

<template>
    <!-- Card Skeleton -->
    <div v-if="type === 'card'" class="animate-pulse">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
            <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-5/6 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-4/6"></div>
        </div>
    </div>

    <!-- Table Skeleton -->
    <div v-else-if="type === 'table'" class="animate-pulse">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gray-50 p-4 border-b border-gray-200">
                <div class="grid gap-4" :style="{ gridTemplateColumns: `repeat(${columns}, 1fr)` }">
                    <div v-for="i in columns" :key="`header-${i}`" class="h-4 bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Table Rows -->
            <div class="divide-y divide-gray-200">
                <div v-for="row in rows" :key="`row-${row}`" class="p-4">
                    <div class="grid gap-4" :style="{ gridTemplateColumns: `repeat(${columns}, 1fr)` }">
                        <div v-for="col in columns" :key="`cell-${row}-${col}`" class="h-3 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- List Skeleton -->
    <div v-else-if="type === 'list'" class="animate-pulse space-y-3">
        <div v-for="i in rows" :key="`list-${i}`" class="bg-white rounded-lg shadow-sm p-4 flex items-center space-x-4">
            <!-- Avatar/Icon -->
            <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>

            <!-- Content -->
            <div class="flex-1">
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>

            <!-- Action -->
            <div class="w-20 h-8 bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Stats/KPI Skeleton -->
    <div v-else-if="type === 'stats'" class="animate-pulse">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="i in columns" :key="`stat-${i}`" class="bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg shadow-md p-6">
                <div class="h-10 bg-gray-300 rounded w-1/2 mb-2"></div>
                <div class="h-3 bg-gray-300 rounded w-3/4"></div>
            </div>
        </div>
    </div>

    <!-- Custom Skeleton (use default slot) -->
    <div v-else-if="type === 'custom'" class="animate-pulse">
        <slot>
            <!-- Default custom skeleton -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="h-6 bg-gray-200 rounded w-1/4 mb-6"></div>
                <div class="space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    <div class="h-4 bg-gray-200 rounded w-4/6"></div>
                </div>
            </div>
        </slot>
    </div>

    <!-- Spinner (alternative to skeleton) -->
    <div v-else class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cyan-600"></div>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
