<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'contract', 'payment', 'sepa', 'signature', 'insurance'].includes(value)
    },
    size: {
        type: String,
        default: 'medium',
        validator: (value) => ['small', 'medium', 'large'].includes(value)
    },
    withIcon: {
        type: Boolean,
        default: false
    }
});

// Define status configurations for different types
const statusConfigs = {
    default: {
        active: { color: 'bg-green-100 text-green-800', label: 'Actif', icon: '✓' },
        inactive: { color: 'bg-gray-100 text-gray-800', label: 'Inactif', icon: '○' },
        pending: { color: 'bg-yellow-100 text-yellow-800', label: 'En attente', icon: '⏳' },
        cancelled: { color: 'bg-red-100 text-red-800', label: 'Annulé', icon: '✗' },
        expired: { color: 'bg-gray-100 text-gray-800', label: 'Expiré', icon: '⊘' }
    },
    contract: {
        active: { color: 'bg-green-100 text-green-800', label: 'Actif', icon: '✓' },
        pending: { color: 'bg-yellow-100 text-yellow-800', label: 'En attente', icon: '⏳' },
        cancelled: { color: 'bg-red-100 text-red-800', label: 'Annulé', icon: '✗' },
        expired: { color: 'bg-gray-100 text-gray-800', label: 'Expiré', icon: '⊘' },
        suspended: { color: 'bg-orange-100 text-orange-800', label: 'Suspendu', icon: '⏸' }
    },
    payment: {
        paid: { color: 'bg-green-100 text-green-800', label: 'Payé', icon: '✓' },
        unpaid: { color: 'bg-red-100 text-red-800', label: 'Impayé', icon: '!' },
        partial: { color: 'bg-yellow-100 text-yellow-800', label: 'Partiel', icon: '◐' },
        overdue: { color: 'bg-orange-100 text-orange-800', label: 'En retard', icon: '⚠' },
        pending: { color: 'bg-blue-100 text-blue-800', label: 'En attente', icon: '⏳' }
    },
    sepa: {
        pending: { color: 'bg-blue-100 text-blue-800', label: 'En attente', icon: '⏳' },
        submitted: { color: 'bg-indigo-100 text-indigo-800', label: 'Soumis', icon: '↗' },
        confirmed: { color: 'bg-cyan-100 text-cyan-800', label: 'Confirmé', icon: '✓' },
        completed: { color: 'bg-green-100 text-green-800', label: 'Complété', icon: '✓✓' },
        failed: { color: 'bg-red-100 text-red-800', label: 'Échoué', icon: '✗' },
        rejected: { color: 'bg-orange-100 text-orange-800', label: 'Rejeté', icon: '⊗' },
        cancelled: { color: 'bg-gray-100 text-gray-800', label: 'Annulé', icon: '⊘' }
    },
    signature: {
        pending: { color: 'bg-yellow-100 text-yellow-800', label: 'En attente', icon: '✍' },
        signed: { color: 'bg-green-100 text-green-800', label: 'Signé', icon: '✓' },
        rejected: { color: 'bg-red-100 text-red-800', label: 'Refusé', icon: '✗' },
        expired: { color: 'bg-gray-100 text-gray-800', label: 'Expiré', icon: '⊘' }
    },
    insurance: {
        active: { color: 'bg-green-100 text-green-800', label: 'Actif', icon: '🛡' },
        cancelled: { color: 'bg-red-100 text-red-800', label: 'Annulé', icon: '✗' },
        suspended: { color: 'bg-orange-100 text-orange-800', label: 'Suspendu', icon: '⏸' },
        expired: { color: 'bg-gray-100 text-gray-800', label: 'Expiré', icon: '⊘' }
    }
};

// Size configurations
const sizeConfigs = {
    small: 'px-2 py-0.5 text-xs',
    medium: 'px-2.5 py-1 text-xs',
    large: 'px-3 py-1.5 text-sm'
};

// Get current status configuration
const currentConfig = computed(() => {
    const typeConfig = statusConfigs[props.type] || statusConfigs.default;
    const statusLower = props.status.toLowerCase();
    return typeConfig[statusLower] || {
        color: 'bg-gray-100 text-gray-800',
        label: props.status,
        icon: '?'
    };
});

// Get badge classes
const badgeClasses = computed(() => {
    return [
        currentConfig.value.color,
        sizeConfigs[props.size],
        'inline-flex items-center font-semibold rounded-full',
        'transition-all duration-150'
    ].join(' ');
});
</script>

<template>
    <span :class="badgeClasses">
        <span v-if="withIcon" class="mr-1">{{ currentConfig.icon }}</span>
        {{ currentConfig.label }}
    </span>
</template>
