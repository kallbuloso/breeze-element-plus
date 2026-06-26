<script setup>
const { y } = useWindowScroll({ behavior: 'smooth' })

const visible = computed(() => y.value > 200)

const scrollTop = () => {
  y.value = 0
}
</script>

<template>
  <Transition name="scroll-to-top">
    <ElButton
      v-show="visible"
      type="primary"
      circle
      class="app-scroll-to-top"
      @click="scrollTop"
    >
      <AppIcon
        icon="ri:arrow-up-line"
        width="18"
        height="18"
      />
    </ElButton>
  </Transition>
</template>

<style scoped>
.app-scroll-to-top {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 999;
  width: 44px;
  height: 44px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.scroll-to-top-enter-active,
.scroll-to-top-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.scroll-to-top-enter-from,
.scroll-to-top-leave-to {
  opacity: 0;
  transform: scale(0.6);
}
</style>
