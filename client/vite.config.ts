import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { VueRouterAutoImports } from 'unplugin-vue-router'
import VueRouter from 'unplugin-vue-router/vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    VueRouter(),
    Components(),
    AutoImport({
      include: [
        /\.[tj]sx?$/, // .ts, .tsx, .js, .jsx
        /\.vue$/,
        /\.vue\?vue/, // .vue
        /\.vue\.[tj]sx?\?vue/, // .vue (vue-loader with experimentalInlineMatchResource enabled)
        /\.md$/, // .md
      ],
      // Automatically scan these directories for composables and stores
      dirs: ['src/stores', 'src/composables', 'src/types', 'src/utils'],
      imports: [
        // presets
        'vue',
        VueRouterAutoImports,
        {
          pinia: ['createPinia', 'defineStore', 'storeToRefs', 'acceptHMRUpdate'],
        },
        {
          axios: [
            // default imports
            ['default', 'axios'], // import { default as axios } from 'axios',
          ],
        },

        {
          from: '@formkit/core',
          imports: ['FormKitNode'],
          type: true,
        },
        // {
        //   from: '@/utils',
        //   imports: ['handleInvalidForm', 'setError', 'serialNo'],
        // },
        // Ensure useAuthStore is auto-imported even if directory scan misses it

        // Auto-import axios instance `http`
        {
          from: '@/services/http',
          imports: [['default', 'http']],
        },
      ],
      dts: true,
      viteOptimizeDeps: true,
    }),
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
})
