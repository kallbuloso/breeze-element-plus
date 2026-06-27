import { useStorage } from '@vueuse/core'

export const useLayoutStore = defineStore('layout', () => {
  const collapsed = useStorage('breeze-element-plus-sidebar-collapsed', false)
  const activeNavKey = useStorage('breeze-element-plus-active-nav-key', 'dashboard')
  const mobileOpen = ref(false)

  const toggleCollapse = () => {
    collapsed.value = !collapsed.value
  }

  const toggleMobile = () => {
    mobileOpen.value = !mobileOpen.value
  }

  const closeMobile = () => {
    mobileOpen.value = false
  }

  return { activeNavKey, collapsed, mobileOpen, toggleCollapse, toggleMobile, closeMobile }
})
