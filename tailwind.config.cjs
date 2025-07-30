/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      './resources/views/**/*.blade.php',
      './resources/js/**/*.js',
    ],
    theme: {
      extend: {},
    },
    plugins: [],
    darkMode: 'class', // Enable dark mode via class strategy
  }