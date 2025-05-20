import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import { LANGUAGES } from './languages'; // Import your language definitions

// --- Placeholder Translation Resources --- 
// We will populate these properly later
const resources = {
  en: {
    translation: {
      "loginButton": "Login",
      "profileButton": "Profile",
      "aboutButton": "About",
      "likesButton": "Likes",
      "languageButton": "Language"
    }
  },
  ru: {
    translation: {
      "loginButton": "Войти",
      "profileButton": "Профиль",
      "aboutButton": "О нас",
      "likesButton": "Понравилось",
      "languageButton": "Язык"
    }
  },
  tg: {
    translation: {
      "loginButton": "Даромадан",
      "profileButton": "Профил",
      "aboutButton": "Дар бораи мо",
      "likesButton": "Лайкҳо",
      "languageButton": "Забон"
    }
  }
};

const initialLangId = localStorage.getItem('lang') || LANGUAGES[0].id;

i18n
  .use(initReactI18next) // passes i18n down to react-i18next
  .init({
    resources,
    lng: initialLangId, // language to use
    fallbackLng: 'en', // use en if detected lng is not available
    supportedLngs: LANGUAGES.map(lang => lang.id), // Explicitly list supported languages

    interpolation: {
      escapeValue: false // react already safes from xss
    },
    
    // Optional: Enable debug logging in development
    // debug: import.meta.env.DEV
  });

export default i18n; 