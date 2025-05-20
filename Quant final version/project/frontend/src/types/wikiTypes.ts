export interface WikiArticle {
  pageid: number;
  title: string;
  displaytitle?: string;
  extract: string;
  url: string;
  thumbnail?: {
    source: string;
    width: number;
    height: number;
  };
  // Optional metadata for tracking
  _source?: string;
  _categoryName?: string;
  _searchQuery?: string;
}

export interface WikiArticlesHook {
  articles: WikiArticle[];
  loading: boolean;
  isLoading: boolean;
  error?: string;
  fetchArticles: (reset?: boolean, categories?: string[]) => Promise<void>;
  resetArticles: (reset?: boolean, categories?: string[]) => Promise<void>;
  likeArticle?: (articleId: number) => void;
  dislikeArticle?: (articleId: number) => void;
} 