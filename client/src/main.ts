import { createApp } from 'vue'
import { plugin, defaultConfig } from '@formkit/vue'
import config from '../formkit.config'
import { VueQueryPlugin } from '@tanstack/vue-query'
import Vue3Toastify, { type ToastContainerOptions } from 'vue3-toastify'
import 'vue-skeletor/dist/vue-skeletor.css'
import 'vue3-toastify/dist/index.css'
import 'dropzone/dist/dropzone.css'

import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(plugin, defaultConfig(config))
app.use(createPinia())
app.use(VueQueryPlugin)
app.use(Vue3Toastify, {
  clearOnUrlChange: false,
  autoClose: 3000,
  // ...
} as ToastContainerOptions)
app.use(router)

app.mount('#app')
