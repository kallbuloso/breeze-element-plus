<script setup>
const page = usePage()
const { flush } = useToast()

onMounted(() => nextTick(() => flush(page.props.flash?.toasts)))
watch(
  () => page.props.flash?.toasts,
  (toasts) => flush(toasts)
)
</script>

<template>
  <ElConfigProvider :locale="useLocale()">
    <div style="min-height: 100vh; background: var(--el-bg-color-page)">
      <slot />
      <AppScrollToTop />
    </div>
  </ElConfigProvider>
</template>
