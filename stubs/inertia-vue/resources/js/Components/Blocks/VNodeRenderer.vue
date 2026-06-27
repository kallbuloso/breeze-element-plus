<script setup>
const props = defineProps({
  nodes: {
    type: [Array, Object],
    required: true
  }
})
</script>

<template>
  <component :is="props.nodes" />
</template>
