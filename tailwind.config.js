/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{html,js}"],
  theme: {
    extend: {
      colors: {
        text_primary: "#1300EF",
        text_secondary: "#D9211B",
        bg_secondary: "#D9211B",
        bg_primary: "#1300EF",
        bg_hover: "#3C2EE1",
      },
    },
  },
  plugins: [],
};
