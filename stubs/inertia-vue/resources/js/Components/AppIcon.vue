<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  icon: { type: String, required: true },
  width: { type: [String, Number], default: '1em' },
  height: { type: [String, Number], default: '1em' },
  color: { type: String, default: 'inherit' }
})
</script>

<template>
  <Icon :icon="icon" :width="width" :height="height" :color="color" />
</template>
