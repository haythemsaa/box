<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    icon: {
        type: String,
        default: 'document',
        validator: (value) => ['document', 'folder', 'user', 'shield', 'chart', 'inbox', 'clipboard'].includes(value)
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    actionText: {
        type: String,
        default: ''
    },
    actionHref: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'medium',
        validator: (value) => ['small', 'medium', 'large'].includes(value)
    }
});

const emit = defineEmits(['action']);

const icons = {
    document: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    folder: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
    user: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    shield: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    chart: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    inbox: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    clipboard: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
};

const sizes = {
    small: {
        icon: 'w-12 h-12',
        title: 'text-base',
        description: 'text-xs',
        padding: 'py-6'
    },
    medium: {
        icon: 'w-16 h-16',
        title: 'text-lg',
        description: 'text-sm',
        padding: 'py-8'
    },
    large: {
        icon: 'w-20 h-20',
        title: 'text-xl',
        description: 'text-base',
        padding: 'py-12'
    }
};

const handleAction = () => {
    if (props.actionHref) {
        return; // Let Link component handle navigation
    }
    emit('action');
};
</script>

<template>
    <div :class="['text-center text-gray-500', sizes[size].padding]">
        <!-- Icon -->
        <svg
            :class="['mx-auto mb-3 text-gray-300', sizes[size].icon]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                :d="icons[icon]"
            />
        </svg>

        <!-- Title -->
        <p :class="['font-medium text-gray-700', sizes[size].title]">
            {{ title }}
        </p>

        <!-- Description -->
        <p
            v-if="description"
            :class="['mt-1 text-gray-500', sizes[size].description]"
        >
            {{ description }}
        </p>

        <!-- Default Slot (for custom content) -->
        <div v-if="$slots.default" class="mt-3">
            <slot />
        </div>

        <!-- Action Button -->
        <Link
            v-if="actionText && actionHref"
            :href="actionHref"
            class="inline-block mt-4 px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-colors duration-150 font-medium text-sm"
        >
            {{ actionText }}
        </Link>

        <button
            v-else-if="actionText"
            @click="handleAction"
            class="inline-block mt-4 px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-colors duration-150 font-medium text-sm"
        >
            {{ actionText }}
        </button>
    </div>
</template>
