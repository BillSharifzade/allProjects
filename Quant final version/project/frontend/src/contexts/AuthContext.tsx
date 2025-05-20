// First, let's update the AuthContext to include the user preferences type
import { createContext, useContext, useState, useEffect, ReactNode } from 'react';

interface UserPreferences {
  categories: string[];
  // Add other preference types as needed
}

interface User {
  username: string;
  email: string;
  description: string; // User description with max 50 chars
  preferences: UserPreferences;
}

interface AuthContextType {
  isAuthenticated: boolean;
  user: User | null;
  login: (email: string) => Promise<boolean>;
  register: (username: string, email: string) => Promise<boolean>;
  logout: () => void;
  updateUserPreferences: (preferences: Partial<UserPreferences>) => void;
  updateUserDescription: (description: string) => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

// Function to debug localStorage
const debugLocalStorage = () => {
  try {
    const user = localStorage.getItem('quantUser');
    if (user) {
      const parsed = JSON.parse(user);
      console.log("DEBUG localStorage quantUser:", parsed);
      console.log("User categories from localStorage:", parsed.preferences?.categories || []);
    } else {
      console.log("DEBUG: No user in localStorage");
    }
  } catch (e) {
  }
};

// Helper to standardize category names for consistent storage
const standardizeCategories = (categories: string[]): string[] => {
  // First ensure we have a valid array
  if (!Array.isArray(categories)) {
    return [];
  }
  
  // Filter out empty values
  const validCategories = categories.filter(Boolean);
  
  // Proper case standardization - capitalize first letter of each word
  return validCategories.map(category => {
    // Skip if category is not a string
    if (typeof category !== 'string') {
      return '';
    }
    
    // Standardize the category name format
    return category
      .trim()
      .split(' ')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(' ');
  }).filter(Boolean);
};

export function AuthProvider({ children }: { children: ReactNode }) {
  const [isAuthenticated, setIsAuthenticated] = useState<boolean>(false);
  const [user, setUser] = useState<User | null>(null);

  // Load user from localStorage on initial render
  useEffect(() => {
    debugLocalStorage();
    const storedUser = localStorage.getItem('quantUser');
    if (storedUser) {
      try {
        const parsedUser = JSON.parse(storedUser);
        
        // Ensure parsed user has valid preferences structure
        if (!parsedUser.preferences) {
          parsedUser.preferences = { categories: [] };
        }
        
        // Ensure categories is an array
        if (!Array.isArray(parsedUser.preferences.categories)) {
          parsedUser.preferences.categories = [];
        }
        
       
        setUser(parsedUser);
        setIsAuthenticated(true);
        
        // Re-save to ensure proper formatting
        localStorage.setItem('quantUser', JSON.stringify(parsedUser));
      } catch (error) {
        localStorage.removeItem('quantUser');
      }
    }
  }, []);

  const updateUserPreferences = (preferences: Partial<UserPreferences>) => {
    if (!user) return;
    
    
    // Create a fresh copy to avoid reference issues
    const updatedPreferences = {...preferences};
    
    // Ensure categories is always an array
    if (updatedPreferences.categories) {
      updatedPreferences.categories = Array.isArray(updatedPreferences.categories) ? 
        [...updatedPreferences.categories] : [];
      
      // Standardize category names
      updatedPreferences.categories = standardizeCategories(updatedPreferences.categories);
      
      // If categories changed, dispatch an event to notify
      if (JSON.stringify(updatedPreferences.categories) !== JSON.stringify(user.preferences.categories)) {
      }
    }
    
    const updatedUser = {
      ...user,
      preferences: {
        ...user.preferences,
        ...updatedPreferences
      }
    };
    
    
    // Update state
    setUser(updatedUser);
    
    // Save to localStorage
    localStorage.setItem('quantUser', JSON.stringify(updatedUser));
    
    // Update users collection too to ensure persistence
    updateUserInCollection(updatedUser);
    
    // Add an event to notify other components of the preference change
    window.dispatchEvent(new CustomEvent('user-preferences-updated', {
      detail: {
        preferences: updatedUser.preferences,
        categoriesChanged: updatedPreferences.categories !== undefined
      }
    }));
    
    // Force reload categories for debugging
    setTimeout(() => {
      debugLocalStorage();
    }, 100);
  };
  
  // Helper to update the user in the users collection
  const updateUserInCollection = (updatedUser: User) => {
    try {
      const storedUsers = localStorage.getItem('quantUsers');
      if (storedUsers) {
        const users = JSON.parse(storedUsers);
        const userIndex = users.findIndex((u: any) => u.email === updatedUser.email);
        
        if (userIndex >= 0) {
          // Update existing user
          const existingUser = users[userIndex];
          users[userIndex] = {
            ...existingUser,
            description: updatedUser.description,
            preferences: updatedUser.preferences
          };
          
          // Save back to localStorage
          localStorage.setItem('quantUsers', JSON.stringify(users));
        }
      }
    } catch (error) {
    }
  };

  const updateUserDescription = (description: string) => {
    if (!user) return;
    
    const updatedUser = {
      ...user,
      description: description.slice(0, 50) // Enforce 50 char limit
    };
    
    setUser(updatedUser);
    localStorage.setItem('quantUser', JSON.stringify(updatedUser));
    updateUserInCollection(updatedUser);
  };

  const login = async (email: string): Promise<boolean> => {
    // In a real app, you would validate against a backend
    const storedUsers = localStorage.getItem('quantUsers');
    if (storedUsers) {
      try {
        const users = JSON.parse(storedUsers);
        const foundUser = users.find((u: any) => 
          u.email === email
        );
        
        if (foundUser) {
          // Ensure user has preferences with categories initialized
          const userToStore = {
            username: foundUser.username,
            email: foundUser.email,
            description: foundUser.description || '',
            preferences: foundUser.preferences || { categories: [] }
          };
          
          // Ensure categories is an array
          if (!Array.isArray(userToStore.preferences.categories)) {
            userToStore.preferences.categories = [];
          }
          
          
          setUser(userToStore);
          setIsAuthenticated(true);
          localStorage.setItem('quantUser', JSON.stringify(userToStore));
          
          // Force reload to apply categories
          setTimeout(() => {
            debugLocalStorage();
            window.dispatchEvent(new CustomEvent('user-preferences-updated', {
              detail: {
                preferences: userToStore.preferences,
                categoriesChanged: true
              }
            }));
          }, 100);
          
          return true;
        }
      } catch (error) {
      }
    }
    return false;
  };

  const register = async (username: string, email: string): Promise<boolean> => {    // In a real app, you would send this to a backend
    const storedUsers = localStorage.getItem('quantUsers');
    let users = [];
    
    if (storedUsers) {
      try {
        users = JSON.parse(storedUsers);
        // Check if email already exists
        if (users.some((u: any) => u.email === email)) {
          throw new Error("Email already exists");
        }
      } catch (error) {
      }
    }
    
    const newUser = {
      username,
      email,
      description: '', // Initialize description
      preferences: { categories: [] }, // Initialize preferences
      // DO NOT STORE PLAINTEXT PASSWORD: password 
    };
    
    users.push(newUser);
    localStorage.setItem('quantUsers', JSON.stringify(users));
    
    // Log in the new user immediately
    // This is safe because login now only checks email
    await login(email);
    return true;
  };

  const logout = () => {
    // Clear user data from state and localStorage
    setUser(null);
    setIsAuthenticated(false);
    localStorage.removeItem('quantUser');
  };

  return (
    <AuthContext.Provider value={{ 
      isAuthenticated, 
      user, 
      login, 
      register, 
      logout,
      updateUserPreferences,
      updateUserDescription
    }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}