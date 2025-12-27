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
  plugins: [],
}
