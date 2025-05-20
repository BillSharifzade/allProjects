import ProfileForm from './ProfileForm';
import { X } from 'lucide-react';

interface ProfileModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function ProfileModal({ isOpen, onClose }: ProfileModalProps) {
  return (
    <div
      className={`fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200 ease-out ${isOpen ? 'opacity-100 bg-black/70 backdrop-blur-sm' : 'opacity-0 pointer-events-none'}`}
      onClick={onClose}
    >
      <div
        className={`bg-gray-900/80 border border-gray-700/50 p-6 rounded-lg w-full max-w-md relative shadow-xl transition-all duration-200 ease-out ${isOpen ? 'opacity-100 scale-100 translate-y-0' : 'opacity-90 scale-95 translate-y-4 pointer-events-none'}`}
        onClick={(e: React.MouseEvent) => e.stopPropagation()}
      >
        <button 
          onClick={onClose}
          className="absolute top-2 right-2 text-gray-400 hover:text-white transition-colors"
          aria-label="Close profile modal"
        >
          <X size={24} />
        </button>
        
        <h2 className="text-xl font-semibold mb-4 text-purple-300">Your Profile</h2>
        <ProfileForm onClose={onClose} />
      </div>
    </div>
  );
} 