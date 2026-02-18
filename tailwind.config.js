/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js" 
  ],
  theme: {
      fontFamily: {
      'sans': ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      'serif': ['Georgia', 'Cambria', 'Times New Roman', 'serif'],
      'display': ['Montserrat', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif']
      },
    extend: {
      backgroundImage: {
        'custom-gradient': 'linear-gradient(to right, #1D4ED8, #16A34A)', // blue-700, green-600 
      },
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}

