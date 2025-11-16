import { createI18n } from 'vue-i18n';
import fr from './lang/fr.json';
import en from './lang/en.json';
import nl from './lang/nl.json';

const messages = {
    fr,
    en,
    nl,
};

// Get browser language or default to French
const getBrowserLanguage = () => {
    const stored = localStorage.getItem('language');
    if (stored && ['fr', 'en', 'nl'].includes(stored)) {
        return stored;
    }

    const browserLang = navigator.language.split('-')[0];
    return ['fr', 'en', 'nl'].includes(browserLang) ? browserLang : 'fr';
};

const i18n = createI18n({
    legacy: false,
    locale: getBrowserLanguage(),
    fallbackLocale: 'fr',
    messages,
    globalInjection: true,
});

export default i18n;
