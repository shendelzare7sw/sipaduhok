/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './app/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#e8f4ff',
          100: '#d5eaff',
          200: '#b9ddff',
          300: '#86c5ff',
          400: '#4ca8ff',
          500: '#1e90ff',
          600: '#1874cd',
          700: '#185ca8',
          800: '#184d86',
          900: '#1b3a6b',
          950: '#112549',
        },
      },
      boxShadow: {
        soft: '0 10px 30px -12px rgb(15 23 42 / 0.14)',
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
