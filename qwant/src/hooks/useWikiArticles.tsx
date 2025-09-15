import { useState, useCallback } from "react";

export interface WikiArticle {
  pageid: number;
  title: string;
  extract: string;
  url: string;
  thumbnail?: {
    source: string;
    width: number;
    height: number;
  };
  _source?: string;
  _categoryName?: string;
  _searchQuery?: string;
}

export interface WikiArticlesHook {
  articles: WikiArticle[];
  loading: boolean;
  fetchArticles: (reset?: boolean, categories?: string[]) => Promise<void>;
}

const API_BATCH_SIZE = 20;
const TARGET_ARTICLE_COUNT = 30;
const MIN_EXTRACT_LENGTH = 50;
const SEARCH_LIMIT = 30;
const RANDOM_ARTICLE_COUNT = 20;

const WIKI_CATEGORY_MAP: Record<string, {
  wikiCategory: string,
  altCategories: string[],
  searchTerms: string[]
}> = {
  "Science": { wikiCategory: "Category:Science", altCategories: ["Category:Scientific_disciplines", "Category:Fields_of_science"], searchTerms: ["science", "scientific", "physics", "chemistry", "biology", "astronomy", "research", "quantum"] },
  "Technology": { wikiCategory: "Category:Technology", altCategories: ["Category:Computing", "Category:Electronics"], searchTerms: ["technology", "computer", "software", "hardware", "internet", "digital", "programming", "artificial intelligence"] },
  "History": { wikiCategory: "Category:History", altCategories: ["Category:Historical_events", "Category:History_by_period"], searchTerms: ["history", "historical", "ancient", "medieval", "civilization", "war", "dynasty", "empire"] },
  "Arts": { wikiCategory: "Category:Arts", altCategories: ["Category:Visual_arts", "Category:Art_by_type"], searchTerms: ["art", "artistic", "painting", "sculpture", "museum", "gallery", "artist", "renaissance"] },
  "Geography": { wikiCategory: "Category:Geography", altCategories: ["Category:Regions", "Category:Countries"], searchTerms: ["geography", "geographic", "country", "continent", "mountain", "river", "ocean", "terrain"] },
  "Sports": { wikiCategory: "Category:Sports", altCategories: ["Category:Ball_games", "Category:Olympic_sports"], searchTerms: ["sport", "sports", "football", "soccer", "basketball", "tennis", "olympic", "championship"] },
  "Entertainment": { wikiCategory: "Category:Entertainment", altCategories: ["Category:Mass_media", "Category:Popular_culture"], searchTerms: ["entertainment", "movie", "film", "cinema", "actor", "actress", "television", "hollywood"] },
  "Politics": { wikiCategory: "Category:Politics", altCategories: ["Category:Government", "Category:Political_science"], searchTerms: ["politics", "political", "government", "election", "president", "parliament", "democracy", "policy"] },
  "Philosophy": { wikiCategory: "Category:Philosophy", altCategories: ["Category:Philosophical_concepts", "Category:Philosophers"], searchTerms: ["philosophy", "philosophical", "ethics", "logic", "metaphysics", "existence", "consciousness", "morality"] },
  "Literature": { wikiCategory: "Category:Literature", altCategories: ["Category:Books", "Category:Fiction"], searchTerms: ["literature", "literary", "book", "novel", "author", "writer", "fiction", "poetry"] },
  "Mathematics": { wikiCategory: "Category:Mathematics", altCategories: ["Category:Mathematical_concepts", "Category:Algebra"], searchTerms: ["mathematics", "mathematical", "algebra", "geometry", "calculus", "theorem", "equation", "number theory"] },
  "Biology": { wikiCategory: "Category:Biology", altCategories: ["Category:Organisms", "Category:Life_sciences"], searchTerms: ["biology", "biological", "organism", "species", "evolution", "genetics", "cell", "ecology"] }
};


const normalizeCategory = (category: string): string => {
  if (WIKI_CATEGORY_MAP[category]) return category;
  const normalizedKey = Object.keys(WIKI_CATEGORY_MAP).find(
    (key) => key.toLowerCase() === category.toLowerCase()
  );
  return normalizedKey || category;
};

