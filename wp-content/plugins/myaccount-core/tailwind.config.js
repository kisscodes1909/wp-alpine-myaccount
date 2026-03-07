const tailpress = require("@jeffreyvr/tailwindcss-tailpress");
const colors = require('tailwindcss/colors');

module.exports = {
  mode: 'jit',
  corePlugins: {
    preflight: false,
  },
  content: [
    './*.php',
    './templates/**/*.php',
    './includes/**/*.php',
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
        ...colors,
        goldBase: '#CAA15F',
        goldLight: '#E8CBA3',
        goldDark: '#8A6A2F',
        yellow: {
          500: '#CAA15F',
        },
        charcoal: '#4d4d4d',
        /* Form colors aligned with base.css --ma-* variables */
        form: {
          text: '#0a0a0a',        /* --ma-text-primary */
          muted: '#6a7282',        /* --ma-text-muted-dark */
          border: '#e5e7eb',       /* --ma-border */
          borderStrong: '#c7cdd4',
          focus: '#99a1af',       /* --ma-text-muted */
          surface: '#f9fafb',      /* --ma-surface-alt */
          field: '#f9fafb',        /* --ma-surface-alt */
          accent: '#111827',       /* --ma-btn-primary-bg */
          danger: '#ec003f',       /* --ma-danger-text */
          ring: 'rgba(0, 0, 0, 0.1)', /* --ma-border-divider */
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
};
