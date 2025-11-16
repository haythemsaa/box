<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: [String, Number],
        required: true
    },
    label: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'cyan',
        validator: (value) => ['cyan', 'teal', 'blue', 'green', 'yellow', 'orange', 'red', 'purple', 'indigo', 'pink'].includes(value)
    },
    icon: {
        type: String,
        default: ''
    },
    trend: {
        type: Object,
        default: null
        // Example: { direction: 'up', value: '12%', label: 'vs mois dernier' }
    },
    clickable: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['click']);

const gradientClasses = computed(() => {
    const gradients = {
        cyan: 'from-cyan-500 to-cyan-600',
        teal: 'from-teal-500 to-teal-600',
        blue: 'from-blue-500 to-blue-600',
        green: 'from-green-500 to-green-600',
        yellow: 'from-yellow-500 to-yellow-600',
        orange: 'from-orange-500 to-orange-600',
        red: 'from-red-500 to-red-600',
        purple: 'from-purple-500 to-purple-600',
        indigo: 'from-indigo-500 to-indigo-600',
        pink: 'from-pink-500 to-pink-600'
    };
    return gradients[props.color];
});

const handleClick = () => {
    if (props.clickable && !props.loading) {
        emit('click');
    }
};
</script>

<template>
    <div
        :class="[
            'bg-gradient-to-br rounded-lg shadow-md p-6 text-white transform transition-all duration-200',
            gradientClasses,
            clickable && !loading ? 'cursor-pointer hover:scale-105 hover:shadow-lg' : 'hover:shadow-lg'
        ]"
        @click="handleClick"
    >
        <!-- Loading State -->
        <div v-if="loading" class="animate-pulse">
            <div class="h-12 bg-white bg-opacity-30 rounded w-2/3 mb-2"></div>
            <div class="h-4 bg-white bg-opacity-20 rounded w-full"></div>
        </div>

        <!-- Content -->
        <div v-else>
            <!-- Icon (optional) -->
            <div v-if="icon" class="mb-3">
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
                </svg>
            </div>

            <!-- Value -->
            <div class="flex items-baseline justify-between">
                <div class="text-5xl font-bold mb-2">{{ value }}</div>

                <!-- Trend indicator (optional) -->
                <div v-if="trend" :class="[
                    'flex items-center text-sm font-medium',
                    trend.direction === 'up' ? 'text-green-200' : 'text-red-200'
                ]">
                    <svg
                        class="w-4 h-4 mr-1"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            v-if="trend.direction === 'up'"
                            fill-rule="evenodd"
                            d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                        <path
                            v-else
                            fill-rule="evenodd"
                            d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ trend.value }}
                </div>
            </div>

            <!-- Label -->
            <div class="text-sm opacity-90">{{ label }}</div>

            <!-- Trend label (optional) -->
            <div v-if="trend && trend.label" class="text-xs opacity-75 mt-1">
                {{ trend.label }}
            </div>

            <!-- Custom slot for additional content -->
            <div v-if="$slots.default" class="mt-3 pt-3 border-t border-white border-opacity-20">
                <slot />
            </div>
        </div>
    </div>
</template>
