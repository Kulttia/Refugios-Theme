/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './woocommerce/**/*.php',
    './assets/js/**/*.js',
  ],
  corePlugins: {
    preflight: false,
  },
  theme: {
    extend: {
      colors: {
        cream: '#f5e9e2',
        brown: '#4e342e',
        amber: '#d9a066',
        teal: '#02535a',
      },
      fontFamily: {
        serif: ['Playfair Display', 'Georgia', 'serif'],
        sans: ['Montserrat', 'Arial', 'sans-serif'],
        body: ['Lato', 'sans-serif'],
      },
    },
  },
  plugins: [],
};
