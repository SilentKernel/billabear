/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.{vue,js,ts,jsx,tsx}",
    './node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}',
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {
      keyframes: {
        shake: {
          '0%, 50%, 100%': { transform: 'translate(-3px, 0px)' },
          '25%, 75%': { transform: 'translate(3px, 0px)' },
        }
      },
      animation: {
        shake: 'shake 0.5s',
      },
      height: {
        '630px': '630px'
      },
      padding: {
        '150px': '150px'
      },

      colors: {
        primary: { "50": "#f0f2f5", "100": "#e1e5eb", "200": "#c3cbd8", "300": "#a5b1c4", "400": "#8797b1", "500": "#1E2E50", "600": "#1a2845", "700": "#16223b", "800": "#121c30", "900": "#0e1626" }
      },
      fontFamily: {
        'sans': ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'system-ui', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
        'body': ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'system-ui', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
        'mono': ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace']
      },
      transitionProperty: {
        'width': 'width'
      },
      textDecoration: ['active'],
      minWidth: {
        'kanban': '28rem'
      },
    },
  },
  plugins: [require('flowbite/plugin')]
}
