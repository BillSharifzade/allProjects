import { useEffect, useRef, useCallback, useState } from "react";
import { WikiCard } from "./components/WikiCard";
import { Search, X, Download, User } from "lucide-react";
import { Analytics } from "@vercel/analytics/react";
import { LanguageSelector } from "./components/LanguageSelector";
import { useLikedArticles } from "./contexts/LikedArticlesContext";
import { useCategorizedArticles } from "./hooks/useCategorizedArticles";
import ArticleModal from './components/ArticleModal';
import AuthModal from './components/Auth/AuthModal';
import ProfileModal from './components/Profile/ProfileModal';
import { useAuth } from './contexts/AuthContext';
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
  const [showAbout, setShowAbout] = useState(false);
  const [showLikes, setShowLikes] = useState(false);
  const [showAuth, setShowAuth] = useState(false);
  const [showProfile, setShowProfile] = useState(false);
  const { articles, loading, fetchArticles, fetchMore, isFetchCooldown } = useCategorizedArticles();
  const { likedArticles, toggleLike } = useLikedArticles();
  const { isAuthenticated, user } = useAuth();
  const observerTarget = useRef(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedArticle, setSelectedArticle] = useState<WikiArticle | null>(null);
  const [categoriesUpdated, setCategoriesUpdated] = useState(false);
  const [profileOpen, setProfileOpen] = useState(false);
  const [notification, setNotification] = useState<{message: string, type: string} | null>(null);
  useEffect(() => {
  }, [articles]);
  
  // Use all articles directly
  const articlesToRender = articles;

  // Track when the profile is closed to refresh articles
  const handleProfileClose = useCallback(() => {
    setShowProfile(false);
    setProfileOpen(false);
    
    // Check if user has categories and trigger a refresh
    if (user?.preferences?.categories && user.preferences.categories.length > 0) {
      setCategoriesUpdated(true);
      
      // Force refresh to ensure categories are applied
      setTimeout(() => {
        window.location.reload();
      }, 200);
    }
  }, [user?.preferences?.categories]);

  // Refresh articles when categories are updated
  useEffect(() => {
    if (categoriesUpdated && !profileOpen && !loading) {
      const categories = user?.preferences?.categories || [];
      fetchArticles(true, categories);
      setCategoriesUpdated(false);
    }
  }, [categoriesUpdated, fetchArticles, loading, user?.preferences?.categories, profileOpen]);

  // Listen for direct categories-saved events
  useEffect(() => {
    const handleCategoriesSaved = (event: any) => {
      const categories = event.detail?.categories || [];
      fetchArticles(true, categories);
    };
    
    window.addEventListener('categories-saved', handleCategoriesSaved);
    
    return () => {
      window.removeEventListener('categories-saved', handleCategoriesSaved);
    };
  }, [fetchArticles]);

  // IntersectionObserver callback for infinite scroll
  const handleObserver = useCallback(
    (entries: IntersectionObserverEntry[]) => {
      const [target] = entries;
      // If the target is intersecting and we are not already loading OR in cooldown...
      if (target.isIntersecting && !loading && !isFetchCooldown) { 
        fetchMore(); 
      }
    },
    // Dependencies: loading state, cooldown state, fetchMore function
    [loading, isFetchCooldown, fetchMore] 
  );

  // Set up the IntersectionObserver
  useEffect(() => {
    const observer = new IntersectionObserver(handleObserver, {
      threshold: 0.1,
      rootMargin: "100px",
    });

    if (observerTarget.current) {
      observer.observe(observerTarget.current);
    }

    return () => observer.disconnect();
  }, [handleObserver]);

  // Filter liked articles for the 'Likes' modal search
  const filteredLikedArticles = likedArticles.filter(
    (article) =>
      article.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      article.extract.toLowerCase().includes(searchQuery.toLowerCase())
  );

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

    const exportFileDefaultName = `quant-favorites-${new Date().toISOString().split("T")[0]
      }.json`;

    const linkElement = document.createElement("a");
    linkElement.setAttribute("href", dataUri);
    linkElement.setAttribute("download", exportFileDefaultName);
    linkElement.click();
  };

  const [textColor, setTextColor] = useState("#d3d3d3"); // Default pale white color

  useEffect(() => {
    // Function to determine text color based on background color
    const updateTextColor = () => {
      const bgColor = window.getComputedStyle(document.body).backgroundColor;
      
      // Parse RGB values from the background color
      const rgb = bgColor
        .replace(/[^\d,]/g, "")
        .split(",")
        .map(Number);
      
      // Calculate the perceived brightness
      const brightness = (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000;
      
      // Set contrasting text color based on background brightness
      const newTextColor = brightness > 128 ? "#000000" : "#ffffff";
      setTextColor(newTextColor);
    };

    // Update text color initially and set up an interval to check regularly
    updateTextColor();
    const intervalId = setInterval(updateTextColor, 500);
    
    // Also update on window resize
    window.addEventListener("resize", updateTextColor);

    return () => {
      clearInterval(intervalId);
      window.removeEventListener("resize", updateTextColor);
    };
  }, []);

  // Common button style with smooth color transition
  const getButtonStyle = (fontSize: string) => ({
    fontFamily: "'Chakra Petch', sans-serif",
    fontSize,
    fontWeight: "bold",
    color: textColor,
    padding: fontSize === "2.5rem" ? "10px 20px" : "5px 15px",
    border: "3px solid transparent",
    borderRadius: "8px",
    background: "transparent",
    backgroundImage: "linear-gradient(#121213,rgb(0, 0, 0)), linear-gradient(90deg, #4A0080, #6A0DAD, #8A2BE2, #9370DB, #9932CC, #800080, #4A0080)",
    backgroundOrigin: "border-box",
    backgroundClip: "padding-box, border-box",
    backgroundSize: "200%",
    cursor: "pointer",
    transition: "transform 0.3s ease, text-shadow 0.3s ease, color 0.5s ease",
  });

  // Special style for the Quant button with violet specters animation
  const getQuantButtonStyle = () => ({
    ...getButtonStyle("2.5rem"),
    position: "relative",
    background: "transparent",
    border: "3px solid transparent",
    borderRadius: "12px",
    backgroundImage: "linear-gradient(#121213,rgb(0, 0, 0)), linear-gradient(90deg, #4A0080, #6A0DAD, #8A2BE2, #9370DB, #9932CC, #800080, #4A0080)",
    backgroundOrigin: "border-box",
    backgroundClip: "padding-box, border-box",
    backgroundSize: "200%",
    animation: "quantButtonAnimate 5s infinite linear",
  });

  // Common hover effects for all buttons
  const handleMouseEnter = (e: React.MouseEvent<HTMLElement>) => {
    e.currentTarget.style.transform = "scale(1.05)";
    e.currentTarget.style.textShadow = `0 0 10px ${textColor === "#000000" ? "rgba(255, 255, 255, 0.7)" : "rgba(0, 0, 0, 0.7)"}`;
    
    // Special handling for Quant button
    if (e.currentTarget.classList.contains('quant-button')) {
      e.currentTarget.style.animation = "quantButtonAnimate 0.5s infinite linear";
    }
  };

  const handleMouseLeave = (e: React.MouseEvent<HTMLElement>) => {
    e.currentTarget.style.transform = "scale(1)";
    e.currentTarget.style.textShadow = "none";
    
    // Special handling for Quant button
    if (e.currentTarget.classList.contains('quant-button')) {
      e.currentTarget.style.animation = "quantButtonAnimate 2s infinite linear";
    }
  };

  // Add keyframes for the animation
  useEffect(() => {
    // Create a style element for the keyframes
    const styleElement = document.createElement('style');
    styleElement.innerHTML = `
      @keyframes quantButtonAnimate {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
      }
      
      /* Add a subtle glow effect for violet specters */
      .quant-button:hover, .nav-button:hover, .read-more-button:hover {
        box-shadow: 0 0 15px rgba(138, 43, 226, 0.6);
        animation: quantButtonAnimate 2s infinite linear;
      }

      /* Atom loading animation */
      @keyframes effect-1 {
        0% {
          transform: perspective(1000px) rotate3d(1, 1, 1, 0deg)
        }
        100% {
          transform: perspective(1000px) rotate3d(1, 1, 1, 360deg)
        }
      }
      
      @keyframes effect-2 {
        0% {
          transform: perspective(1000px) rotate3d(1, -1, 1, 0deg)
        }
        100% {
          transform: perspective(1000px) rotate3d(1, -1, 1, 360deg)
        }
      }
      
      @keyframes effect-3 {
        0%,
        100% {
          opacity: 0
        }
        25%,
        75% {
          opacity: 1
        }
      }
      
      .atom-loader .dot {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background-color: rgba(138, 43, 226, 0.8);
        z-index: 0;
        opacity: 1;
        animation-name: effect-3;
        animation-duration: 5s, 2s;
        animation-iteration-count: infinite;
        animation-timing-function: ease, linear;
        margin: -12.5px 0 0 -12.5px;
      }
      
      .atom-loader .wrapper {
        position: relative;
        width: 150px;
        height: 150px;
      }
      
      .atom-loader .wrapper::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -65px 0 0 -65px;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        opacity: 1;
        z-index: 1;
        border: 2px solid rgba(138, 43, 226, 0.8);
        animation-name: effect-3, effect-1;
        animation-duration: 5s, 2s;
        animation-iteration-count: infinite;
        animation-timing-function: ease, linear;
      }
      
      .atom-loader .wrapper::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -65px 0 0 -65px;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        opacity: 1;
        z-index: 2;
        border: 2px solid rgba(138, 43, 226, 0.8);
        animation-name: effect-3, effect-2;
        animation-duration: 5s, 2s;
        animation-iteration-count: infinite;
        animation-timing-function: ease, linear;
      }
    `;
    document.head.appendChild(styleElement);
    
    return () => {
      document.head.removeChild(styleElement);
    };
  }, []);

  // Listen for category debugging events
  useEffect(() => {
    const handleCategoryTrace = () => {
      // const { category, timestamp } = event.detail || {}; // Removed unused variables
      // Removed category trace log
    };
    
    const handleCategoriesApplied = () => {
      // const { categories } = event.detail || {}; // Removed unused variable
      // Removed categories applied log
    };
    
    // Add event listeners
    window.addEventListener('category-trace', handleCategoryTrace);
    window.addEventListener('categories-applied', handleCategoriesApplied);
    
    return () => {
      window.removeEventListener('category-trace', handleCategoryTrace);
      window.removeEventListener('categories-applied', handleCategoriesApplied);
    };
  }, []);

  // Listen for relevance event notifications
  useEffect(() => {
    const handleLowRelevanceWarning = (event: any) => {
      const { relevancePercent, categories } = event.detail || {};
      
      if (categories && categories.length > 0) {
        setNotification({
          message: `Limited ${categories.join(", ")} content found (${relevancePercent}% relevant). Try selecting different categories.`,
          type: "warning"
        });
        
        // Clear after 8 seconds
        setTimeout(() => setNotification(null), 8000);
      }
    };
    
    window.addEventListener('low-relevance-warning', handleLowRelevanceWarning);
    
    return () => {
      window.removeEventListener('low-relevance-warning', handleLowRelevanceWarning);
    };
  }, []);

  // Listen for wiki article relevance stats
  useEffect(() => {
    const handleArticlesLoaded = () => {
      // No notification handling needed here anymore
    };
    
    window.addEventListener('articles-loaded', handleArticlesLoaded);
    
    return () => {
      window.removeEventListener('articles-loaded', handleArticlesLoaded);
    };
  }, []);

  // Listen for wiki-force-refresh events (debugging)
  useEffect(() => {
    const handleWikiForceRefresh = () => {
      // const { message, categories, timestamp } = event.detail || {}; // Removed unused variables
      
      // Removed force refresh event logs
      
      // Check if we have local storage data
      const storedUser = localStorage.getItem('quantUser');
      if (storedUser) {
        try {
        } catch (err) {
        }
      }
    };
    
    window.addEventListener('wiki-force-refresh', handleWikiForceRefresh);
    
    return () => {
      window.removeEventListener('wiki-force-refresh', handleWikiForceRefresh);
    };
  }, []);

  return (
    <div className="h-screen w-full bg-black text-white overflow-y-scroll snap-y snap-mandatory hide-scroll">
      <div className="fixed top-4 left-4 z-50">
        <button
          onClick={() => window.location.reload()}
          style={getQuantButtonStyle() as React.CSSProperties}
          onMouseEnter={handleMouseEnter}
          onMouseLeave={handleMouseLeave}
          className="quant-button"
        >
          Quant
        </button>
      </div>

      <div className="fixed top-4 right-4 z-50 flex flex-col items-end gap-2">
        {isAuthenticated ? (
          <div className="flex items-center gap-2 mb-2">
            <span className="text-sm text-white/70">Hi, {user?.username}</span>
            <button
              onClick={() => setShowProfile(true)}
              style={getButtonStyle("0.75rem")}
              onMouseEnter={handleMouseEnter}
              onMouseLeave={handleMouseLeave}
              className="nav-button"
            >
              <User className="w-5 h-5 mr-1" />
              Profile
            </button>
          </div>
        ) : (
          <button
            onClick={() => setShowAuth(true)}
            style={getButtonStyle("1rem")}
            onMouseEnter={handleMouseEnter}
            onMouseLeave={handleMouseLeave}
            className="nav-button"
          >
            <User className="w-5 h-5 mr-1" />
            Login
          </button>
        )}
        <button
          onClick={() => setShowAbout(!showAbout)}
          style={getButtonStyle("1rem")}
          onMouseEnter={handleMouseEnter}
          onMouseLeave={handleMouseLeave}
          className="nav-button"
        >
          About
        </button>
        <button
          onClick={() => setShowLikes(!showLikes)}
          style={getButtonStyle("1rem")}
          onMouseEnter={handleMouseEnter}
          onMouseLeave={handleMouseLeave}
          className="nav-button"
        >
          Likes
        </button>
        <div 
          style={{
            ...getButtonStyle("1rem"),
            display: "flex",
            justifyContent: "flex-end",
            alignItems: "center"
          }}
          onMouseEnter={handleMouseEnter}
          onMouseLeave={handleMouseLeave}
          className="nav-button"
        >
          <LanguageSelector />
        </div>
      </div>

      {showAbout && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-gray-900 z-[41] p-6 rounded-lg max-w-md relative">
            <button
              onClick={() => setShowAbout(false)}
              className="absolute top-2 right-2 text-white/70 hover:text-white"
            >
              ✕
            </button>
            <h2 className="text-xl font-bold mb-4">About Quant</h2>
            <p className="mb-4">
              Feed your mind, not the algorithm.
            </p>
            <p className="text-white/70">
              Crafted by{" "}
              <a
                href="https://t.me/quantuw"
                target="_blank"
                rel="noopener noreferrer"
                className="text-white hover:underline"
              >
                @quantuw
              </a>
            </p>
          </div>
          <div
            className={`w-full h-full z-[40] top-1 left-1  bg-[rgb(28 25 23 / 43%)] fixed  ${showAbout ? "block" : "hidden"
              }`}
            onClick={() => setShowAbout(false)}
          ></div>
        </div>
      )}

      {showLikes && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-gray-900 z-[41] p-6 rounded-lg w-full max-w-2xl h-[80vh] flex flex-col relative">
            <button
              onClick={() => setShowLikes(false)}
              className="absolute top-2 right-2 text-white/70 hover:text-white"
            >
              ✕
            </button>

            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold">Liked Articles</h2>
              {likedArticles.length > 0 && (
                <button
                  onClick={handleExport}
                  className="flex items-center gap-2 px-3 py-1.5 text-sm bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors"
                  title="Export liked articles"
                >
                  <Download className="w-4 h-4" />
                  Export
                </button>
              )}
            </div>

            <div className="relative mb-4">
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search liked articles..."
                className="w-full bg-gray-800 text-white px-4 py-2 pl-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <Search className="w-5 h-5 text-white/50 absolute left-3 top-1/2 transform -translate-y-1/2" />
            </div>

            <div className="flex-1 overflow-y-auto min-h-0">
              {filteredLikedArticles.length === 0 ? (
                <p className="text-white/70">
                  {searchQuery ? "No matches found." : "No liked articles yet."}
                </p>
              ) : (
                <div className="space-y-4">
                  {filteredLikedArticles.map((article) => (
                    <div
                      key={`${article.pageid}-${Date.now()}`}
                      className="flex gap-4 items-start group"
                    >
                      {article.thumbnail && (
                        <img
                          src={article.thumbnail.source}
                          alt={article.title}
                          className="w-20 h-20 object-cover rounded"
                        />
                      )}
                      <div className="flex-1">
                        <div className="flex justify-between items-start">
                          <a
                            onClick={(e) => {
                              e.preventDefault();
                              setSelectedArticle(article);
                              setShowLikes(false);
                            }}
                            href="#"
                            className="font-bold hover:text-gray-200 cursor-pointer"
                          >
                            {article.title}
                          </a>
                          <button
                            onClick={() => toggleLike(article)}
                            className="text-white/50 hover:text-white/90 p-1 rounded-full md:opacity-0 md:group-hover:opacity-100 transition-opacity"
                            aria-label="Remove from likes"
                          >
                            <X className="w-4 h-4" />
                          </button>
                        </div>
                        <p className="text-sm text-white/70 line-clamp-2">
                          {article.extract}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
          <div
            className={`w-full h-full z-[40] top-1 left-1  bg-[rgb(28 25 23 / 43%)] fixed  ${showLikes ? "block" : "hidden"
              }`}
            onClick={() => setShowLikes(false)}
          ></div>
        </div>
      )}

      {articlesToRender.map((article) => (
        <WikiCard 
          key={article.pageid} 
          article={article} 
          onLike={toggleLike}
          isLiked={likedArticles.some(likedArticle => likedArticle.pageid === article.pageid)}
        />
      ))}
      <div ref={observerTarget} className="h-10 -mt-1" />
      {loading && (
        <div className="h-screen w-full flex flex-col items-center justify-center gap-4">
          <div className="atom-loader">
            <div className="wrapper">
              <div className="dot"></div>
            </div>
          </div>
        </div>
      )}
      
      {selectedArticle && (
        <ArticleModal
          article={selectedArticle}
          onClose={() => setSelectedArticle(null)}
          onLike={toggleLike}
          isLiked={likedArticles.some(a => a.pageid === selectedArticle.pageid)}
        />
      )}
      
      <Analytics />
      
      {/* Auth Modal */}
      <AuthModal isOpen={showAuth} onClose={() => setShowAuth(false)} />
      
      {/* Profile Modal */}
      <ProfileModal
        isOpen={showProfile}
        onClose={handleProfileClose}
      />

      {notification && (
        <div 
          className={`fixed bottom-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-md shadow-lg z-50 flex items-center space-x-2 ${
            notification.type === 'info' ? 'bg-indigo-800' : 
            notification.type === 'success' ? 'bg-green-700' : 
            notification.type === 'warning' ? 'bg-amber-700' : 'bg-purple-700'
          }`}
          style={{
            maxWidth: '90%',
            animation: 'fadeIn 0.3s',
            boxShadow: '0 0 15px rgba(138, 43, 226, 0.6)'
          }}
        >
          <div className="w-2 h-2 rounded-full bg-white animate-pulse"></div>
          <p className="text-white">{notification.message}</p>
        </div>
      )}
    </div>
  );
}

export default App;