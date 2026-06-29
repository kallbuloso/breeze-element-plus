import { createSharedComposable } from '@vueuse/core'

const modes = [
  { value: 'auto', labelKey: 'theme.system', icon: 'ri:computer-line' },
  { value: 'light', labelKey: 'theme.light', icon: 'ri:sun-line' },
  { value: 'dark', labelKey: 'theme.dark', icon: 'ri:moon-line' }
]

function buildTheme() {
  const mode = useColorMode({
    storageKey: 'app-color-mode',
    emitAuto: true,
    selector: 'html',
    attribute: 'class',
    modes: { auto: '', light: '', dark: 'dark' }
  })

  const preferredDark = usePreferredDark()
  const isDark = computed(() => (mode.value === 'auto' ? preferredDark.value : mode.value === 'dark'))
  const currentMode = computed(() => modes.find((item) => item.value === mode.value) ?? modes[0])
  const setMode = (value) => {
    mode.value = value
  }

  return { mode, isDark, modes, currentMode, setMode }
}

export const useTheme = createSharedComposable(buildTheme)
