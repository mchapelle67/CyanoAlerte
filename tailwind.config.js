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
    require('flowbite/plugin'),
    function({ addUtilities }) {
      addUtilities({
        '.btn-slide-effect': { // hover effet, couleur de gauche à droite 
          'position': 'relative',
          'overflow': 'hidden',
          'transition': 'color 0.5s ease',
          '&::before': {
            'content': '""',
            'position': 'absolute',
            'top': '0',
            'left': '-100%',
            'width': '100%',
            'height': '100%',
            'background': '#1D4ED8', // blue-700
            'transition': 'left 0.5s ease-in-out',
            'z-index': '-1'
          },
          '&:hover::before': {
            'left': '0'
          }
        }
      })
    }
  ],
}

