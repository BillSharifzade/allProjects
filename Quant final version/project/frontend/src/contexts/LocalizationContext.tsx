import { createContext, useContext, useState, ReactNode, useEffect } from "react";
import { LANGUAGES } from "../languages";

export interface Language {
  id: string;
  name: string;
  flag: string;
  api: string;
  article: string;
}

interface LocalizationContextType {
  currentLanguage: Language;
  setLanguage: (id: string) => void;
  languages: Language[];
}

const LocalizationContext = createContext<LocalizationContextType | undefined>(undefined);

export function LocalizationProvider({ children }: { children: ReactNode }) {
  const [currentLanguage, setCurrentLanguage] = useState<Language>(() => {
    // Default to first language if localStorage is not available (SSR)
    if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
      return LANGUAGES[0];
    }
    
    const savedLanguage = localStorage.getItem("quantLanguage");
    return savedLanguage 
      ? LANGUAGES.find(lang => lang.id === savedLanguage) || LANGUAGES[0]
      : LANGUAGES[0];
  });

  // Update localStorage when language changes
  useEffect(() => {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem("quantLanguage", currentLanguage.id);
    }
  }, [currentLanguage]);

  const setLanguage = (id: string) => {
    const language = LANGUAGES.find(lang => lang.id === id);
    if (language) {
      setCurrentLanguage(language);
    }
  };

  return (
    <LocalizationContext.Provider value={{ 
      currentLanguage, 
      setLanguage,
      languages: LANGUAGES
    }}>
      {children}
    </LocalizationContext.Provider>
  );
}

export function useLocalization() {
  const context = useContext(LocalizationContext);
  if (context === undefined) {
    throw new Error("useLocalization must be used within a LocalizationProvider");
  }
  return context;
}