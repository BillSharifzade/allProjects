import { useEffect, useCallback } from "react";
import { useTranslation } from 'react-i18next';
import { LANGUAGES } from "../languages";

export function useLocalization() {
  // Get the i18n instance and t function from react-i18next
  const { i18n } = useTranslation();

  // Function to change the language
  const setLanguage = useCallback((languageId: string) => {
    const newLanguage = LANGUAGES.find((lang) => lang.id === languageId);
    if (newLanguage && i18n.language !== languageId) {
      console.log(`[useLocalization] Changing language to: ${languageId}`);
      // Change language using i18next
      i18n.changeLanguage(languageId).then(() => {
        // Update localStorage *after* successful change
        localStorage.setItem("lang", languageId);
        // No reload needed, i18next provider/hooks handle UI updates
        console.log(`[useLocalization] Language changed successfully.`);
        // Note: Article fetching logic might need adjustment elsewhere
        // if it solely relies on window reload for language change.
        // We might need to explicitly re-trigger fetches.
         window.location.reload(); // KEEPING RELOAD FOR NOW to ensure article fetching updates
      }).catch(err => {
        console.error("[useLocalization] Error changing language:", err);
      });
    } else if (!newLanguage) {
      console.warn(`[useLocalization] Attempted to set invalid language: ${languageId}`);
    }
  }, [i18n]); // Depend on i18n instance

  // The current language object, derived from i18n state
  const currentLanguage = LANGUAGES.find(lang => lang.id === i18n.language) || LANGUAGES[0];

  // Optional: Sync localStorage if language changes via other means (e.g., browser detector)
  useEffect(() => {
    const handleLanguageChanged = (lng: string) => {
      if (localStorage.getItem('lang') !== lng) {
        localStorage.setItem('lang', lng);
      }
    };
    i18n.on('languageChanged', handleLanguageChanged);
    return () => {
      i18n.off('languageChanged', handleLanguageChanged);
    };
  }, [i18n]);

  return {
    // Provide the language object derived from i18n state
    currentLanguage,
    // Provide the function to change the language via i18n
    setLanguage,
    // Optionally expose i18n instance or t function if needed directly
    // i18n,
    // t 
  };
}