const getWikidataItemForTitle = async (enWikiTitle: string): Promise<string | null> => {
  const apiUrl = `https://en.wikipedia.org/w/api.php?action=query&prop=pageprops&titles=${encodeURIComponent(enWikiTitle)}&format=json&origin=*`;
  try {
    const response = await fetch(apiUrl);
    if (!response.ok) {
      return null;
    }
    const data = await response.json();
    const pages = data.query?.pages;
    if (!pages) return null;
    const pageId = Object.keys(pages)[0];
    if (pageId === "-1") {
      return null;
    }
    const qid = pages[pageId]?.pageprops?.wikibase_item;
    if (qid) {
      return qid;
    } else {
      return null;
    }
  } catch (error) {
    return null;
  }
};

const getSitelinkFromWikidata = async (qid: string, targetLang: string): Promise<string | null> => {
  const apiUrl = `https://www.wikidata.org/w/api.php?action=wbgetentities&ids=${qid}&props=sitelinks&format=json&origin=*`;
  try {
    const response = await fetch(apiUrl);
    if (!response.ok) {
      return null;
    }
    const data = await response.json();
    const sitelink = data.entities?.[qid]?.sitelinks?.[`${targetLang}wiki`]?.title;
    if (sitelink) {
      return sitelink;
    } else {
      return null;
    }
  } catch (error) {
    return null;
  }
};

const getLocalizedCategory = async (englishCategoryName: string, targetLang: string): Promise<string> => {
  if (targetLang === 'en') {
    return englishCategoryName.startsWith("Category:") ? englishCategoryName : `Category:${englishCategoryName}`;
  }

  const formattedEnCategory = englishCategoryName.startsWith("Category:")
    ? englishCategoryName
    : `Category:${englishCategoryName.replace(/ /g, "_")}`;


  try {
    const langlinksUrl = `https://en.wikipedia.org/w/api.php?action=query&titles=${encodeURIComponent(formattedEnCategory)}&prop=langlinks&lllang=${targetLang}&format=json&origin=*`;
    const response = await fetch(langlinksUrl);
    if (response.ok) {
      const data = await response.json();
      const pages = data.query?.pages;
      if (pages) {
        const pageId = Object.keys(pages)[0];
        const page = pages[pageId];
        if (page.langlinks && page.langlinks.length > 0) {
          const localizedTitle = page.langlinks[0]['*'];
          return localizedTitle;
        }
      }
    }
  } catch (error) {

  }

  const qid = await getWikidataItemForTitle(formattedEnCategory);
  if (qid) {
    const wikidataSitelink = await getSitelinkFromWikidata(qid, targetLang);
    if (wikidataSitelink) {
       return wikidataSitelink;
    }
  }

  return formattedEnCategory;
};


const logCategories = (categories: string[]) => {
  if (categories && categories.length > 0) {
    const allSearchTerms = categories.flatMap(category => {
      const normalizedCategory = normalizeCategory(category);
      return WIKI_CATEGORY_MAP[normalizedCategory]?.searchTerms || [];
    });
    categories.forEach(cat => {
      const normalizedCategory = normalizeCategory(cat);
      if (!WIKI_CATEGORY_MAP[normalizedCategory]) {
      }
    });
  } else {
  }
};



