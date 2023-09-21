import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
const { rename } = require('fs/promises');
// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    {
      name: 'postbuild-commands', // the name of your custom plugin. Could be anything.
      closeBundle: async () => {
          await rename('./dist/index.html','../../templates/page-kpm-counter-index.php');
          await rename('./dist/wp-content/plugins/kpm-counter/public/dist/','../../public/dist');
      }
    },
    vue()],
  // base: '/wp-content/plugins/kpm-counter/js-src/counter-app/dist/',
  base: '/',
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  build: {
    // outDir: '../../../../../',
    // outDir: '../../templates/',
    assetsDir: 'wp-content/plugins/kpm-counter/public/dist/',
    // rollupOptions: {
    //   input: [
    //     './page-kpm-counter-index.php',
    //     // 'src/main.js',
    //     // 'src/style.scss',
    //     // 'src/assets/*'
    //   ],
    //   // output: {
    //   //   // chunkFileNames: 'js/[name].js',
    //   //   // entryFileNames: 'js/[name].js',
    //   //   dir: '../../../../../',
    //   //   // assetFileNames: '../../public/dist/[name]-[hash][extname]',
    //   // },
    // },
  }
})
