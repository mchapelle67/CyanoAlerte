/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    fontFamily: {
      'sans': ['Montserrat', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      'serif': ['Georgia', 'Cambria', 'Times New Roman', 'serif'],
      'mono': ['JetBrains Mono', 'Fira Code', 'Consolas', 'monospace'],
    },
    extend: {
      fontFamily: {
        'primary': 'var(--font-primary)',
        'secondary': 'var(--font-secondary)', 
        'montserrat': ['Montserrat', 'sans-serif'],
        'roboto': ['Roboto', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
