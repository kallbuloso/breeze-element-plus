<script setup>
const layout = useLayoutStore()
const { collapsed, mobileOpen } = storeToRefs(layout)
const page = usePage()
const { t } = useI18n({ useScope: 'global' })
const breakpoints = useBreakpoints({ md: 768 })
const isMobile = breakpoints.smaller('md')
const sidebarWidth = computed(() => (collapsed.value ? '70px' : '240px'))
const pageTitleKeys = {
  Dashboard: 'layout.pageTitles.dashboard',
  'Profile/Edit': 'layout.pageTitles.profile'
}
const pageTitle = computed(() => {
  const translationKey = pageTitleKeys[page.component]

  if (translationKey) return t(translationKey)

  return (
    String(page.component)
      .split('/')
      .pop()
      ?.replace(/([A-Z])/g, ' $1')
      .trim() || t('layout.pageTitles.dashboard')
  )
})

const toggleSidebar = () => {
  if (isMobile.value) layout.toggleMobile()
  else layout.toggleCollapse()
}
</script>

<template>
  <LayoutProvider>
    <div style="display: flex; min-height: 100vh">
      <ElAside
        v-show="!isMobile"
        :style="{
          width: sidebarWidth,
          transition: 'width 0.25s ease',
          background: 'var(--el-bg-color-page)',
          display: 'flex',
          flexDirection: 'column',
          position: 'sticky',
          top: 0,
          height: '100vh',
          flexShrink: 0,
          overflow: 'hidden'
        }"
      >
        <div style="height: 56px; display: flex; align-items: center; gap: 10px; padding: 0 18px; color: var(--el-color-primary)">
          <ApplicationLogo style="width: 30px; height: 30px; fill: currentColor; flex-shrink: 0" />
          <strong v-if="!collapsed" style="color: var(--el-text-color-primary); white-space: nowrap">
            {{ $page.props.appName }}
          </strong>
        </div>
        <AppNavMenu />
      </ElAside>

      <ElDrawer v-model="mobileOpen" direction="ltr" :with-header="false" size="248px" style="--el-drawer-padding-primary: 0">
        <div class="mobile-sidebar">
          <div class="mobile-sidebar-header">
            <ApplicationLogo style="width: 30px; height: 30px; fill: currentColor" />
            <strong style="color: var(--el-text-color-primary)">{{ $page.props.appName }}</strong>
          </div>
          <AppNavMenu force-expanded />
        </div>
      </ElDrawer>

      <ElContainer style="flex: 1; display: flex; flex-direction: column; min-width: 0">
        <ElHeader
          style="
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 20px;
            background: var(--el-bg-color-page);
            position: sticky;
            top: 0;
            z-index: 10;
            min-height: 56px;
          "
        >
          <div style="display: flex; align-items: center; gap: 12px">
            <ElButton circle text :aria-label="t('layout.toggleNavigation')" @click="toggleSidebar">
              <AppIcon :icon="(isMobile ? mobileOpen : !collapsed) ? 'ri:menu-fold-line' : 'ri:menu-unfold-line'" width="20" height="20" />
            </ElButton>
            <h1 style="margin: 0; font-size: 16px; font-weight: 600">{{ pageTitle }}</h1>
          </div>
          <div style="display: flex; align-items: center; gap: 4px">
            <AppUserMenu />
            <AppThemeSwitcher />
          </div>
        </ElHeader>
        <ElMain style="flex: 1; padding: 8px; overflow-x: auto">
          <slot />
        </ElMain>
      </ElContainer>
    </div>
  </LayoutProvider>
</template>

<style scoped>
.mobile-sidebar {
  display: flex;
  height: 100%;
  min-height: 0;
  flex-direction: column;
  overflow: hidden;
}

.mobile-sidebar-header {
  display: flex;
  height: 56px;
  flex-shrink: 0;
  align-items: center;
  gap: 10px;
  padding: 0 18px;
  color: var(--el-color-primary);
}
</style>
