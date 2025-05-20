import { useState } from 'react';
import { WIKI_CATEGORY_MAP } from '../constants/wikiCategories';

interface GuestCategoryModalProps {
  isOpen: boolean;
  onConfirm: (selectedCategories: string[]) => void;
}

const allCategories = Object.keys(WIKI_CATEGORY_MAP);

export default function GuestCategoryModal({ isOpen, onConfirm }: GuestCategoryModalProps) {
  const [selectedCategories, setSelectedCategories] = useState<string[]>([]);

  const handleToggleCategory = (category: string) => {
    setSelectedCategories(prev =>
      prev.includes(category)
        ? prev.filter(c => c !== category)
        : [...prev, category]
    );
  };

  const handleConfirm = () => {
    if (selectedCategories.length > 0) {
      onConfirm(selectedCategories);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm">
      <div className="bg-gray-900 border border-purple-700/50 rounded-lg p-6 w-full max-w-md mx-4 shadow-xl">
        <h2 className="text-2xl font-chakra font-medium text-purple-300 mb-4 text-center">Welcome to Qwant!</h2>
        <p className="text-center text-white/80 mb-6">Please select at least one category to start exploring:</p>

        <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-60 overflow-y-auto p-1 mb-6 border border-gray-700 rounded-md">
          {allCategories.sort().map(category => (
            <button
              key={category}
              onClick={() => handleToggleCategory(category)}
              className={`
                w-full p-2 rounded border text-sm font-medium transition-colors duration-150
                ${selectedCategories.includes(category)
                  ? 'bg-purple-600 border-purple-500 text-white'
                  : 'bg-gray-800 border-gray-600 text-white/70 hover:bg-gray-700 hover:border-gray-500'
                }
              `}
            >
              {category}
            </button>
          ))}
        </div>

        <button
          onClick={handleConfirm}
          disabled={selectedCategories.length === 0}
          className={`
            w-full font-chakra font-medium px-4 py-2 text-base border rounded transition-colors duration-150
            ${selectedCategories.length > 0
              ? 'border-purple-500/80 text-purple-300 bg-purple-500/10 hover:bg-purple-500/20 hover:border-purple-500'
              : 'border-gray-600 text-gray-500 bg-gray-800 cursor-not-allowed'
            }
            focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900
          `}
        >
          Confirm Selection
        </button>
      </div>
    </div>
  );
} 