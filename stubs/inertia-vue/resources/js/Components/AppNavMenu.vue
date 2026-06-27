<script setup>
import { nav } from '@/Configs/nav'

const props = defineProps({
  forceExpanded: {
    type: Boolean,
    default: false
  }
})

const layout = useLayoutStore()
const page = usePage()
const pendingFeatureKey = ref(null)
const isCollapsed = computed(() => !props.forceExpanded && layout.collapsed)

const featureItems = (item) => item.features ?? item.children ?? []

const hasPermission = (permission) => {
  if (!permission) return true
  return can(permission)
}

const isAllowed = (item) => hasPermission(item.can)

const visibleNav = computed(() =>
  nav
    .map((item) => ({
      ...item,
      features: featureItems(item).filter(isAllowed)
    }))
    .filter((item) => isAllowed(item) && (item.route || item.href || item.features.length > 0))
)

const resolveHref = (item) => {
  if (item.route && typeof route === 'function') return route(item.route)
  return item.href ?? '#'
}

const routeIsCurrent = (routeName) => {
  if (!routeName || typeof route !== 'function') return false

  try {
    return Boolean(page.url) && route().current(routeName)
  } catch {
    return false
  }
}

const itemIsActive = (item) => {
  if (pendingFeatureKey.value && pendingFeatureKey.value === item.key) return true
  if (routeIsCurrent(item.route)) return true
  if (item.href) return page.url === item.href || page.url.startsWith(`${item.href}/`)
  return false
}

const groupIsActive = (group) => itemIsActive(group) || group.features.some(itemIsActive)

const urlActiveGroupKey = computed(() => visibleNav.value.find(groupIsActive)?.key ?? null)
const activeGroup = computed(() => visibleNav.value.find((group) => group.key === layout.activeNavKey) ?? visibleNav.value[0])

watchEffect(() => {
  if (urlActiveGroupKey.value) {
    layout.activeNavKey = urlActiveGroupKey.value
  }
})

const visitItem = (item) => {
  pendingFeatureKey.value = item.key ?? null

  router.visit(resolveHref(item), {
    onFinish: () => {
      pendingFeatureKey.value = null
    }
  })

  layout.closeMobile()
}

const handleGroupClick = (group) => {
  layout.activeNavKey = group.key

  if (group.features.length === 0 && (group.route || group.href)) {
    visitItem(group)
  }
}

const logout = () => {
  router.post(route('logout'))
  layout.closeMobile()
}
</script>

<template>
  <div class="app-nav-menu">
    <nav class="app-nav-rail">
      <ul class="app-nav-list">
        <li v-for="group in visibleNav" :key="group.key" :class="{ active: activeGroup?.key === group.key }">
          <ElPopover
            trigger="hover"
            placement="right-start"
            :width="196"
            :show-arrow="false"
            :show-after="120"
            :hide-after="120"
            :offset="12"
            :disabled="!isCollapsed"
            popper-class="app-nav-flyout"
          >
            <template #reference>
              <button class="app-nav-rail-button" type="button" :aria-label="group.label" @click="handleGroupClick(group)">
                <ElBadge v-if="group.badge" :is-dot="group.badge === true" :value="typeof group.badge === 'number' ? group.badge : undefined">
                  <AppIcon :icon="group.icon" width="20" height="20" />
                </ElBadge>
                <AppIcon v-else :icon="group.icon" width="20" height="20" />
              </button>
            </template>

            <div class="app-nav-flyout-content">
              <p class="app-nav-title">{{ group.label }}</p>
              <button v-for="feature in group.features" :key="feature.key ?? feature.label" class="app-nav-feature" :class="{ active: itemIsActive(feature) }" type="button" @click="visitItem(feature)">
                <AppIcon :icon="feature.icon" width="16" height="16" />
                <span>{{ feature.label }}</span>
              </button>
            </div>
          </ElPopover>
        </li>
      </ul>

      <ul class="app-nav-list">
        <li>
          <ElTooltip content="Sair" placement="right" :show-after="220" :offset="12">
            <button class="app-nav-rail-button" type="button" @click="logout">
              <AppIcon icon="ri:logout-box-r-line" width="20" height="20" />
            </button>
          </ElTooltip>
        </li>
      </ul>
    </nav>

    <Transition name="app-nav-panel">
      <section v-if="!isCollapsed && activeGroup?.features.length" class="app-nav-panel">
        <p class="app-nav-title">{{ activeGroup.label }}</p>
        <button v-for="feature in activeGroup.features" :key="feature.key ?? feature.label" class="app-nav-feature" :class="{ active: itemIsActive(feature) }" type="button" @click="visitItem(feature)">
          <AppIcon :icon="feature.icon" width="16" height="16" />
          <span>{{ feature.label }}</span>
        </button>
      </section>
    </Transition>
  </div>
