import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { setupHttpInterceptors } from './lib/http'
import router from './router'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
setupHttpInterceptors()
app.use(router)

app.mount('#app')
