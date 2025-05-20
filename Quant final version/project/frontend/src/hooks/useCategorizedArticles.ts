import { useState, useCallback, useEffect, useRef, useMemo } from "react";
import { useWikiArticles } from "./useWikiArticles";
import { useAuth } from "../contexts/AuthContext";
import type { WikiArticle } from "../components/WikiCard";

// Key for storing guest categories in localStorage
const GUEST_CATEGORIES_KEY = 'quantGuestCategories';

// Gets user categories OR guest categories
// Accepts the user object or null/undefined
const getActiveCategories = (user: { preferences?: { categories?: string[] } } | null | undefined): string[] => {
  try {
    if (user) { // User object provided (implies logged in)
      // Directly use preferences from the passed user object
      const categories = user.preferences?.categories || [];
      return validateCategories(categories);
    } else { // User is logged out (guest)
      const storedGuestCats = localStorage.getItem(GUEST_CATEGORIES_KEY);
      if (storedGuestCats) {
        const categories = JSON.parse(storedGuestCats);
        return validateCategories(categories);
      }
    }
  } catch (e) {
    console.error("Error reading categories:", e);
  }
  return []; // Default to empty
};

const validateCategories = (categories: any): string[] => {
  if (!Array.isArray(categories)) {
    return [];
  }
  // Ensure all items are strings and filter out empty ones
  const validCategories = categories.map(String).filter(Boolean);
  return validCategories;
};

// Function to save guest categories
export const saveGuestCategories = (categories: string[]) => {
  try {
    const validCategories = validateCategories(categories);
    localStorage.setItem(GUEST_CATEGORIES_KEY, JSON.stringify(validCategories));
    console.log('[useCategorizedArticles] Saved guest categories:', validCategories);
  } catch (e) {
    console.error("Error saving guest categories to localStorage:", e);
  }
};

export function useCategorizedArticles() {
  const { articles: rawArticles, loading: coreLoading, fetchArticles: originalFetchArticles } = useWikiArticles();
  const { user, isAuthenticated } = useAuth();

  const [filteredArticles, setFilteredArticles] = useState<WikiArticle[]>([]);
  const [isFetchCooldown, setIsFetchCooldown] = useState(false);
  const [lastUsedCategories, setLastUsedCategories] = useState<string[]>(() => getActiveCategories(user));
  const lastAppliedCategories = useRef<string[]>([]);
  const [guestNeedsCategories, setGuestNeedsCategories] = useState(false);

  const currentUserCategoriesString = useMemo(() => {
    const categories = getActiveCategories(user);
    return JSON.stringify(categories.sort());
  }, [user?.preferences?.categories]);

  const fetchArticles = useCallback(async (reset = false, explicitCategories: string[] = []) => {
    console.log(`[useCategorizedArticles] fetchArticles called. Reset: ${reset}, Explicit Categories:`, explicitCategories);

    const categoriesToUse = validateCategories(explicitCategories);

    if (coreLoading || isFetchCooldown || categoriesToUse.length === 0) {
      console.log('[useCategorizedArticles] Aborting fetch: Loading/Cooldown/NoCategories.', { coreLoading, isFetchCooldown, categoriesToUse });
      if (!isAuthenticated && categoriesToUse.length === 0) {
        setGuestNeedsCategories(true);
      }
      return;
    }

    console.log('[useCategorizedArticles] Conditions met. Setting guestNeedsCategories=false and isFetchCooldown=true.');
    setGuestNeedsCategories(false);
    setIsFetchCooldown(true);
    setLastUsedCategories(categoriesToUse);

    console.log('[useCategorizedArticles] Calling originalFetchArticles with categories:', categoriesToUse);
    try {
      await originalFetchArticles(reset, categoriesToUse);
    } catch (error) {
      console.error('[useCategorizedArticles] Error calling originalFetchArticles:', error);
    } finally {
       console.log('[useCategorizedArticles] originalFetchArticles finished. Clearing cooldown soon...');
      setTimeout(() => {
          console.log('[useCategorizedArticles] Cooldown finished.');
          setIsFetchCooldown(false);
      }, 500);
    }
  }, [coreLoading, isFetchCooldown, originalFetchArticles, isAuthenticated]);

  useEffect(() => {
    if (!originalFetchArticles) {
      console.log('[useCategorizedArticles Effect] originalFetchArticles not ready.');
      return;
    }

    const currentCategories = JSON.parse(currentUserCategoriesString);
    const lastCategoriesString = JSON.stringify([...lastAppliedCategories.current].sort());

    console.log(`[useCategorizedArticles Effect] Checking. Auth: ${isAuthenticated}, Current: ${currentUserCategoriesString}, Last Applied: ${lastCategoriesString}`);

    let needsFetch = false;
    let categoriesToFetch: string[] = [];

    if (!isAuthenticated) {
      console.log("[useCategorizedArticles Effect] Handling Guest User.");
      if (currentCategories.length === 0) {
        console.log("[useCategorizedArticles Effect] Guest user has NO categories selected. Setting flag to show modal.");
        setGuestNeedsCategories(true);
        setFilteredArticles([]);
        lastAppliedCategories.current = [];
      } else {
        if (currentUserCategoriesString !== lastCategoriesString) {
          console.log("[useCategorizedArticles Effect] Guest categories changed. Preparing fetch.");
          needsFetch = true;
          categoriesToFetch = currentCategories;
        } else {
           console.log("[useCategorizedArticles Effect] Guest categories unchanged.");
           setGuestNeedsCategories(false);
        }
      }
    } else {
      console.log("[useCategorizedArticles Effect] Handling Authenticated User.");
      setGuestNeedsCategories(false);
      if (currentUserCategoriesString !== lastCategoriesString) {
        console.log("[useCategorizedArticles Effect] Authenticated user categories changed. Preparing fetch.");
        needsFetch = true;
        categoriesToFetch = currentCategories;
      } else {
        console.log("[useCategorizedArticles Effect] Authenticated user categories unchanged.");
      }
      if (currentCategories.length === 0) {
         console.log("[useCategorizedArticles Effect] Authenticated user has no categories. Clearing articles.");
         setFilteredArticles([]);
      }
    }

    if (needsFetch) {
      console.log(`[useCategorizedArticles Effect] Triggering fetch with categories:`, categoriesToFetch);
      lastAppliedCategories.current = [...categoriesToFetch];
      fetchArticles(true, categoriesToFetch);
    } else {
       console.log("[useCategorizedArticles Effect] No fetch triggered.");
    }

  }, [currentUserCategoriesString, originalFetchArticles, fetchArticles, isAuthenticated]);

  useEffect(() => {
    setFilteredArticles(rawArticles);
  }, [rawArticles]);

  const fetchMore = useCallback(() => {
    if (lastUsedCategories.length > 0) {
        console.log('[useCategorizedArticles] fetchMore called. Using last used categories:', lastUsedCategories);
        fetchArticles(false, lastUsedCategories);
    } else {
        console.log('[useCategorizedArticles] fetchMore called, but no categories established. Aborting.');
    }
  }, [fetchArticles, lastUsedCategories]);

  return { articles: filteredArticles, loading: coreLoading, fetchArticles, fetchMore, isFetchCooldown, guestNeedsCategories };
}