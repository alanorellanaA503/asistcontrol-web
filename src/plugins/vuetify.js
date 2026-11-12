import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

// Preferencia guardada por el usuario; si no existe, se usa la del sistema
const savedTheme = localStorage.getItem('theme')
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
const initialTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light')

export default createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: initialTheme,
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#ff4b23',
          secondary: '#d9773a',
          background: '#fcf7f1',
          surface: '#ffffff',
          'on-surface': '#111111',
          muted: '#5b5b5b',
          footer: '#161616',
        },
      },
      dark: {
        dark: true,
        colors: {
          primary: '#ff6b47',
          secondary: '#e8924f',
          background: '#140f0d',
          surface: '#221a17',
          'on-surface': '#f5ece6',
          muted: '#b7a9a1',
          footer: '#0b0808',
        },
      },
    },
  },
})
