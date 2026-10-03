/** @type {import('tailwindcss').Config} */
// Tailwind khusus halaman publik (landing + auth). Dipisah dari tailwind.config.js karena
// warna `primary`/`secondary` di sini akan menimpa kelas Bootstrap bridge di halaman admin.
// Nilai tema disalin dari konfigurasi Tailwind CDN lama agar tampilan tidak berubah.
export default {
  content: [
    './resources/views/*.blade.php',
    './resources/views/auth/**/*.blade.php',
    './resources/views/landing/**/*.blade.php',
    './resources/views/partials/program-*.blade.php',
    './resources/views/layouts/landing.blade.php',
    './resources/views/layouts/auth.blade.php',
    './resources/views/components/navbar.blade.php',
    './resources/views/components/footer.blade.php',
    './resources/views/components/seo-meta.blade.php',
    './resources/views/components/landing/**/*.blade.php',
    './app/Models/Berita.php',
    './resources/views/components/lms/lightbox.blade.php',
    './resources/js/components/media-lightbox.js',
    './resources/js/public-site.js',
    './resources/js/public-site/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#165fac',
        secondary: '#287f3b',
        'accent-orange': '#d45930',
        'accent-yellow': '#fac030',
        'accent-bright': '#ffe400',
        cream: '#e8e7e2',
      },
      fontFamily: {
        poppins: ['Poppins', 'sans-serif'],
      },
      keyframes: {
        'float-putar': {
          '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
          '25%': { transform: 'translateY(-30px) rotate(90deg)' },
          '50%': { transform: 'translateY(-60px) rotate(180deg)' },
          '75%': { transform: 'translateY(-30px) rotate(270deg)' },
        },
        'geser-gradien': {
          '0%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' },
          '100%': { backgroundPosition: '0% 50%' },
        },
        blob: {
          '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
          '33%': { transform: 'translate(30px, -30px) scale(1.1)' },
          '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
        },
        'scale-in': {
          from: { opacity: '0', transform: 'scale(0.9)' },
          to: { opacity: '1', transform: 'scale(1)' },
        },
        'geser-masuk': {
          to: { opacity: '1', transform: 'translateX(0)' },
        },
        'progres-gulir': {
          from: { transform: 'scaleX(0)' },
          to: { transform: 'scaleX(1)' },
        },
        'fade-in': {
          from: { opacity: '0' },
          to: { opacity: '1' },
        },
        'zoom-in': {
          from: { opacity: '0', transform: 'scale(0.8)' },
          to: { opacity: '1', transform: 'scale(1)' },
        },
        'float-kecil': {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        'slide-in-left': {
          from: { opacity: '0', transform: 'translateX(-50px)' },
          to: { opacity: '1', transform: 'translateX(0)' },
        },
        'slide-in-right': {
          from: { opacity: '0', transform: 'translateX(50px)' },
          to: { opacity: '1', transform: 'translateX(0)' },
        },
        'fade-in-up': {
          from: { opacity: '0', transform: 'translateY(30px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-20px)' },
        },
        'float-gentle': {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(10px)' },
        },
        'scroll-down': {
          '0%': { opacity: '1', transform: 'translateY(0)' },
          '50%': { opacity: '0.3', transform: 'translateY(0.75rem)' },
          '100%': { opacity: '0', transform: 'translateY(0.75rem)' },
        },
      },
      animation: {
        'fade-up': 'fade-in-up 0.8s ease-out',
        blob: 'blob 8s ease-in-out infinite',
        'scale-in': 'scale-in 0.6s ease-out forwards',
        'fade-in-up-lambat': 'fade-in-up 0.8s ease-out forwards',
        'float-cepat': 'float 3s ease-in-out infinite',
        'geser-masuk': 'geser-masuk 0.5s ease-out forwards',
        'progres-gulir': 'progres-gulir linear both',
        'fade-in': 'fade-in 0.3s ease',
        'zoom-in': 'zoom-in 0.3s ease',
        'float-kecil': 'float-kecil 3s ease-in-out infinite',
        'slide-in-left': 'slide-in-left 0.6s ease-out',
        'slide-in-right': 'slide-in-right 0.6s ease-out',
        'fade-in-up': 'fade-in-up 0.6s ease-out',
        float: 'float 6s ease-in-out infinite',
        'float-gentle': 'float-gentle 3s ease-in-out infinite',
        'scroll-down': 'scroll-down 2s ease-in-out infinite',
      },
    },
  },
  plugins: [],
}
