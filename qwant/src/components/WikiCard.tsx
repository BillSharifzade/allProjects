import { useState } from "react";
import { Heart } from "lucide-react";
import { useLocalization } from "../hooks/useLocalization";
import { FC } from 'react';

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
  relevanceScore?: number;
}

interface WikiCardProps {
  article: WikiArticle;
  onLike: (article: WikiArticle) => void;
  isLiked: boolean;
}

export const WikiCard: FC<WikiCardProps> = ({ article, onLike, isLiked }) => {
  const [expanded, setExpanded] = useState(false);
  const [loading, setLoading] = useState(false);
  const [fullContent, setFullContent] = useState("");
  const { currentLanguage } = useLocalization();

  const handleReadMore = async (e: React.MouseEvent) => {
    e.preventDefault();
    
    if (expanded) {
      setExpanded(false);
      return;
    }
    
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
        
        setFullContent(htmlContent);
        setExpanded(true);
      } else {
        // Handle case when parse data is missing
        window.open(article.url, "_blank");
      }
    } catch (error) {
      // Fallback to original link if fetch fails
      window.open(article.url, "_blank");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="snap-start h-screen w-full flex items-center justify-center">
      <div className="bg-gray-900 rounded-lg overflow-hidden w-full h-full max-h-screen shadow-xl flex flex-col relative">
        {/* Background Image (if thumbnail exists) */}
        {article.thumbnail && (
          <div className="absolute inset-0 z-0"> {/* Positioned behind content */}
            <div className="absolute inset-0 bg-black opacity-80 z-10"></div> {/* Dark overlay */}
            <img
              src={article.thumbnail.source}
              alt={article.title}
              className="w-full h-full object-cover" // Cover the area
              loading="lazy" // Lazy load image
              decoding="async" // Decode image asynchronously
            />
          </div>
        )}
        
        {/* Fallback Background (if thumbnail is missing) */}
        {!article.thumbnail && (
          <div className="absolute inset-0 z-0 bg-gradient-to-br from-gray-900 via-gray-800 to-purple-900 opacity-80"></div>
        )}
        
        {/* Content Area: Padding, flex-grow, scrollable, positioned above background */}
        <div className="pt-100 px-6 pb-6 flex-grow overflow-auto relative z-20"> {/* Increased top padding to lower text */}
          <h2 className="text-3xl font-bold mb-4 text-white">
            {article.displaytitle || article.title}
          </h2>
          
          {!expanded ? (
            <p className="text-gray-100 mb-6 text-xl">{article.extract}</p>
          ) : (
            <div 
              className="text-gray-100 mb-6 wiki-content-container text-lg bg-black bg-opacity-50 p-4 rounded-lg"
              dangerouslySetInnerHTML={{ __html: fullContent }}
            />
          )}
          
          <div className="flex justify-between items-center mt-auto">
            {loading ? (
              <button
                className="text-white flex items-center text-lg"
                disabled
                style={{
                  minWidth: "120px",
                  minHeight: "40px",
                  display: "flex",
                  justifyContent: "center",
                  alignItems: "center"
                }}
              >
                <div className="atom-loader" style={{ transform: "scale(0.5)" }}>
                  <div className="wrapper">
                    <div className="dot"></div>
                  </div>
                </div>
              </button>
            ) : expanded ? (
              <button
                onClick={handleReadMore}
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
                Show Less
              </button>
            ) : (
              <button
                onClick={handleReadMore}
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
                Read More
              </button>
            )}
            
            <button
              onClick={() => onLike(article)}
              className={`p-2 rounded-full transition-all duration-300 ${
                isLiked ? "text-red-500" : "text-gray-400 hover:text-red-400"
              }`}
              aria-label={isLiked ? "Unlike" : "Like"}
            >
              <Heart
                className={`w-6 h-6 ${isLiked ? "fill-current" : ""}`}
              />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
