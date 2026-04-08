import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify'

export default defineNuxtConfig({
  ssr: true,

  build: {
    transpile: ['vuetify'],
  },

  modules: [
    (_options, nuxt) => {
      nuxt.hooks.hook('vite:extendConfig', (config) => {
        config.plugins?.push(vuetify({ autoImport: true }))
      })
    },
  ],

  vite: {
    vue: {
      template: {
        transformAssetUrls,
      },
    },
  },

  css: [
    'vuetify/styles',
    '@mdi/font/css/materialdesignicons.css',
    '~/assets/css/main.css'
  ],

  runtimeConfig: {
    dbHost: '',
    dbPort: '',
    dbUser: '',
    dbPassword: '',
    dbName: '',
    photoDbHost: '',
    photoDbPort: '',
    photoDbUser: '',
    photoDbPassword: '',
    photoDbName: '',
    authApiUrl: '',
    galleryApiUrl: '',
    public: {
      s3Endpoint: '',
    }
  },
})