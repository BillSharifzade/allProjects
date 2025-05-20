import { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import CategorySelector from './CategorySelector';

const CATEGORIES = [
  "Science", "Technology", "History",
  "Geography", "Sports", "Entertainment", "Politics",
  "Philosophy", "Literature", "Mathematics", "Biology"
];

interface ProfileFormProps {
  onClose: () => void;
}

export default function ProfileForm({ onClose }: ProfileFormProps) {
  const { user, logout, updateUserPreferences, updateUserDescription } = useAuth();
  const [description, setDescription] = useState(user?.description || '');
  const [selectedCategories, setSelectedCategories] = useState<string[]>(
    user?.preferences?.categories || []
  );
  const [isSaving, setIsSaving] = useState(false);
  const [saveMessage, setSaveMessage] = useState('');

  // Update local state when user changes
  useEffect(() => {
    console.log("ProfileForm: User updated, setting local state:", user);
    setDescription(user?.description || '');
    setSelectedCategories(user?.preferences?.categories || []);
    console.log("ProfileForm: Selected categories set to:", user?.preferences?.categories || []);
  }, [user]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSaving(true);
    setSaveMessage('');
    
    try {
      // Update user description
      updateUserDescription(description.slice(0, 50));
      
      // Log current state
      console.log("%c🔄 ProfileForm: About to save categories:", "color: #FF0000; font-weight: bold", selectedCategories);
      
      // CRITICAL FIX: Add detailed validation of categories
      if (!selectedCategories) {
        console.error("%c❌ ProfileForm: selectedCategories is null or undefined!", "color: red");
        setSaveMessage('Error: Invalid category selection');
        setIsSaving(false);
        return;
      }
      
      // Create a fresh copy of the categories array to avoid reference issues
      const categoriesToSave = Array.isArray(selectedCategories) ? 
        [...selectedCategories] : 
        [];
      
      console.log("%c✅ ProfileForm: Using categories to save:", "color: #FF0000; font-weight: bold", categoriesToSave);
      
      // CRITICAL FIX: Verify categories are a valid array before saving
      if (!Array.isArray(categoriesToSave)) {
        console.error("%c❌ ProfileForm: Invalid categories format - not an array:", "color: red", categoriesToSave);
        setSaveMessage('Error: Invalid category format');
        setIsSaving(false);
        return;
      }
      
      // Save categories
      updateUserPreferences({
        categories: categoriesToSave
      });
      
      // Add a small delay to ensure state updates are processed
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // Verify the save
      const storedUser = localStorage.getItem('quantUser');
      if (storedUser) {
        const parsed = JSON.parse(storedUser);
        console.log("%c📂 ProfileForm: Verified saved categories:", "color: #FF0000; font-weight: bold", parsed.preferences?.categories);
        
        // CRITICAL FIX: Double-check that categories were actually saved
        if (!parsed.preferences || !Array.isArray(parsed.preferences.categories)) {
          console.error("%c❌ ProfileForm: Categories were NOT properly saved!", "color: red");
          
          // Force fix the localStorage if needed
          if (parsed.preferences) {
            parsed.preferences.categories = categoriesToSave;
            localStorage.setItem('quantUser', JSON.stringify(parsed));
            console.log("%c🔧 ProfileForm: Forced update of localStorage with categories", "color: #FF0000; font-weight: bold");
          }
        } else {
          console.log("%c✅ ProfileForm: Categories saved successfully", "color: #FF0000; font-weight: bold");
        }
        
        // Show categories in the success message
        if (categoriesToSave.length > 0) {
          setSaveMessage(`Categories saved: ${categoriesToSave.join(", ")}`);
        } else {
          setSaveMessage('No categories selected. Showing general articles.');
        }
      } else {
        setSaveMessage('Preferences saved!');
      }
      
      // CRITICAL FIX: Add multiple stronger events to force a refresh with these categories
      console.log("%c🔄 ProfileForm: Dispatching DIRECT categories-apply event", "color: #FF0000; font-weight: bold");
      
      // Event 1: direct-categories-apply for our custom handler
      window.dispatchEvent(new CustomEvent('direct-categories-apply', {
        detail: { 
          categories: categoriesToSave,
          source: "ProfileForm",
          forceReset: true,
          timestamp: new Date().toISOString()
        }
      }));
      
      // Event 2: user-preferences-updated for the default App handlers
      window.dispatchEvent(new CustomEvent('user-preferences-updated', {
        detail: {
          preferences: { categories: categoriesToSave },
          categoriesChanged: true
        }
      }));

      // Re-enable the save button now that operations are done
      setIsSaving(false); 
      
      // Add a delay to show success message before closing modal
      setTimeout(() => {
        // Close the modal
        onClose();
        
        // REMOVED: Forced page reload logic
        // console.log("%c🔄 ProfileForm: About to reload page...", ...);
        // setTimeout(() => { window.location.reload(); }, 100);

      }, 2000); // Keep 2-second delay for showing message

    } catch (error) {
      console.error("%c❌ Error saving profile:", "color: red", error);
      setSaveMessage('Error saving preferences');
      setIsSaving(false); // Ensure button is re-enabled on error too
    } // Removed finally block as setIsSaving(false) is handled in try/catch
  };

  const handleCategoryChange = (categories: string[]) => {
    console.log("ProfileForm: Categories selected:", categories);
    
    // Calculate differences for debugging
    const added = categories.filter(c => !user?.preferences?.categories?.includes(c));
    const removed = user?.preferences?.categories?.filter(c => !categories.includes(c)) || [];
    
    console.log("ProfileForm: Categories added:", added);
    console.log("ProfileForm: Categories removed:", removed);
    
    // Create a fresh copy of the array
    setSelectedCategories([...categories]);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div>
        <label className="block mb-1">About You (max 50 characters)</label>
        <textarea
          value={description}
          onChange={(e) => setDescription(e.target.value)}
          maxLength={50}
          className="w-full p-2 bg-gray-800 rounded"
          rows={2}
        />
        <div className="text-right text-sm text-gray-400">
          {description.length}/50
        </div>
      </div>
      
      <div>
        <label className="block mb-1">Preferred Categories</label>
        <p className="text-sm text-gray-400 mb-2">
          Select your favorite topics to see articles from these categories
        </p>
        <CategorySelector 
          categories={CATEGORIES}
          selectedCategories={selectedCategories}
          onChange={handleCategoryChange}
        />
        {selectedCategories.length > 0 && (
          <p className="text-sm text-green-400 mt-2">
              {selectedCategories.length} categories selected: {selectedCategories.join(", ")}
          </p>
        )}
      </div>
      
      {saveMessage && (
        <div className="text-sm text-green-400 py-2">
          {saveMessage}
        </div>
      )}
      
      <div className="flex items-center justify-between pt-4">
        <button
          type="button"
          onClick={() => {
            logout();
            onClose();
          }}
          className="px-4 py-2 bg-red-700 hover:bg-red-800 rounded-md"
        >
          Logout
        </button>
        
        <div>
          <button
            type="button"
            onClick={onClose}
            className="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-md mr-2"
          >
            Cancel
          </button>
          <button
            type="submit"
            disabled={isSaving}
            className={`px-4 py-2 ${isSaving ? 'bg-gray-600' : 'bg-purple-700 hover:bg-purple-600'} rounded-md`}
          >
            {isSaving ? 'Saving...' : 'Save'}
          </button>
        </div>
      </div>
    </form>
  );
} 