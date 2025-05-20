'use client';

import { useState, useEffect, useRef } from 'react';

interface ContentCard {
  id: number;
  title: string;
  description: string;
  category: string;
  type: 'article' | 'video' | 'image';
  imageUrl: string;
}

export default function Science() {
  const [activeFilter, setActiveFilter] = useState('all');
  const sectionRef = useRef<HTMLElement>(null);
  
  const contentCards: ContentCard[] = [
    {
      id: 1,
      title: 'The Mystery of Dark Matter',
      description: 'Exploring the invisible force that holds galaxies together',
      category: 'physics',
      type: 'article',
      imageUrl: 'https://images.unsplash.com/photo-1462331940025-496dfbfc7564?q=80&w=1000',
    },
    {
      id: 2,
      title: 'Quantum Computing Explained',
      description: 'How qubits are revolutionizing computing power',
      category: 'technology',
      type: 'video',
      imageUrl: 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?q=80&w=1000',
    },
    {
      id: 3,
      title: 'Neural Networks Visualization',
      description: 'The beautiful architecture of artificial intelligence',
      category: 'ai',
      type: 'image',
      imageUrl: 'https://images.unsplash.com/photo-1545987796-200677ee1011?q=80&w=1000',
    },
    {
      id: 4,
      title: 'The Future of Green Energy',
      description: 'Sustainable technologies powering tomorrow',
      category: 'environment',
      type: 'article',
      imageUrl: 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?q=80&w=1000',
    },
    {
      id: 5,
      title: 'Exploring Deep Ocean Life',
      description: 'Creatures of the abyss and their unique adaptations',
      category: 'biology',
      type: 'video',
      imageUrl: 'https://images.unsplash.com/photo-1551244072-5d12893278ab?q=80&w=1000',
    },
    {
      id: 6,
      title: 'The Human Genome Project',
      description: 'Mapping the blueprint of human life',
      category: 'biology',
      type: 'article',
      imageUrl: 'https://images.unsplash.com/photo-1530210124550-912dc1381cb8?q=80&w=1000',
    },
  ];

  const filteredCards = activeFilter === 'all' 
    ? contentCards 
    : contentCards.filter(card => card.category === activeFilter || card.type === activeFilter);

  const filters = [
    { id: 'all', label: 'All Content' },
    { id: 'article', label: 'Articles' },
    { id: 'video', label: 'Videos' },
    { id: 'image', label: 'Images' },
    { id: 'physics', label: 'Physics' },
    { id: 'biology', label: 'Biology' },
    { id: 'technology', label: 'Technology' },
  ];

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('opacity-100');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );

    if (sectionRef.current) {
      observer.observe(sectionRef.current);
    }

    return () => {
      if (sectionRef.current) {
        observer.unobserve(sectionRef.current);
      }
    };
  }, []);

  return (
    <section 
      id="science" 
      ref={sectionRef}
      className="py-20 opacity-0 transition-opacity duration-1000 relative"
    >
      <div className="absolute inset-0 bg-gradient-to-b from-black via-purple-900/10 to-black z-0"></div>
      
      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-12">
          <h2 className="text-3xl md:text-5xl font-bold mb-6 gradient-text">
            Scientific Discovery Feed
          </h2>
          <p className="text-lg max-w-3xl mx-auto text-gray-300 mb-8">
            Preview the type of high-quality scientific content you'll discover on Quant.
            Fascinating insights await with every scroll.
          </p>
          
          <div className="flex flex-wrap justify-center gap-2 mb-10">
            {filters.map(filter => (
              <button
                key={filter.id}
                onClick={() => setActiveFilter(filter.id)}
                className={`px-4 py-2 rounded-full text-sm transition-all duration-300 ${
                  activeFilter === filter.id
                    ? 'bg-secondary text-black'
                    : 'bg-black/30 text-white/80 border border-white/10 hover:border-secondary/50'
                }`}
              >
                {filter.label}
              </button>
            ))}
          </div>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredCards.map((card, index) => (
            <div 
              key={card.id}
              className="bg-black/40 backdrop-blur-sm border border-white/10 rounded-lg overflow-hidden transition-all duration-300 hover:transform hover:scale-[1.02] hover:border-secondary/50 group animate-fadeIn"
              style={{ animationDelay: `${index * 150}ms` }}
            >
              <div className="h-48 overflow-hidden relative">
                <div 
                  className="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                  style={{ backgroundImage: `url(${card.imageUrl})` }}
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60" />
                <span className="absolute top-2 right-2 bg-black/70 text-xs font-medium uppercase tracking-wider text-secondary px-2 py-1 rounded">
                  {card.type}
                </span>
              </div>
              
              <div className="p-5">
                <h3 className="text-xl font-bold mb-2 text-white group-hover:text-secondary transition-colors">
                  {card.title}
                </h3>
                <p className="text-gray-300 text-sm mb-3">
                  {card.description}
                </p>
                <div className="flex justify-between items-center">
                  <span className="text-xs bg-white/10 rounded-full px-3 py-1 text-white/70">
                    #{card.category}
                  </span>
                  <button className="text-secondary text-sm hover:underline">
                    Read more
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
} 