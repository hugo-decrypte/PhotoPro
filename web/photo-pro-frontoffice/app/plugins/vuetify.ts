import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        components,
        directives,
        theme: {
            defaultTheme: 'light',
            themes: {
                light: {
                    colors: {
                        primary: '#268D86',
                        secondary: '#135062',
                        accent: '#4FD180',
                        background: '#ffffff',
                        surface: '#f9f9f9',
                    },
                },
            },
        },
    })
    nuxtApp.vueApp.use(vuetify)
})