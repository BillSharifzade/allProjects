'use client';

import { useEffect, useRef } from 'react';

interface Skill {
  name: string;
  description: string;
  progress: number;
  color: string;
}

export default function Skills() {
  const sectionRef = useRef<HTMLElement>(null);
  
  const skills: Skill[] = [
    {
      name: 'Critical Thinking',
      description: 'Develop analytical skills to evaluate complex scientific topics',
      progress: 85,
      color: 'from-blue-500 to-cyan-400',
    },
    {
      name: 'Scientific Literacy',
      description: 'Understand key scientific concepts and methodologies',
      progress: 90,
      color: 'from-purple-500 to-pink-500',
    },
    {
      name: 'Data Interpretation',
      description: 'Learn to analyze and interpret data visualizations and statistics',
      progress: 80,
      color: 'from-green-500 to-emerald-400',
    },
    {
      name: 'Technology Awareness',
      description: 'Stay informed about cutting-edge scientific and technological advancements',
      progress: 95,
      color: 'from-yellow-500 to-orange-400',
    },
    {
      name: 'Research Skills',
      description: 'Develop the ability to find and evaluate credible scientific sources',
      progress: 75,
      color: 'from-red-500 to-pink-400',
    },
  ];
  
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-skills');
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
      id="skills" 
      ref={sectionRef}
      className="py-20 relative"
    >
      <div className="absolute inset-0 bg-gradient-to-b from-black via-blue-900/10 to-black z-0"></div>
      
      <style jsx global>{`
        @keyframes progress {
          0% { width: 0; }
          100% { width: var(--progress); }
        }
        
        .animate-skills .skill-progress::before {
          animation: progress 1.2s ease-in-out forwards;
        }
      `}</style>
      
      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-5xl font-bold mb-6 gradient-text">
            Skills You'll Develop
          </h2>
          <p className="text-lg max-w-3xl mx-auto text-gray-300">
            Quant doesn't just entertain - it helps you grow intellectually and professionally.
            Here are some of the skills you'll develop while using our platform.
          </p>
        </div>
        
        <div className="max-w-4xl mx-auto">
          {skills.map((skill, index) => (
            <div 
              key={index} 
              className="mb-10"
              style={{ animationDelay: `${index * 200}ms` }}
            >
              <div className="flex justify-between mb-2">
                <h3 className="text-xl font-bold">{skill.name}</h3>
                <span className="text-secondary">{skill.progress}%</span>
              </div>
              <p className="text-gray-400 mb-3">{skill.description}</p>
              <div className="h-2 bg-gray-800 rounded-full overflow-hidden relative">
                <div 
                  className={`skill-progress h-full rounded-full bg-gradient-to-r ${skill.color} absolute top-0 left-0`}
                  style={{ '--progress': `${skill.progress}%` } as React.CSSProperties}
                ></div>
              </div>
            </div>
          ))}
        </div>
        
        <div className="mt-16 text-center">
          <h3 className="text-2xl font-bold mb-6 gradient-text">Start Building Your Skills Today</h3>
          <button className="sci-button text-lg">
            Join Quant Beta
          </button>
        </div>
      </div>
    </section>
  );
} 