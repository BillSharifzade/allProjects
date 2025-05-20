import React from 'react';
// import { motion } from 'framer-motion'; // Remove framer-motion
import { Search, X, Download } from 'lucide-react';
import { WikiArticle } from './WikiCard'; // Assuming WikiArticle interface is exported or moved

interface LikesModalProps {
  isOpen: boolean; // Add isOpen prop
  likedArticles: WikiArticle[];
  toggleLike: (article: WikiArticle) => void;
  searchQuery: string;
  setSearchQuery: (query: string) => void;
  handleExport: () => void;
  setSelectedArticle: (article: WikiArticle | null) => void;
  onClose: () => void;
}

const LikesModal: React.FC<LikesModalProps> = ({
  isOpen,
  likedArticles,
  toggleLike,
  searchQuery,
  setSearchQuery,
  handleExport,
  setSelectedArticle,
  onClose,
}) => {

  // Filter liked articles locally within the modal
  const filteredLikedArticles = likedArticles.filter(
    (article) =>
      article.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      article.extract.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    // Backdrop with transition
    <div
      className={`fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200 ease-out ${isOpen ? 'opacity-100 bg-black/70 backdrop-blur-sm' : 'opacity-0 pointer-events-none'}`}
      onClick={onClose}
    >
      {/* Modal content with transition */}
      <div
        className={`bg-gray-900/80 border border-gray-700/50 z-[41] p-6 rounded-lg w-full max-w-2xl h-[80vh] flex flex-col relative shadow-xl transition-all duration-200 ease-out ${isOpen ? 'opacity-100 scale-100 translate-y-0' : 'opacity-90 scale-95 translate-y-4 pointer-events-none'}`}
        onClick={(e: React.MouseEvent) => e.stopPropagation()}
      >
        <button
          onClick={onClose}
          className="absolute top-2 right-2 text-white/70 hover:text-white transition-colors"
          aria-label="Close Likes modal"
        >
          ✕
        </button>

        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">Liked Articles</h2>
          {likedArticles.length > 0 && (
            <button
              onClick={handleExport}
              className="flex items-center gap-2 px-3 py-1.5 text-sm bg-gray-800/80 hover:bg-gray-700/80 rounded-lg transition-colors border border-gray-600/50"
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
            className="w-full bg-gray-800/80 text-white px-4 py-2 pl-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 border border-gray-700/50"
          />
          <Search className="w-5 h-5 text-white/50 absolute left-3 top-1/2 transform -translate-y-1/2" />
        </div>

        <div className="flex-1 overflow-y-auto min-h-0 pr-2 hide-scroll">
          {filteredLikedArticles.length === 0 ? (
            <p className="text-white/70">
              {searchQuery ? "No matches found." : "No liked articles yet."}
            </p>
          ) : (
            <div className="space-y-4">
              {filteredLikedArticles.map((article) => (
                <div
                  key={`${article.pageid}-${Date.now()}`} // Consider more stable key if possible
                  className="flex gap-4 items-start group p-2 rounded hover:bg-gray-800/50 transition-colors"
                >
                  {article.thumbnail && (
                    <img
                      src={article.thumbnail.source}
                      alt={article.title}
                      className="w-20 h-20 object-cover rounded flex-shrink-0"
                    />
                  )}
                  <div className="flex-1 min-w-0">
                    <div className="flex justify-between items-start">
                      <a
                        onClick={(e) => {
                          e.preventDefault();
                          setSelectedArticle(article);
                          onClose(); // Close Likes modal when opening article
                        }}
                        href="#"
                        className="font-bold hover:text-gray-200 cursor-pointer truncate pr-2"
                        title={article.title}
                      >
                        {article.title}
                      </a>
                      <button
                        onClick={() => toggleLike(article)}
                        className="text-white/50 hover:text-white/90 p-1 rounded-full opacity-50 group-hover:opacity-100 transition-opacity flex-shrink-0"
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
    </div>
  );
};

export default LikesModal; 