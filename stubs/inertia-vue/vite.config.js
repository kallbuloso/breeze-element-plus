import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import AutoImport from 'unplugin-auto-import/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
import Components from 'unplugin-vue-components/vite'
import { defineConfig } from 'vite'

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.js',
      ssr: 'resources/js/ssr.js',
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    AutoImport({
      resolvers: [ElementPlusResolver()],
      imports: [
        'vue',
        'pinia',
        '@vueuse/core',
        {
          '@inertiajs/vue3': ['router', 'useForm', 'usePage']
        }
      ],
      dirs: ['./resources/js/composables', './resources/js/utils', './resources/js/Stores'],
      vueTemplate: true,
      ignore: ['useCookies', 'useStorage']
    }),
    Components({
      dirs: ['./resources/js/Components/**/**', './resources/js/Layouts/**/**'],
      extensions: ['vue'],
      directoryAsNamespace: true,
      deep: true,
      resolvers: [
        ElementPlusResolver(),
        (name) => {
          if (name === 'Head') {
            return { importName: 'Head', path: '@inertiajs/vue3' }
          }

          if (name === 'Link') {
            return { importName: 'Link', path: '@inertiajs/vue3' }
          }
        }
      ]
    }),
    {
      name: 'vite:inertia:layout',
      enforce: 'pre',
      transform: (code, id) => {
        if (!id.endsWith('.vue')) return code

        const layoutRegex = /<template +layout(?: *= *['"](?:(?:([\w|,]+):)?([\w|,]+))['"] *)?>/

        if (!layoutRegex.test(code)) return code

        return code.replace(layoutRegex, (_, __, layoutNames) => {
          const layoutImports = layoutNames.split(',').map((layoutName) => `import ${layoutName} from '@/Layouts/${layoutName}.vue'`)

          return `
                <script>
                ${layoutImports.join('\n')}
                export default {
                  layout: [${layoutNames}]
                }
                </script>
                <template>
              `
        })
      }
    }
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
      '@images': path.resolve(__dirname, 'resources/images')
    }
  }
})
