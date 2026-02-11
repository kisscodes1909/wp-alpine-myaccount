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
      fontSize: {
        'form-label': ['13px', { lineHeight: '1.1' }],
        'form-input': ['18px', { lineHeight: '1.2' }],
        'form-check': ['18px', { lineHeight: '1.25' }],
      },
      minHeight: {
        'form-field': '50px',
      },
      height: {
        'form-field': '50px',
      },
      colors: {
        goldBase: '#CAA15F',
        goldLight: '#E8CBA3',
        goldDark: '#8A6A2F',
        yellow: {
          500: '#CAA15F', // Extended yellow color
        },
        charcoal: '#4d4d4d',
        form: {
          text: '#111827',
          muted: '#6b7280',
          border: '#d1d5db',
          borderStrong: '#c7cdd4',
          focus: '#9ca3af',
          surface: '#f3f4f6',
          field: '#f8fafc',
          accent: '#111827',
          danger: '#dc2626',
          ring: 'rgb(17 24 39 / 0.08)',
        },
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
