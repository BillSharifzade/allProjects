import { useState } from 'react';
import { X } from 'lucide-react';
import LoginForm from './LoginForm';
import RegisterForm from './RegisterForm';

interface AuthModalProps {
  isOpen: boolean;
  onClose: () => void;
}

const AuthModal: React.FC<AuthModalProps> = ({ isOpen, onClose }) => {
  const [activeTab, setActiveTab] = useState<'login' | 'register'>('login');

  return (
    <div
      className={`fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200 ease-out ${isOpen ? 'opacity-100 bg-black/70 backdrop-blur-sm' : 'opacity-0 pointer-events-none'}`}
      onClick={onClose}
    >
      <div
        className={`bg-gray-900/80 border border-gray-700/50 z-[41] p-6 rounded-lg w-full max-w-md relative shadow-xl transition-all duration-200 ease-out ${isOpen ? 'opacity-100 scale-100 translate-y-0' : 'opacity-90 scale-95 translate-y-4 pointer-events-none'}`}
        onClick={(e: React.MouseEvent) => e.stopPropagation()}
      >
        <button
          onClick={onClose}
          className="absolute top-2 right-2 text-white/70 hover:text-white transition-colors"
          aria-label="Close authentication modal"
        >
          <X size={20} />
        </button>
        
        <div className="flex mb-6">
          <button
            onClick={() => setActiveTab('login')}
            className={`flex-1 py-2 text-center transition-colors ${
              activeTab === 'login'
                ? 'text-white border-b-2 border-purple-500'
                : 'text-white/50 hover:text-white/70'
            }`}
          >
            Login
          </button>
          <button
            onClick={() => setActiveTab('register')}
            className={`flex-1 py-2 text-center transition-colors ${
              activeTab === 'register'
                ? 'text-white border-b-2 border-purple-500'
                : 'text-white/50 hover:text-white/70'
            }`}
          >
            Register
          </button>
        </div>
        
        {activeTab === 'login' ? (
          <LoginForm onSuccess={onClose} />
        ) : (
          <RegisterForm onSuccess={() => {
            setActiveTab('login');
          }} />
        )}
      </div>
    </div>
  );
};

export default AuthModal;