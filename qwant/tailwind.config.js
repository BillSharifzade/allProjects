module.exports = {
  theme: {
    extend: {
      fontFamily: {
        orbitron: ['Orbitron', 'sans-serif'],
        audiowide: ['Audiowide', 'cursive'],
        vt323: ['VT323', 'monospace'],
        shareTech: ['Share Tech Mono', 'monospace'],
      },
      animation: {
        glow: 'glow 1.5s infinite alternate',
      },
      keyframes: {
        glow: {
          '0%': { textShadow: '0 0 5px #00f, 0 0 10px #00f, 0 0 20px #00f' },
          '100%': { textShadow: '0 0 10px #0ff, 0 0 20px #0ff, 0 0 40px #0ff' },
        },
      },
    },
  },
  plugins: [],
};