/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        bebas: ['"Bebas Neue"', 'sans-serif'],
        inter: ['Inter', 'sans-serif'],
        roboto: ['Roboto', 'sans-serif'],
      },

      colors: {
        brand: "#FFE96E",
        secondary: "#F7CF43",
        primary: "#E09624",
        neutral: "#1A1919",
        warningPrimary: "#AB0000",
        warningSecondary: "#FFE9E9",
      }
    },
  },
  plugins: [],
}

