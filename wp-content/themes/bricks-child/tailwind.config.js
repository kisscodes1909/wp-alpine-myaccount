const _ = require("lodash");
const tailpress = require("@jeffreyvr/tailwindcss-tailpress");
const colors = require('tailwindcss/colors')

module.exports = {
  mode: 'jit',
  corePlugins: {
    // Disable preflight to avoid duplicate/conflict with Bricks theme (Bricks has its own reset)
    preflight: false,
  },
  content: [
    './*.php',
    './template-parts/**/*.php',
    './woocommerce/**/*.php',
    './includes/**/*.php',
    './elements/**/*.php',
    './*/*.php',
  ],
  theme: {
    fontSize: {
      'xs': '12px',
      'sm': '14px',
      'base': '16px',
      'lg': '18px',
      'xl': '20px',
      '2xl': '24px',
      '3xl': '30px',
      '4xl': '36px',
      '5xl': '48px',
      '6xl': '64px',
    },
    screens: {
      'xs': '320px',
      '2xs': '480px',
      'sm': '768px',
      'md': '992px',
      'lg': '1280px',
      'xl': '1440px',
    },
    extend: {
      colors: {
        goldBase: '#CAA15F',
        goldLight: '#E8CBA3',
        goldDark: '#8A6A2F',
        yellow: {
          500: '#CAA15F', // Extended yellow color
        },
        charcoal: '#4d4d4d',
      },
      backgroundImage: {
        'gold-gradient': 'linear-gradient(45deg, #E8CBA3, #CAA15F, #8A6A2F)',
      },
      lineHeight: {
        '28px': '28px',
        'base': '21px'
      },
    },
  },
  variants: {},
  plugins: [
    tailpress.tailwind,
    require('@tailwindcss/forms'),
  ],
}
