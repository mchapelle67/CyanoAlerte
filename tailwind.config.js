/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
      fontFamily: {
      'sans': ['Montserrat', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      'serif': ['Georgia', 'Cambria', 'Times New Roman', 'serif']
      },
    extend: {
      backgroundImage: {
        'custom-gradient': 'linear-gradient(0.25turn, #2c5df1, rgb(52, 150, 108))',
      }
    },
  },
  plugins: [],
}
