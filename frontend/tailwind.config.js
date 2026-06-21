/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#f2f5f1',
          100: '#e1e9df',
          200: '#c5d5c1',
          300: '#a8bc9a', /* muted leaf green */
          400: '#809a71',
          500: '#5c7a52', /* sage army green */
          600: '#465e3e',
          700: '#3b4a3a', /* green army dark */
          800: '#2e3d2c', /* sidebar dark */
          900: '#263324',
          950: '#141c13',
        },
        sidebar: '#2e3d2c',
        parchment: '#f4f0e8',
        stone: '#eae5da',
        sand: '#c8b89a',
        ochre: '#c27a38',
        dark: '#2e3d2c',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
