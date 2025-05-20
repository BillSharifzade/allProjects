import { useState, useCallback } from "react";
import { useLocalization } from "./useLocalization";
import type { WikiArticle } from "../components/WikiCard";

const shuffleArray = <T>(array: T[]): T[] => {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
};

export function useWikiArticles() {
  const [articles, setArticles] = useState<WikiArticle[]>([]);
  const [loading, setLoading] = useState(false);
  const [fetchedPageIds, setFetchedPageIds] = useState<Set<number>>(new Set());
  const { currentLanguage } = useLocalization();

  const fetchArticles = useCallback(async (reset: boolean = false, categories: string[] = []) => {
    if (loading) return;
    setLoading(true);
    if (reset) {
      setFetchedPageIds(new Set());
      setArticles([]);
    }

    try {
      const baseParams: Record<string, string> = {
            action: "query",
            format: "json",
            prop: "extracts|info|pageimages",
            inprop: "url|varianttitles",
            exintro: "1",
            exlimit: "max",
            exsentences: "5",
            explaintext: "1",
            piprop: "thumbnail",
            pithumbsize: "300",
            origin: "*",
            variant: currentLanguage.id,
      };

      let allPages: any[] = [];
      let fetchMethodUsed: 'recursive' | 'search_fallback' | 'random' = 'random';

      if (categories.length > 0) {
        const potentialTargets = new Set<string>();

        const mainCategory = categories[Math.floor(Math.random() * categories.length)];
        const mainCategoryTitle = `Category:${mainCategory.replace(/ /g, '_')}`;
        potentialTargets.add(mainCategoryTitle);

        let level1Subcats: string[] = [];
        try {
          const subcatParams1 = {
              action: "query", format: "json", list: "categorymembers",
              cmtitle: mainCategoryTitle, cmtype: "subcat", cmlimit: "20", origin: "*",
          };
          const subcatResponse1 = await fetch(currentLanguage.api + new URLSearchParams(subcatParams1));
          if (!subcatResponse1.ok) throw new Error(`L1 Subcategory fetch failed: ${subcatResponse1.status}`);
          const subcatData1 = await subcatResponse1.json();

          if (subcatData1.query?.categorymembers) {
              level1Subcats = subcatData1.query.categorymembers.map((cat: any) => cat.title);
              level1Subcats.forEach(subcat => potentialTargets.add(subcat));
          }

          if (level1Subcats.length === 0) {
              fetchMethodUsed = 'search_fallback';
              try {
                  const searchParams = {
                      ...baseParams,
                      generator: "search",
                      gsrsearch: mainCategory,
                      gsrlimit: "100",
                      gsrnamespace: "0",
                  };
                  const searchResponse = await fetch(currentLanguage.api + new URLSearchParams(searchParams));
                  if (!searchResponse.ok) throw new Error(`Search fallback failed: ${searchResponse.status}`);
                  const searchData = await searchResponse.json();
                  if (searchData.error) throw new Error(`API Error (search fallback): ${searchData.error.info}`);
                  if (searchData.query?.pages) {
                      allPages = Object.values(searchData.query.pages);
                  }
              } catch(searchError) {
                  // Error handled by empty allPages
              }

          } else {
              fetchMethodUsed = 'recursive';
              const subcatPromises2 = level1Subcats.map(level1Title => {
                  const subcatParams2 = {
                      action: "query", format: "json", list: "categorymembers",
                      cmtitle: level1Title, cmtype: "subcat", cmlimit: "10", origin: "*",
                  };
                  return fetch(currentLanguage.api + new URLSearchParams(subcatParams2)).then(res => res.ok ? res.json() : Promise.reject(`L2 fetch failed for ${level1Title}`));
              });
              const results2 = await Promise.allSettled(subcatPromises2);
              let level2Subcats: string[] = [];
              results2.forEach(result => {
                  if (result.status === 'fulfilled' && result.value.query?.categorymembers) {
                      result.value.query.categorymembers.forEach((cat: any) => {
                          const l2Title = cat.title;
                          potentialTargets.add(l2Title);
                          level2Subcats.push(l2Title);
                      });
                  }
              });

              if (level2Subcats.length > 0) {
                  const subcatPromises3 = level2Subcats.map(level2Title => {
                      const subcatParams3 = {
                          action: "query", format: "json", list: "categorymembers",
                          cmtitle: level2Title, cmtype: "subcat", cmlimit: "5", origin: "*",
                      };
                      return fetch(currentLanguage.api + new URLSearchParams(subcatParams3)).then(res => res.ok ? res.json() : Promise.reject(`L3 fetch failed for ${level2Title}`));
                  });
                  const results3 = await Promise.allSettled(subcatPromises3);
                  results3.forEach(result => {
                      if (result.status === 'fulfilled' && result.value.query?.categorymembers) {
                          result.value.query.categorymembers.forEach((cat: any) => {
                              potentialTargets.add(cat.title);
                          });
                      }
                  });
              }
          }

        } catch (subcatError) {
            // Proceed with targets found so far
        }

        let targetCategories = Array.from(potentialTargets);
        const MAX_TARGET_CATEGORIES = 70;
        if (targetCategories.length > MAX_TARGET_CATEGORIES) {
            targetCategories = shuffleArray(targetCategories).slice(0, MAX_TARGET_CATEGORIES);
        }

        if (fetchMethodUsed === 'recursive') { // Only fetch members if we didn't fallback to search
            const categoryFetchPromises = targetCategories.map(categoryTitle => {
              const queryParams = {
                ...baseParams,
                generator: "categorymembers",
                gcmtitle: categoryTitle,
                gcmlimit: "20",
              };
              return fetch(currentLanguage.api + new URLSearchParams(queryParams)).then(res => {
                 if (!res.ok) throw new Error(`Member fetch failed for ${categoryTitle}: ${res.status}`);
                 return res.json();
              });
            });

            const results = await Promise.allSettled(categoryFetchPromises);
            results.forEach((result) => {
              if (result.status === 'fulfilled') {
                const data = result.value;
                if (data.query?.pages) {
                  const pages = Object.values(data.query.pages);
                  allPages = allPages.concat(pages);
                } else if (data.error) {
                  // Handle API error per category if needed
                }
              } else {
                // Handle fetch failure per category if needed
              }
            });
        }

      } else {
        fetchMethodUsed = 'random';
        const queryParams = {
          ...baseParams,
          generator: "random",
          grnlimit: "30",
          grnnamespace: "0",
        };
        const response = await fetch(currentLanguage.api + new URLSearchParams(queryParams));
        if (!response.ok) throw new Error(`Random fetch failed: ${response.status}`);
        const data = await response.json();
        if (data.error) throw new Error(`API Error (random): ${data.error.info}`);
        if (data.query?.pages) {
           allPages = Object.values(data.query.pages);
        }
      }

      const articleNamespacePages = allPages.filter(page => page?.ns === 0);

      const uniquePageMap = new Map<number, any>();
      articleNamespacePages.forEach(page => {
          if (page.pageid) {
              uniquePageMap.set(page.pageid, page);
          }
      });
      const uniqueCombinedPages = Array.from(uniquePageMap.values());

      const uniqueNewPages = uniqueCombinedPages.filter(page => !fetchedPageIds.has(page.pageid));

      let pagesToProcess = uniqueNewPages;

      if (pagesToProcess.length > 200) {
        pagesToProcess = shuffleArray(pagesToProcess).slice(0, 200);
      }

      const newArticles = pagesToProcess
        .map(
          (page: any): WikiArticle | null => {
            if (!page.pageid || !page.title || !page.extract || !page.canonicalurl) {
              return null;
            }
            return {
              title: page.title,
              displaytitle: page.varianttitles?.[currentLanguage.id] || page.title,
              extract: page.extract,
              pageid: page.pageid,
              thumbnail: page.thumbnail,
              url: page.canonicalurl,
            };
          }
        )
        .filter(
          (article): article is WikiArticle =>
            article !== null &&
            !!article.url &&
            !!article.extract
        );

      const withThumbnail: WikiArticle[] = [];
      const withoutThumbnail: WikiArticle[] = [];
      newArticles.forEach(article => {
          if (article.thumbnail?.source) {
              withThumbnail.push(article);
          } else {
              withoutThumbnail.push(article);
          }
      });

      const shuffledWithThumb = shuffleArray(withThumbnail);
      const shuffledWithoutThumb = shuffleArray(withoutThumbnail);

      const targetCount = 15;
      const numWithThumb = Math.min(targetCount, shuffledWithThumb.length);
      const neededMore = targetCount - numWithThumb;
      const numWithoutThumb = Math.min(neededMore, shuffledWithoutThumb.length);

      const combinedSelection = [
          ...shuffledWithThumb.slice(0, numWithThumb),
          ...shuffledWithoutThumb.slice(0, numWithoutThumb)
      ];

      const articlesToAdd: WikiArticle[] = shuffleArray(combinedSelection);

      if (articlesToAdd.length > 0) {
          const newIds = new Set(articlesToAdd.map(a => a.pageid));
          setFetchedPageIds(prevIds => new Set([...prevIds, ...newIds]));

          setArticles((prev) => [...prev, ...articlesToAdd]);
      }

    } catch (error) {
    }
    setLoading(false);
  }, [loading, currentLanguage.api, currentLanguage.id, fetchedPageIds]);

  return { articles, loading, fetchArticles };
}