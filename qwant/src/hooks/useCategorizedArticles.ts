import { useState, useCallback, useEffect, useRef, useMemo } from "react";
import { useWikiArticles } from "./useWikiArticles";
import { useAuth } from "../contexts/AuthContext";
import type { WikiArticle } from "../components/WikiCard";

const getCategoriesFromLocalStorage = (): string[] => {
  try {
    const storedUser = localStorage.getItem('quantUser');
    if (storedUser) {
      const parsedUser = JSON.parse(storedUser);
      const categories = parsedUser.preferences?.categories || [];
      return Array.isArray(categories) ? categories.filter(Boolean) : [];
    }
  } catch (e) {
    
  }
  return [];
};

const validateCategories = (categories: string[]): string[] => {
  if (!Array.isArray(categories)) {
    return [];
  }
  const validCategories = categories.filter(Boolean);
  return validCategories;
};

export function useCategorizedArticles() {
  const { articles: rawArticles, fetchArticles: originalFetchArticles } = useWikiArticles();
  const { user } = useAuth();

  const [filteredArticles, setFilteredArticles] = useState<WikiArticle[]>([]);
  const [isFetching, setIsFetching] = useState(false);
  const [isFetchCooldown, setIsFetchCooldown] = useState(false);
  const [lastUsedCategories, setLastUsedCategories] = useState<string[]>(getCategoriesFromLocalStorage());
  const lastAppliedCategories = useRef<string[]>([]);

  const currentUserCategoriesString = useMemo(() => {
    const categories = user?.preferences?.categories
        ? validateCategories([...user.preferences.categories])
        : [];
    return JSON.stringify(categories.sort());
  }, [user?.preferences?.categories]);

  const fetchArticles = useCallback(async (reset = false, explicitCategories: string[] = []) => {
    if (isFetching || isFetchCooldown) {
      return;
    }

    setIsFetching(true);
    setIsFetchCooldown(true);

    let categoriesToUse: string[] = [];

    if (explicitCategories && explicitCategories.length > 0) {
      categoriesToUse = validateCategories([...explicitCategories]);
    } else if (user?.preferences?.categories && user.preferences.categories.length > 0) {
      categoriesToUse = validateCategories([...user.preferences.categories]);
    } else {
      categoriesToUse = validateCategories(getCategoriesFromLocalStorage());
    }

    setLastUsedCategories(categoriesToUse);

    try {
      await originalFetchArticles(reset, categoriesToUse);
    } catch (error) {
      
    } finally {
      setIsFetching(false);
      setTimeout(() => {
          setIsFetchCooldown(false);
      }, 500);
    }
  }, [isFetching, isFetchCooldown, originalFetchArticles, user?.preferences?.categories]);

  useEffect(() => {
    if (!originalFetchArticles) return;

    const currentCategories = JSON.parse(currentUserCategoriesString);
    const lastCategoriesString = JSON.stringify([...lastAppliedCategories.current].sort());

    if (currentUserCategoriesString !== lastCategoriesString) {
      lastAppliedCategories.current = [...currentCategories];
      originalFetchArticles(true, currentCategories);
    }
  }, [currentUserCategoriesString, originalFetchArticles]);

  useEffect(() => {
    setFilteredArticles(rawArticles);
  }, [rawArticles]);

  const fetchMore = useCallback(() => {
    fetchArticles(false, lastUsedCategories);
  }, [fetchArticles, lastUsedCategories]);

  return { articles: filteredArticles, loading: isFetching, fetchArticles, fetchMore, isFetchCooldown };
}