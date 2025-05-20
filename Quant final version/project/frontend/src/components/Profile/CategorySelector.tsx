import { useState, useEffect } from 'react';
import {
  FlaskConical, Cpu, Scroll, Globe, Trophy, 
  Ticket, Landmark, BrainCircuit, BookOpen, Sigma, Leaf,
  Shuffle
} from 'lucide-react';
import React from 'react'; // Ensure React is imported for FC type if needed

interface CategorySelectorProps {
  categories: string[];
  selectedCategories: string[];
  onChange: (categories: string[]) => void;
}

// Icon mapping
const categoryIcons: { [key: string]: React.ElementType } = {
  "Science": FlaskConical,
  "Technology": Cpu,
  "History": Scroll,
  "Geography": Globe,
  "Sports": Trophy,
  "Entertainment": Ticket,
  "Politics": Landmark, // Using Landmark as a proxy for government/politics
  "Philosophy": BrainCircuit,
  "Literature": BookOpen,
  "Mathematics": Sigma,
  "Biology": Leaf,
};

export default function CategorySelector({ 
  categories, 
  selectedCategories, 
  onChange 
}: CategorySelectorProps) {
  // Local state for immediate feedback
  const [localSelectedCategories, setLocalSelectedCategories] = useState<string[]>(selectedCategories);
  
  // Update local state when prop changes
  useEffect(() => {
    setLocalSelectedCategories(selectedCategories);
  }, [selectedCategories]);
  
  const toggleCategory = (category: string) => {
    let newCategories: string[];
    
    // If already selected, remove it
    if (localSelectedCategories.includes(category)) {
      newCategories = localSelectedCategories.filter(c => c !== category);
    } else {
      // Otherwise add it
      newCategories = [...localSelectedCategories, category];
    }
    
    // Update local state immediately for responsive UI
    setLocalSelectedCategories(newCategories);
    
    // Log the change for debugging
    console.log(`Category ${category} ${localSelectedCategories.includes(category) ? 'removed' : 'added'}`);
    console.log("New selected categories:", newCategories);
    
    // Call the parent component's onChange
    onChange(newCategories);
  };

  // Shuffle function
  const handleShuffle = () => {
    const shuffledAvailable = [...categories].sort(() => 0.5 - Math.random());
    // Select between 2 and 5 categories
    const countToSelect = Math.floor(Math.random() * 4) + 2; 
    const newSelection = shuffledAvailable.slice(0, countToSelect);
    
    console.log(`Shuffle: Selecting ${countToSelect} categories:`, newSelection);
    setLocalSelectedCategories(newSelection); // Update local UI
    onChange(newSelection); // Update parent state
  };

  return (
    <div>
      {/* Changed from grid to flexbox for better wrapping */}
      <div className="flex flex-wrap gap-2">
        {categories.map(category => {
          const IconComponent = categoryIcons[category]; // Get the icon component
          const isSelected = localSelectedCategories.includes(category);

          return (
            <button
              key={category}
              type="button"
              onClick={() => toggleCategory(category)}
              // Adjusted classes for flex layout and icon inclusion
              className={`flex items-center justify-center gap-2 px-3 py-2 rounded-md text-sm transition-all w-[calc(50%-0.25rem)] ${ // Width calculation for 2 columns with gap-2
                isSelected
                  ? 'bg-purple-700 hover:bg-purple-600 font-bold text-white'
                  : 'bg-gray-700 hover:bg-gray-600 text-white/80'
              }`}
            >
              {IconComponent && <IconComponent size={16} className="flex-shrink-0" />} 
              <span>{isSelected ? `${category}` : category}</span> {/* Removed checkmark, using icon presence/color */} 
            </button>
          );
        })}

        {/* Shuffle Button - Added to fill the layout */}
        <button
          key="shuffle-btn"
          type="button"
          onClick={handleShuffle}
          title="Select 2-5 random categories"
          className={`flex items-center justify-center gap-2 px-3 py-2 rounded-md text-sm transition-all w-[calc(50%-0.25rem)] bg-blue-700 hover:bg-blue-600 text-white/90 font-medium`} // Distinct style
        >
          <Shuffle size={16} className="flex-shrink-0" /> 
          <span>Shuffle</span> 
        </button>
      </div>
      
      {/* Display selected categories text (optional, can be removed if redundant) */}
      {localSelectedCategories.length > 0 && (
        <div className="text-xs text-gray-400 mt-2">
          Selected: {localSelectedCategories.join(", ")}
        </div>
      )}
    </div>
  );
} 