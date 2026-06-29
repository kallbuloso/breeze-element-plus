<script setup>
const theme = useTheme()
const { t } = useI18n({ useScope: 'global' })
</script>

<template>
  <ElDropdown trigger="click" @command="theme.setMode">
    <ElButton circle text :aria-label="t('theme.change')">
      <AppIcon :icon="theme.currentMode.value.icon" width="18" height="18" />
    </ElButton>
    <template #dropdown>
      <ElDropdownMenu>
        <ElDropdownItem v-for="mode in theme.modes" :key="mode.value" :command="mode.value">
          <span style="display: inline-flex; align-items: center; gap: 8px">
            <AppIcon :icon="mode.icon" width="16" height="16" />
            {{ t(mode.labelKey) }}
          </span>
        </ElDropdownItem>
      </ElDropdownMenu>
    </template>
  </ElDropdown>
</template>
