import vuePrettierConfig from '@vue/eslint-config-prettier'
import pluginPrettier from 'eslint-plugin-prettier'
import pluginVue from 'eslint-plugin-vue'
import globals from 'globals'
import { readFileSync } from 'node:fs'

const prettierConfig = JSON.parse(readFileSync(new URL('./.prettierrc', import.meta.url), 'utf8'))

export default [
  {
    ignores: ['bootstrap/ssr/**/*', 'node_modules/**/*', 'public/**/*', 'vendor/**/*']
  },
  ...pluginVue.configs['flat/recommended'],
  {
    plugins: {
      prettier: pluginPrettier
    },
    languageOptions: {
      ecmaVersion: 'latest',
      globals: {
        ...globals.browser,
        ...globals.node,
        ...globals.es2021
      }
    },
    rules: {
      'prettier/prettier': ['error', prettierConfig],
      'vue/block-lang': 'off',
      'vue/component-name-in-template-casing': ['error', 'PascalCase'],
      'vue/multi-word-component-names': 'off',
      'vue/no-mutating-props': ['error', { shallowOnly: true }],
      'vue/require-default-prop': 'off',
      'no-undef': 'off',
      indent: ['error', 2]
    }
  },
  vuePrettierConfig
]
