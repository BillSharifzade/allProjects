import { FC } from 'react';
import { X } from 'lucide-react';
import { WikiArticle } from './WikiCard';
import { useLocalization } from '../hooks/useLocalization';
import { useState, useEffect } from 'react';

interface ArticleModalProps {
  article: WikiArticle;
  onClose: () => void;
  onLike: (article: WikiArticle) => void;
  isLiked: boolean;
}

const ArticleModal: FC<ArticleModalProps> = ({ article, onClose, onLike, isLiked }) => {
  const [content, setContent] = useState<string>('');
  const [loading, setLoading] = useState<boolean>(true);
  const { currentLanguage } = useLocalization();

  useEffect(() => {
    const fetchArticleContent = async () => {
      setLoading(true);
      try {
        // Get article title from URL
        const titleFromUrl = article.url.split('/wiki/')[1];
        
        // Fetch the full article content
        const response = await fetch(
          `${currentLanguage.api}${new URLSearchParams({
            action: "parse",
            page: decodeURIComponent(titleFromUrl),
            prop: "text",
            format: "json",
            origin: "*"
          })}`
        );
        
        const data = await response.json();
        
        if (data.parse && data.parse.text) {
          // Clean up the HTML content
          let htmlContent = data.parse.text['*'];
          
          // Remove edit links, references, etc.
          htmlContent = htmlContent
            .replace(/<span class="mw-editsection">.*?<\/span>/g, '')
            .replace(/<a class="mw-jump-link".*?<\/a>/g, '');
          
          setContent(htmlContent);
        }
      } catch (error) {
        console.error("Error fetching article content:", error);
        setContent(`<p>Failed to load article content. Please try again later.</p>`);
      } finally {
        setLoading(false);
      }
    };

    fetchArticleContent();
  }, [article, currentLanguage]);

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
      <div className="bg-gray-900 rounded-lg w-full max-w-4xl h-[90vh] relative overflow-hidden flex flex-col">
        <div className="p-4 border-b border-gray-800 flex justify-between items-center">
          <h2 className="text-2xl font-bold text-white">{article.displaytitle || article.title}</h2>
          <button
            onClick={onClose}
            className="text-white/70 hover:text-white p-2 rounded-full"
            aria-label="Close article"
          >
            <X className="w-6 h-6" />
          </button>
        </div>
        
        <div className="flex-grow overflow-auto p-6 relative">
          {loading ? (
            <div className="h-full w-full flex flex-col items-center justify-center">
              <div className="atom-loader">
                <div className="wrapper">
                  <div className="dot"></div>
                </div>
              </div>
              <span className="mt-4 text-lg" style={{ fontFamily: "'Chakra Petch', sans-serif" }}>Loading article...</span>
            </div>
          ) : (
            <>
              {article.thumbnail && (
                <div className="float-right ml-6 mb-6">
                  <img 
                    src={article.thumbnail.source} 
                    alt={article.title}
                    className="rounded-lg max-w-xs"
                  />
                </div>
              )}
              <div 
                className="wiki-content-container text-gray-100 text-lg"
                dangerouslySetInnerHTML={{ __html: content }}
              />
            </>
          )}
        </div>
        
        <div className="p-4 border-t border-gray-800 flex justify-between">
          <button
            onClick={() => window.open(article.url, '_blank')}
            className="read-more-button text-white flex items-center text-lg"
            style={{
              fontFamily: "'Chakra Petch', sans-serif",
              fontWeight: "bold",
              padding: "5px 15px",
              border: "3px solid transparent",
              borderRadius: "8px",
              background: "transparent",
              backgroundImage: "linear-gradient(#121213,rgb(0, 0, 0)), linear-gradient(90deg, #4A0080, #6A0DAD, #8A2BE2, #9370DB, #9932CC, #800080, #4A0080)",
              backgroundOrigin: "border-box",
              backgroundClip: "padding-box, border-box",
              backgroundSize: "200%",
              transition: "transform 0.3s ease, text-shadow 0.3s ease, color 0.5s ease",
            }}
            onMouseEnter={(e) => {
              e.currentTarget.style.transform = "scale(1.05)";
              e.currentTarget.style.textShadow = "0 0 10px rgba(255, 255, 255, 0.7)";
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.transform = "scale(1)";
              e.currentTarget.style.textShadow = "none";
            }}
          >
            View on Wikipedia
          </button>
          <button
            onClick={() => onLike(article)}
            className={`p-2 rounded-full transition-all duration-300 ${
              isLiked ? "text-red-500" : "text-gray-400 hover:text-red-400"
            }`}
            aria-label={isLiked ? "Unlike" : "Like"}
          >
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              width="24" 
              height="24" 
              viewBox="0 0 24 24" 
              fill={isLiked ? "currentColor" : "none"} 
              stroke="currentColor" 
              strokeWidth="2" 
              strokeLinecap="round" 
              strokeLinejoin="round"
            >
              <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  );
};

export default ArticleModal;