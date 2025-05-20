import { useState, useEffect, useRef } from "react";
import { LANGUAGES } from "../languages";
import { useLocalization } from "../hooks/useLocalization";

export function LanguageSelector() {
  const [showDropdown, setShowDropdown] = useState(false);
  const { setLanguage } = useLocalization();
  const dropdownRef = useRef<HTMLDivElement>(null);

  const handleClickOutside = (event: MouseEvent) => {
    if (
      dropdownRef.current &&
      !dropdownRef.current.contains(event.target as Node)
    ) {
      setShowDropdown(false);
    }
  };

  useEffect(() => {
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  return (
    <div
      className="relative inline-flex items-center"
      ref={dropdownRef}
    >
      <button 
        onClick={() => setShowDropdown(!showDropdown)}
        className="font-chakra font-medium px-4 py-1.5 text-base border border-purple-500/50 text-purple-300 hover:bg-purple-500/10 hover:border-purple-500/90 rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-black"
      >
        Language
      </button>

      {showDropdown && (
        <div className="absolute overflow-y-auto max-h-[205px] py-2 w-40 right-0 top-full mt-1 bg-gray-900 rounded-md shadow-lg">
          {LANGUAGES.sort((a,b) => a.id.localeCompare(b.id)).map((language) => (
            <button
              key={language.id}
              onClick={() => setLanguage(language.id)}
              className="w-full items-center flex gap-3 px-3 py-1 hover:bg-gray-800"
            >
              <img className="w-5" src={language.flag} alt={language.name} />
              <span className="text-xs">{language.name}</span>
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
