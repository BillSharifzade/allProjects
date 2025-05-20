'use client';

import { useEffect, useRef } from 'react';
import Image from 'next/image';

interface FeatureProps {
  title: string;
  description: string;
  icon: string;
  index: number;
}

const Feature = ({ title, description, icon, index }: FeatureProps) => {
  const featureRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('opacity-100', 'translate-y-0');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );

    if (featureRef.current) {
      observer.observe(featureRef.current);
    }

    return () => {
      if (featureRef.current) {
        observer.unobserve(featureRef.current);
      }
    };
  }, []);

  return (
    <div
      ref={featureRef}
      className={`bg-black/30 backdrop-blur-sm border border-white/10 rounded-lg p-6 flex flex-col items-center text-center transform opacity-0 translate-y-8 transition-all duration-700 ease-out`}
      style={{ transitionDelay: `${index * 150}ms` }}
    >
      <div className="w-16 h-16 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center mb-4">
        <span className="text-2xl text-white">{icon}</span>
      </div>
      <h3 className="text-xl font-bold mb-3 gradient-text">{title}</h3>
      <p className="text-gray-300">{description}</p>
    </div>
  );
};

export default function Features() {
  const features = [
    {
      title: 'Scientific Content',
      description: 'Access a curated feed of fascinating scientific articles, papers, and discoveries.',
      icon: '🧪',
    },
    {
      title: 'Visual Learning',
      description: 'Engaging scientific imagery and visualizations that make complex concepts accessible.',
      icon: '🔭',
    },
    {
      title: 'Skill Development',
      description: 'Build valuable professional and personal skills while you scroll.',
      icon: '🧠',
    },
    {
      title: 'Personalized Feed',
      description: 'Smart algorithms that learn what scientific topics interest you most.',
      icon: '🔬',
    },
    {
      title: 'Dopamine Detox',
      description: 'Break the cycle of addictive short-form content while still enjoying your screen time.',
      icon: '⚡',
    },
    {
      title: 'Community',
      description: 'Connect with like-minded individuals passionate about science and learning.',
      icon: '🌐',
    },
  ];

  const sectionRef = useRef<HTMLElement>(null);

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
      id="features"
      ref={sectionRef}
      className="py-20 relative opacity-0 transition-opacity duration-1000"
    >
      <div className="absolute inset-0 bg-gradient-to-b from-black via-blue-900/10 to-black z-0"></div>
      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-5xl font-bold mb-6 gradient-text">
            Why Choose Quant?
          </h2>
          <p className="text-lg max-w-3xl mx-auto text-gray-300">
            Quant transforms your digital habits by replacing mindless scrolling with enriching scientific content.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <Feature
              key={index}
              title={feature.title}
              description={feature.description}
              icon={feature.icon}
              index={index}
            />
          ))}
        </div>
      </div>
    </section>
  );
} 