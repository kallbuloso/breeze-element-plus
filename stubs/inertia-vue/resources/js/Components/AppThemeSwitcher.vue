<script setup>
const theme = useTheme()
</script>

<template>
  <ElDropdown trigger="click" @command="theme.setMode">
    <ElButton circle text>
      <AppIcon :icon="theme.currentMode.value.icon" width="18" height="18" />
    </ElButton>
    <template #dropdown>
      <ElDropdownMenu>
        <ElDropdownItem v-for="mode in theme.modes" :key="mode.value" :command="mode.value">
          <span style="display: inline-flex; align-items: center; gap: 8px">
            <AppIcon :icon="mode.icon" width="16" height="16" />
            {{ mode.label }}
          </span>
        </ElDropdownItem>
      </ElDropdownMenu>
    </template>
  </ElDropdown>
</template>
