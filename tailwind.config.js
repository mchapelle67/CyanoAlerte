/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js" 
  ],
  theme: {
      fontFamily: {
      'sans': ['Montserrat', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      'serif': ['Georgia', 'Cambria', 'Times New Roman', 'serif']
      },
    extend: {
      backgroundImage: {
        'custom-gradient': 'linear-gradient(to right, #2E6CF6, #4FAD80)',
      },
      colors: {
        'custom-primary-blue': '#0B4CFA',
        'custom-primary-green': '#2C6536'
      }
    },
  },
  plugins: [
    require('flowbite/plugin'),
    function({ addUtilities }) {
      addUtilities({
        '.btn-slide-effect': {
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
            'background': '#0B4CFA',
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

