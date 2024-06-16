/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    fontSize: {
      sm: ['14px', '20px'],
      'xl': '1.25rem',
      '3xl': ['30px', '36px'],
      '6xl': '3.75rem',
    },
    colors: {
      'white': '#ffffff',
    },
    extend: {
      colors: {
        indigo: {
          200: '#a3b1fa',
          400: '#5e6fd3',
        },
      }
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