export function useWikiArticles(): WikiArticlesHook {
  const [articles, setArticles] = useState<WikiArticle[]>([]);
  const [loading, setLoading] = useState(false);

  const getCurrentLanguage = () => navigator.language.split('-')[0] || 'en';


  const fetchPageIdsFromCategory = async (
    localizedCategoryName: string,
    lang: string
  ): Promise<{pageIds: number[], source: string}> => {
    const formattedCategory = localizedCategoryName.replace(/ /g, "_");

    try {
      const categoryUrl = `https://${lang}.wikipedia.org/w/api.php?action=query&list=categorymembers&cmtitle=${encodeURIComponent(formattedCategory)}&cmnamespace=0&cmlimit=50&format=json&origin=*`;
      const response = await fetch(categoryUrl);
      if (!response.ok) throw new Error(`Categorymembers API error: ${response.status}`);
      const data = await response.json();

      if (!data.query?.categorymembers || data.query.categorymembers.length === 0) {
        return { pageIds: [], source: `empty_category:${formattedCategory}` };
      }

      const pageIds = data.query.categorymembers.map((page: any) => page.pageid);
      return { pageIds, source: `category:${formattedCategory}` };

    } catch (error) {
      return { pageIds: [], source: `error_category:${formattedCategory}` };
    }
  };

  const fetchPageIdsFromIncategory = async (
    localizedCategoryName: string,
    lang: string
  ): Promise<{pageIds: number[], source: string}> => {
    const categoryTerm = localizedCategoryName.replace(/^Category:/, '').replace(/_/g, " ");

    try {
      const searchUrl = `https://${lang}.wikipedia.org/w/api.php?action=query&list=search&srsearch=incategory:"${encodeURIComponent(categoryTerm)}"&srnamespace=0&srlimit=${SEARCH_LIMIT}&format=json&origin=*`;
      const searchResponse = await fetch(searchUrl);
      if (!searchResponse.ok) throw new Error(`Incategory search API error: ${searchResponse.status}`);
      const searchData = await searchResponse.json();

      if (!searchData.query?.search || searchData.query.search.length === 0) {
        return { pageIds: [], source: `empty_incategory:${categoryTerm}` };
      }

      const pageIds = searchData.query.search.map((item: any) => item.pageid);
      return { pageIds, source: `incategory:${categoryTerm}` };

    } catch (error) {
      return { pageIds: [], source: `error_incategory:${categoryTerm}` };
    }
  };

  const fetchPageIdsFromSearch = async (searchTerms: string[], lang: string): Promise<{pageIds: number[], source: string}> => {
    try {
      let adjustedSearchTerms = [...searchTerms];
      if (lang !== 'en') {
        switch (lang) {
          case 'fr': adjustedSearchTerms = [...adjustedSearchTerms, 'article', 'sujet']; break;
          case 'es': adjustedSearchTerms = [...adjustedSearchTerms, 'artículo', 'tema']; break;
          case 'de': adjustedSearchTerms = [...adjustedSearchTerms, 'artikel', 'thema']; break;
        }
      }

      let queryStrategy: string;
      let queryTerms: string;
      if (Math.random() > 0.5 && adjustedSearchTerms.length > 1) {
        const randomTerms = adjustedSearchTerms.sort(() => 0.5 - Math.random()).slice(0, Math.min(3, adjustedSearchTerms.length));
        queryTerms = randomTerms.join(" ");
        queryStrategy = "exact_random_terms";
      } else {
        queryTerms = adjustedSearchTerms.join(" OR ");
        queryStrategy = "OR_terms";
      }

      const searchUrl = `https://${lang}.wikipedia.org/w/api.php?action=query&list=search&srsearch=${encodeURIComponent(queryTerms)}&srnamespace=0&srlimit=${SEARCH_LIMIT}&format=json&origin=*`;

      const response = await fetch(searchUrl);
      if (!response.ok) throw new Error(`Search API error: ${response.status}`);
      const data = await response.json();

      if (!data.query?.search || data.query.search.length === 0) {
        return { pageIds: [], source: `empty_search:${queryTerms}` };
      }

      const pageIds = data.query.search.map((page: any) => page.pageid);
      return { pageIds, source: `search:${queryTerms}` };

    } catch (error) {
      return { pageIds: [], source: `error_search:${searchTerms.join(',')}` };
    }
  };


  const fetchRandomPageIds = async (lang: string): Promise<{pageIds: number[], source: string}> => {
     try {
       const randomUrl = `https://${lang}.wikipedia.org/w/api.php?action=query&list=random&rnnamespace=0&rnlimit=${RANDOM_ARTICLE_COUNT}&format=json&origin=*`;
       const response = await fetch(randomUrl);
       if (!response.ok) throw new Error(`Random API error: ${response.status}`);
       const data = await response.json();

       if (!data.query?.random || data.query.random.length === 0) {
         return { pageIds: [], source: "empty_random" };
       }
       const pageIds = data.query.random.map((page: any) => page.id);
       return { pageIds, source: "random" };
     } catch (error) {
       return { pageIds: [], source: "error_random" };
     }
  };

  const fetchArticleDetails = async (pageIds: number[], lang: string, sourceInfo: string = "unknown"): Promise<WikiArticle[]> => {
     if (pageIds.length === 0) return [];

     try {
       const allArticles: WikiArticle[] = [];
       for (let i = 0; i < pageIds.length; i += API_BATCH_SIZE) {
         const batch = pageIds.slice(i, i + API_BATCH_SIZE);
         const detailsUrl = `https://${lang}.wikipedia.org/w/api.php?action=query&pageids=${batch.join('|')}&prop=extracts|info|pageimages&exintro=1&explaintext=1&inprop=url&pithumbsize=500&format=json&origin=*`;

         const detailsResponse = await fetch(detailsUrl);
         if (!detailsResponse.ok) {
           continue;
         }
         const detailsData = await detailsResponse.json();
         if (!detailsData.query?.pages) {
           continue;
         }

         const batchArticles: WikiArticle[] = Object.values(detailsData.query.pages)
           .map((page: any): WikiArticle | null => {
             if (page.missing || !page.extract || page.extract.length < MIN_EXTRACT_LENGTH) {
               return null;
             }
             return {
               pageid: page.pageid,
               title: page.title,
               extract: page.extract,
               url: page.fullurl,
               thumbnail: page.thumbnail ? {
                 source: page.thumbnail.source,
                 width: page.thumbnail.width,
                 height: page.thumbnail.height
               } : undefined,
               _source: sourceInfo
             };
           })
           .filter((article): article is WikiArticle => article !== null);

         allArticles.push(...batchArticles);
       }


       if (allArticles.length > 0 && allArticles.length < pageIds.length) {
       }

       return allArticles;
     } catch (error) {
       return [];
     }
  };

  const fetchArticles = useCallback(async (reset = false, categories: string[] = []) => {
    setLoading(true);
    if (reset) setArticles([]);

    const lang = getCurrentLanguage();
    logCategories(categories);

    window.dispatchEvent(new CustomEvent('wiki-fetching', {
      detail: { message: categories.length > 0 ? `Fetching articles in categories: ${categories.join(', ')}...` : 'Fetching random articles...' }
    }));

    const uniquePageIds = new Set<number>();
    let fetchSourceInfo = "unknown";

    try {
      if (categories && categories.length > 0) {

        for (const userCategory of categories) {
          if (uniquePageIds.size >= TARGET_ARTICLE_COUNT) break;

          const normalizedUserCategory = normalizeCategory(userCategory);
          const categoryConfig = WIKI_CATEGORY_MAP[normalizedUserCategory];
          const primaryWikiCategory = categoryConfig?.wikiCategory || `Category:${normalizedUserCategory}`;


          const localizedPrimaryCategory = await getLocalizedCategory(primaryWikiCategory, lang);

          let result = await fetchPageIdsFromIncategory(localizedPrimaryCategory, lang);
          if (result.pageIds.length > 0) {
             result.pageIds.forEach(id => uniquePageIds.add(id));
             if (fetchSourceInfo === "unknown") fetchSourceInfo = result.source;
             window.dispatchEvent(new CustomEvent('direct-category-match', { detail: { message: `Found ${result.pageIds.length} articles using incategory search for ${userCategory}`, category: userCategory } }));
             if (uniquePageIds.size >= TARGET_ARTICLE_COUNT) continue;
          }


          if (result.pageIds.length === 0) {
              result = await fetchPageIdsFromCategory(localizedPrimaryCategory, lang);
              if (result.pageIds.length > 0) {
                  result.pageIds.forEach(id => uniquePageIds.add(id));
                  if (fetchSourceInfo === "unknown") fetchSourceInfo = result.source;
                  window.dispatchEvent(new CustomEvent('direct-category-match', { detail: { message: `Found ${result.pageIds.length} articles using category listing for ${userCategory}`, category: userCategory } }));
                  if (uniquePageIds.size >= TARGET_ARTICLE_COUNT) continue;
              }
          }


          if (result.pageIds.length === 0 && categoryConfig?.altCategories.length > 0) {
            for (const altCatName of categoryConfig.altCategories) {
              if (uniquePageIds.size >= TARGET_ARTICLE_COUNT) break;
              const localizedAltCategory = await getLocalizedCategory(altCatName, lang);
              result = await fetchPageIdsFromCategory(localizedAltCategory, lang);
              if (result.pageIds.length > 0) {
                result.pageIds.forEach(id => uniquePageIds.add(id));
                if (fetchSourceInfo === "unknown") fetchSourceInfo = result.source;
                window.dispatchEvent(new CustomEvent('direct-category-match', { detail: { message: `Found ${result.pageIds.length} articles using alternative category ${altCatName} for ${userCategory}`, category: userCategory } }));
                break;
              }
            }
          }
        }


        if (uniquePageIds.size < TARGET_ARTICLE_COUNT) {
          let allSearchTerms: string[] = [];
          categories.forEach(userCategory => {
            const normalizedCategory = normalizeCategory(userCategory);
            const config = WIKI_CATEGORY_MAP[normalizedCategory];
            if (config) {
              const termsToAdd = config.searchTerms
                .sort(() => 0.5 - Math.random())
                .slice(0, 2);
              allSearchTerms = [...allSearchTerms, normalizedCategory, ...termsToAdd];
            } else {
              allSearchTerms.push(userCategory);
            }
          });
          const uniqueTerms = [...new Set(allSearchTerms)];
          const termsToUse = uniqueTerms.slice(0, 6);

          if (termsToUse.length > 0) {
            const searchResult = await fetchPageIdsFromSearch(termsToUse, lang);
            if (searchResult.pageIds.length > 0) {
               searchResult.pageIds.forEach(id => uniquePageIds.add(id));
               if (fetchSourceInfo === "unknown") fetchSourceInfo = searchResult.source;
            }
          }
        }
      }


      if (uniquePageIds.size === 0) {
        const reason = categories.length > 0 ? "Category/Search strategies failed" : "No categories specified";
        window.dispatchEvent(new CustomEvent('fetch-fallback', {
          detail: { message: `Showing random articles because ${categories.length > 0 ? 'no relevant articles were found for selected categories' : 'no categories were selected'}.`, categories: categories }
        }));

        const randomResult = await fetchRandomPageIds(lang);
        randomResult.pageIds.forEach(id => uniquePageIds.add(id));
        if (fetchSourceInfo === "unknown") fetchSourceInfo = randomResult.source;
      }

      if (uniquePageIds.size === 0) {
        window.dispatchEvent(new CustomEvent('fetch-failed', {
          detail: { message: `Unable to find any Wikipedia articles. Please try different categories or check your connection.`, categories: categories }
        }));
        setLoading(false);
        return;
      }

      const fetchedArticles = await fetchArticleDetails(Array.from(uniquePageIds), lang, fetchSourceInfo);

      if (fetchedArticles.length === 0) {
         window.dispatchEvent(new CustomEvent('fetch-failed', {
           detail: { message: `Found article references, but failed to load their content. Please try again later.`, categories: categories }
         }));
         setLoading(false);
         return;
      }


      const articlesWithMeta = fetchedArticles.map(article => ({
          ...article,
          _categoryName: categories.join(', ') || 'N/A (Random/Search Fallback)'
      }));


      if (articlesWithMeta.length > 0 && categories.length > 0 && fetchSourceInfo !== 'random') {
          const relevanceScore: Record<string, number> = {};
          let totalRelevantArticles = 0;
          const keywordMap: Record<string, string[]> = {};
          categories.forEach(category => {
              const normalizedCategory = normalizeCategory(category);
              const config = WIKI_CATEGORY_MAP[normalizedCategory];
              keywordMap[normalizedCategory] = config ? [...config.searchTerms, normalizedCategory] : [normalizedCategory];
          });

          articlesWithMeta.forEach(article => {
              const content = (article.title + " " + article.extract).toLowerCase();
              let isRelevant = false;
              Object.entries(keywordMap).forEach(([category, keywords]) => {
                  if (!relevanceScore[category]) relevanceScore[category] = 0;
                  const found = keywords.some(keyword => content.includes(keyword.toLowerCase()));
                  if (found) {
                      relevanceScore[category]++;
                      isRelevant = true;
                  }
              });
              if (isRelevant) totalRelevantArticles++;
          });

          const relevancePercent = articlesWithMeta.length > 0 ? Math.round(totalRelevantArticles / articlesWithMeta.length * 100) : 0;
           if (relevancePercent >= 30) {
                window.dispatchEvent(new CustomEvent('relevant-articles-found', { detail: { relevancePercent, categories }}));
           } else if (categories.length > 0){
                window.dispatchEvent(new CustomEvent('low-relevance-warning', { detail: { relevancePercent, categories }}));
           }
      }

      setArticles(prev => (reset ? articlesWithMeta : [...prev, ...articlesWithMeta]));

    } catch (error) {
       window.dispatchEvent(new CustomEvent('fetch-error', {
         detail: { message: `An unexpected error occurred: ${error instanceof Error ? error.message : 'Unknown error'}` }
       }));
    } finally {
      setLoading(false);
       window.dispatchEvent(new CustomEvent('articles-loaded', { detail: { count: articles.length, timestamp: new Date().toISOString() } }));
    }
  }, []);

  return { articles, loading, fetchArticles };
}