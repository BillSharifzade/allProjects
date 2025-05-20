module.exports = {
  theme: {
    extend: {
      fontFamily: {
        orbitron: ['Orbitron', 'sans-serif'],
        audiowide: ['Audiowide', 'cursive'],
        vt323: ['VT323', 'monospace'],
        shareTech: ['Share Tech Mono', 'monospace'],
        chakra: ["'Chakra Petch'", 'sans-serif'],
      },
      animation: {
        glow: 'glow 1.5s infinite alternate',
        pulse: 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        glow: {
          '0%': { textShadow: '0 0 5px #00f, 0 0 10px #00f, 0 0 20px #00f' },
          '100%': { textShadow: '0 0 10px #0ff, 0 0 20px #0ff, 0 0 40px #0ff' },
        },
        pulse: {
          '0%, 100%': { opacity: 1 },
          '50%': { opacity: .5 },
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
};