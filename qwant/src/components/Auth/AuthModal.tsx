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

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div className="bg-gray-900 z-[41] p-6 rounded-lg w-full max-w-md relative">
        <button
          onClick={onClose}
          className="absolute top-2 right-2 text-white/70 hover:text-white"
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
      <div
        className="w-full h-full z-[40] top-1 left-1 bg-[rgb(28 25 23 / 43%)] fixed"
        onClick={onClose}
      ></div>
    </div>
  );
};

export default AuthModal;