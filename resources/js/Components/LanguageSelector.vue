<template>
    <div class="relative">
        <button
            @click="showDropdown = !showDropdown"
            class="flex items-center space-x-1 px-3 py-2 text-sm text-gray-700 hover:text-gray-900 rounded-md hover:bg-gray-100 transition"
        >
            <span class="text-lg">{{ currentFlag }}</span>
            <span class="hidden sm:inline">{{ currentLanguage }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            v-show="showDropdown"
            @click.away="showDropdown = false"
            class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        >
            <div class="py-1">
                <button
                    v-for="lang in languages"
                    :key="lang.code"
                    @click="changeLanguage(lang.code)"
                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition"
                    :class="{ 'bg-gray-50 font-medium': currentLocale === lang.code }"
                >
                    <span class="text-lg mr-2">{{ lang.flag }}</span>
                    <span>{{ lang.name }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const showDropdown = ref(false);

const languages = [
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
    { code: 'en', name: 'English', flag: '🇬🇧' },
    { code: 'nl', name: 'Nederlands', flag: '🇳🇱' },
];

const currentLocale = computed(() => locale.value);

const currentFlag = computed(() => {
    const lang = languages.find(l => l.code === locale.value);
    return lang ? lang.flag : '🌐';
});

const currentLanguage = computed(() => {
    const lang = languages.find(l => l.code === locale.value);
    return lang ? lang.name : 'Language';
});

const changeLanguage = (code) => {
    locale.value = code;
    localStorage.setItem('language', code);
    showDropdown.value = false;
};
</script>
