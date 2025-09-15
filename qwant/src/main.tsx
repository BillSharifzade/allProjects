import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App.tsx';
import './index.css';
import { LikedArticlesProvider } from './contexts/LikedArticlesContext.tsx';
import { LocalizationProvider } from './contexts/LocalizationContext.tsx';
import { AuthProvider } from './contexts/AuthContext.tsx';

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <AuthProvider>
        <LocalizationProvider>
          <LikedArticlesProvider>
            <App />
          </LikedArticlesProvider>
        </LocalizationProvider>
    </AuthProvider>
  </React.StrictMode>,
);
