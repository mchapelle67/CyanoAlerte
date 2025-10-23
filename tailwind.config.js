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
        'custom-gradient': 'linear-gradient(to right, #2563EB, #16A34A)', // blue-600, green-600 
      },
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}

