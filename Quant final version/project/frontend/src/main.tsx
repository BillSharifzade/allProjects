import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App.tsx';
import './index.css';
import { AuthProvider } from './contexts/AuthContext.tsx';
import { LocalizationProvider } from './contexts/LocalizationContext.tsx';
import { LikedArticlesProvider } from './contexts/LikedArticlesContext.tsx';
import './i18n';

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    {/* I18nextProvider typically wraps everything */}
    <AuthProvider>
      <LocalizationProvider>
        <LikedArticlesProvider>
          <App />
        </LikedArticlesProvider>
      </LocalizationProvider>
    </AuthProvider>
  </React.StrictMode>,
);