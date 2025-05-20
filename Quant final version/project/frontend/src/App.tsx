import { useEffect, useRef, useCallback, useState, lazy, Suspense } from "react";
import { useTranslation } from 'react-i18next';
import { WikiCard } from "./components/WikiCard";
import { User } from "lucide-react";
import { Analytics } from "@vercel/analytics/react";
import { LanguageSelector } from "./components/LanguageSelector";
import { useLikedArticles } from "./contexts/LikedArticlesContext";
import { useCategorizedArticles, saveGuestCategories } from "./hooks/useCategorizedArticles";
import { useAuth } from './contexts/AuthContext';

const GuestCategoryModal = lazy(() => import('./components/GuestCategoryModal'));
const AboutModal = lazy(() => import('./components/AboutModal'));
const LikesModal = lazy(() => import('./components/LikesModal'));
const AuthModal = lazy(() => import('./components/Auth/AuthModal'));
const ProfileModal = lazy(() => import('./components/Profile/ProfileModal'));
const ArticleModal = lazy(() => import('./components/ArticleModal'));

interface WikiArticle {
  pageid: number;
  title: string;
  extract: string;
  url: string;
  thumbnail?: {
    source: string;
    width: number;
    height: number;
  };
}

function App() {
  const { t } = useTranslation();
  const [showAbout, setShowAbout] = useState(false);
  const [showLikes, setShowLikes] = useState(false);
  const [showAuth, setShowAuth] = useState(false);
  const [showProfile, setShowProfile] = useState(false);
  const { articles, loading, fetchArticles, fetchMore, isFetchCooldown, guestNeedsCategories } = useCategorizedArticles();
  const { likedArticles, toggleLike } = useLikedArticles();
  const { isAuthenticated, user } = useAuth();
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedArticle, setSelectedArticle] = useState<WikiArticle | null>(null);

  const observerRef = useRef<IntersectionObserver | null>(null);

  useEffect(() => {
  }, [articles]);
  
  const articlesToRender = articles;

  const handleProfileClose = useCallback(() => {
    setShowProfile(false);
  }, []);

  const handleObserver = useCallback(
    (entries: IntersectionObserverEntry[]) => {
      const targetEntry = entries[0];
      if (targetEntry.isIntersecting && !loading && !isFetchCooldown) {
        fetchMore();
        if (observerRef.current) {
           observerRef.current.unobserve(targetEntry.target);
        }
      } else {
      }
    },
    [loading, isFetchCooldown, fetchMore]
  );

  const observeArticleRef = useCallback((node: HTMLDivElement | null) => {
    if (observerRef.current) {
      observerRef.current.disconnect();
      observerRef.current = null;
    }

    if (node) {
      observerRef.current = new IntersectionObserver(handleObserver, {
        threshold: 0.1, 
      });
      observerRef.current.observe(node);
    } else {
    }
  }, [handleObserver]);

  const handleExport = () => {
    const simplifiedArticles = likedArticles.map((article) => ({
      title: article.title,
      url: article.url,
      extract: article.extract,
      thumbnail: article.thumbnail?.source || null,
    }));

    const dataStr = JSON.stringify(simplifiedArticles, null, 2);
    const dataUri =
      "data:application/json;charset=utf-8," + encodeURIComponent(dataStr);

    const exportFileDefaultName = `qwant-favorites-${new Date().toISOString().split("T")[0]
      }.json`;

    const linkElement = document.createElement("a");
    linkElement.setAttribute("href", dataUri);
    linkElement.setAttribute("download", exportFileDefaultName);
    linkElement.click();
  };

  const handleGuestConfirm = useCallback((selectedCategories: string[]) => {
    console.log("[App] Guest confirmed categories:", selectedCategories);
    saveGuestCategories(selectedCategories);
    console.log("[App] Attempting to trigger fetchArticles...");
    fetchArticles(true, selectedCategories);
    console.log("[App] fetchArticles call completed.");
  }, [fetchArticles]);

  return (
    <div className="h-screen w-full bg-black text-white overflow-y-scroll snap-y snap-mandatory hide-scroll">
      <div className="fixed top-4 left-4 z-50">
        <button
          onClick={() => window.location.reload()}
          className="font-chakra font-medium px-5 py-2 text-2xl border border-purple-500/50 text-purple-300 hover:bg-purple-500/10 hover:border-purple-500/90 rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-black"
        >
          Qwant
        </button>
      </div>

      <div className="fixed top-4 right-4 z-50 flex flex-col items-end gap-2">
        {isAuthenticated ? (
          <div className="flex items-center gap-2 mb-2">
            <span className="text-sm text-white/70">Hi, {user?.username}</span>
            <button
              onClick={() => setShowProfile(true)}
              className="font-chakra font-medium flex items-center px-3 py-1 text-sm border border-purple-500/50 text-purple-300 hover:bg-purple-500/10 hover:border-purple-500/90 rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-black"
            >
              <User className="w-4 h-4 mr-1" />
              {t('profileButton')}
            </button>
          </div>
        ) : (
          <button
            onClick={() => setShowAuth(true)}
            className="font-chakra font-medium flex items-center px-4 py-1.5 text-base border border-purple-500/50 text-purple-300 hover:bg-purple-500/10 hover:border-purple-500/90 rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-black"
          >
            <User className="w-5 h-5 mr-1" />
            {t('loginButton')}
          </button>
        )}
        <button
          onClick={() => setShowAbout(!showAbout)}
          className="font-chakra font-medium px-4 py-1.5 text-base border border-purple-500/50 text-purple-300 hover:bg-purple-500/10 hover:border-purple-500/90 rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-black"
        >
          {t('aboutButton')}
        </button>
        <button
          onClick={() => setShowLikes(!showLikes)}
          className="font-chakra font-medium px-4 py-1.5 text-base border border-purple-500/50 text-purple-300 hover:bg-purple-500/10 hover:border-purple-500/90 rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-black"
        >
          {t('likesButton')}
        </button>
        <div>
          <LanguageSelector />
        </div>
      </div>

      {guestNeedsCategories && (
        <Suspense fallback={null}>
          <GuestCategoryModal isOpen={guestNeedsCategories} onConfirm={handleGuestConfirm} />
        </Suspense>
      )}

      {showAbout && (
        <Suspense fallback={null}>
          <AboutModal key="aboutModal" isOpen={showAbout} onClose={() => setShowAbout(false)} />
        </Suspense>
      )}

      {showLikes && (
        <Suspense fallback={null}>
          <LikesModal
            key="likesModal"
            isOpen={showLikes}
            likedArticles={likedArticles}
            toggleLike={toggleLike}
            searchQuery={searchQuery}
            setSearchQuery={setSearchQuery}
            handleExport={handleExport}
            setSelectedArticle={setSelectedArticle}
            onClose={() => setShowLikes(false)}
          />
        </Suspense>
      )}

      {showAuth && (
        <Suspense fallback={null}>
          <AuthModal key="authModal" isOpen={showAuth} onClose={() => setShowAuth(false)} />
        </Suspense>
      )}

      {showProfile && (
        <Suspense fallback={null}>
          <ProfileModal key="profileModal" isOpen={showProfile} onClose={handleProfileClose} />
        </Suspense>
      )}

      {selectedArticle && (
        <Suspense fallback={null}>
          <ArticleModal
            key="articleModal"
            article={selectedArticle}
            onClose={() => setSelectedArticle(null)}
            onLike={toggleLike}
            isLiked={likedArticles.some(a => a.pageid === selectedArticle.pageid)}
          />
        </Suspense>
      )}

      {loading && articlesToRender.length === 0 ? (
        <div className="h-screen w-full flex flex-col items-center justify-center gap-4">
          <div className="atom-loader">
            <div className="wrapper">
              <div className="dot"></div>
            </div>
          </div>
          {!guestNeedsCategories && <p className="text-white/70 mt-4">Loading articles...</p>} 
        </div>
      ) : articlesToRender.length === 0 && !loading && !guestNeedsCategories ? (
        <div className="h-screen w-full flex flex-col items-center justify-center gap-4 text-center px-4">
          <p className="text-xl text-white/80">No articles found for the selected categories.</p>
          <p className="text-white/60">Try selecting different categories or logging in for more options.</p>
        </div>
      ) : (
        articlesToRender.map((article, index) => {
          const targetIndex = Math.max(0, articlesToRender.length - 10);
          const isTarget = index === targetIndex && index > 0;
          return (
            <div key={article.pageid} ref={isTarget ? observeArticleRef : null}>
              <WikiCard
                article={article}
                onLike={toggleLike}
                isLiked={likedArticles.some(likedArticle => likedArticle.pageid === article.pageid)}
              />
            </div>
          );
        })
      )}

      <div className="h-1" />

      <Analytics />
    </div>
  );
}

export default App;