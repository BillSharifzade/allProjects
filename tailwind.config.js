/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'crypto-blue': '#1E40AF',
        'crypto-green': '#10B981',
        'crypto-red': '#EF4444',
        'crypto-yellow': '#F59E0B',
        'telegram-blue': '#0088CC',
      },
      fontFamily: {
        'sans': ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}