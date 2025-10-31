/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./src/**/*.php",
  ],
  safelist: [
    'flash-success',
    'flash-error',
    'text-primary',
    'stroke-primary'
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#13A4AA',
        'secondary': '#1A7F83',
        'light': '#f0f7f4',
      }
    }
  },
  plugins: [],
}
