import React from 'react';
// import { motion } from 'framer-motion'; // Remove framer-motion

interface AboutModalProps {
  isOpen: boolean; // Need isOpen prop to control visibility classes
  onClose: () => void;
}

// Remove modalVariants

const AboutModal: React.FC<AboutModalProps> = ({ isOpen, onClose }) => {
  // Use isOpen to control classes for transition
  return (
    // Backdrop with transition
    <div
      className={`fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200 ease-out ${isOpen ? 'opacity-100 bg-black/70 backdrop-blur-sm' : 'opacity-0 pointer-events-none'}`}
      onClick={onClose}
    >
      {/* Modal content with transition */}
      <div
        className={`bg-gray-900/80 border border-gray-700/50 z-[41] p-6 rounded-lg max-w-md relative shadow-xl transition-all duration-200 ease-out ${isOpen ? 'opacity-100 scale-100 translate-y-0' : 'opacity-90 scale-95 translate-y-4 pointer-events-none'}`}
        onClick={(e: React.MouseEvent) => e.stopPropagation()}
      >
        <button
          onClick={onClose}
          className="absolute top-2 right-2 text-white/70 hover:text-white transition-colors"
          aria-label="Close About modal"
        >
          ✕
        </button>
        <h2 className="text-xl font-bold mb-4">About Qwant</h2>
        <p className="mb-4">
          Feed your mind, not the algorithm.
        </p>
        <p className="text-white/70">
          Crafted by{" "}
          <a
            href="https://t.me/qwantuw"
            target="_blank"
            rel="noopener noreferrer"
            className="text-white hover:underline"
          >
            @qwantuw
          </a>
        </p>
      </div>
    </div>
  );
};

export default AboutModal; 