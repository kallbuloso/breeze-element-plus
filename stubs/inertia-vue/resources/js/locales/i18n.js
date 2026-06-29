import { createI18n } from 'vue-i18n'

import { locale, messages } from '@/locales'

export const createAppI18n = () =>
  createI18n({
    legacy: false,
    globalInjection: false,
    locale,
    fallbackLocale: locale,
    messages: {
      [locale]: messages
    }
  })
