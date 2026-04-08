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
    dbHost: process.env.NUXT_DB_HOST,
    dbPort: process.env.NUXT_DB_PORT,
    dbUser: process.env.NUXT_DB_USER,
    dbPassword: process.env.NUXT_DB_PASSWORD,
    dbName: process.env.NUXT_DB_NAME,

    photoDbHost: process.env.NUXT_PHOTO_DB_HOST,
    photoDbPort: process.env.NUXT_PHOTO_DB_PORT,
    photoDbUser: process.env.NUXT_PHOTO_DB_USER,
    photoDbPassword: process.env.NUXT_PHOTO_DB_PASSWORD,
    photoDbName: process.env.NUXT_PHOTO_DB_NAME,

    authApiUrl: process.env.AUTH_API_URL,
    galleryApiUrl: process.env.GALLERY_API_URL,

    public: {
      s3Endpoint: process.env.NUXT_PUBLIC_S3_ENDPOINT,
    }
  },
})