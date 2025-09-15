'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function Navbar() {
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 50) {
        setScrolled(true);
      } else {
        setScrolled(false);
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <nav 
      className={`fixed w-full z-50 transition-all duration-300 ${
        scrolled 
          ? 'bg-black/80 backdrop-blur-md py-3' 
          : 'bg-transparent py-5'
      }`}
    >
      <div className="container mx-auto px-4 md:px-6">
        <div className="flex items-center justify-between">
          <Link 
            href="/" 
            className="flex items-center space-x-2"
          >
            <div className="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-secondary animate-pulse flex items-center justify-center">
              <span className="font-bold text-black">Q</span>
            </div>
            <span className="text-xl font-bold gradient-text">Quant</span>
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-8">
            <Link href="#features" className="text-white hover:text-secondary transition-colors duration-200">
              Features
            </Link>
            <Link href="#science" className="text-white hover:text-secondary transition-colors duration-200">
              Science
            </Link>
            <Link href="#skills" className="text-white hover:text-secondary transition-colors duration-200">
              Skills
            </Link>
            <Link href="#contact" className="text-white hover:text-secondary transition-colors duration-200">
              Contact
            </Link>
            <button className="sci-button">
              Join Beta
            </button>
          </div>

          {/* Mobile Menu Button */}
          <button 
            onClick={() => setMenuOpen(!menuOpen)}
            className="md:hidden text-white"
            aria-label="Toggle menu"
          >
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor" 
              className="w-6 h-6"
            >
              {menuOpen ? (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              ) : (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16m-7 6h7" />
              )}
            </svg>
          </button>
        </div>

        {/* Mobile Menu */}
        {menuOpen && (
          <div className="md:hidden fixed inset-0 top-16 bg-black/95 animate-fadeIn">
            <div className="flex flex-col items-center pt-10 space-y-6">
              <Link 
                href="#features" 
                className="text-white hover:text-secondary transition-colors duration-200"
                onClick={() => setMenuOpen(false)}
              >
                Features
              </Link>
              <Link 
                href="#science" 
                className="text-white hover:text-secondary transition-colors duration-200"
                onClick={() => setMenuOpen(false)}
              >
                Science
              </Link>
              <Link 
                href="#skills" 
                className="text-white hover:text-secondary transition-colors duration-200"
                onClick={() => setMenuOpen(false)}
              >
                Skills
              </Link>
              <Link 
                href="#contact" 
                className="text-white hover:text-secondary transition-colors duration-200"
                onClick={() => setMenuOpen(false)}
              >
                Contact
              </Link>
              <button className="sci-button">
                Join Beta
              </button>
            </div>
          </div>
        )}
      </div>
    </nav>
  );
} 