</template>

<style scoped>
.app-nav-menu {
  display: flex;
  min-height: 0;
  flex: 1;
  width: 100%;
  overflow: hidden;
}

.app-nav-rail {
  display: flex;
  width: 48px;
  flex-shrink: 0;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  align-self: stretch;
  margin: 8px 0 8px 8px;
  padding: 6px 0;
  border-radius: 8px;
  background: var(--el-color-primary);
}

.app-nav-list {
  display: flex;
  width: 100%;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.app-nav-list li {
  position: relative;
  display: flex;
  width: 100%;
  justify-content: center;
}

.app-nav-list li.active::after {
  position: absolute;
  top: 50%;
  right: -6px;
  z-index: 1;
  width: 12px;
  height: 12px;
  border-radius: 2px;
  background: var(--el-bg-color-page);
  content: '';
  transform: translateY(-50%) rotate(45deg);
}

.app-nav-rail-button {
  display: inline-flex;
  width: 38px;
  height: 38px;
  align-items: center;
  justify-content: center;
  padding: 0;
  border: 0;
  border-radius: 7px;
  background: transparent;
  color: rgba(255, 255, 255, 0.72);
  cursor: pointer;
  transition:
    color 0.15s ease,
    background 0.15s ease;
}

.app-nav-rail-button:hover,
.app-nav-list li.active .app-nav-rail-button {
  color: #fff;
}

.app-nav-rail-button:hover {
  background: rgba(255, 255, 255, 0.12);
}

.app-nav-panel {
  width: calc(100% - 56px);
  min-width: 0;
  flex-shrink: 0;
  overflow: hidden;
  padding: 8px 8px 0;
}

.app-nav-title {
  margin: 0;
  padding: 8px 6px 10px;
  color: var(--el-text-color-placeholder);
  font-size: 11px;
  font-weight: 700;
  line-height: 1.2;
  text-transform: uppercase;
}

.app-nav-feature {
  display: flex;
  width: 100%;
  min-height: 34px;
  align-items: center;
  gap: 8px;
  padding: 7px 8px;
  border: 0;
  border-radius: 6px;
  background: transparent;
  color: var(--el-text-color-regular);
  cursor: pointer;
  font: inherit;
  font-size: 13px;
  line-height: 1.25;
  text-align: left;
  transition:
    color 0.15s ease,
    background 0.15s ease;
}

.app-nav-feature span {
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.app-nav-feature:hover {
  background: var(--el-fill-color-light);
  color: var(--el-text-color-primary);
}

.app-nav-feature.active {
  background: var(--el-color-primary);
  color: #fff;
  font-weight: 600;
}

.app-nav-panel-enter-active,
.app-nav-panel-leave-active {
  overflow: hidden;
  transition:
    width 0.2s ease,
    opacity 0.2s ease;
}

.app-nav-panel-enter-from,
.app-nav-panel-leave-to {
  width: 0;
  opacity: 0;
}
</style>

<style>
.app-nav-flyout.el-popover {
  padding: 8px !important;
}

.app-nav-flyout-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
</style>
