<script setup>
import { nav } from '@/Configs/nav'

const page = usePage()

const itemHref = (item) => (typeof route === 'function' && item.route ? route(item.route) : '#')
const active = (item) => (typeof route === 'function' && item.route ? route().current(item.route) : page.url === itemHref(item))
</script>

<template>
  <ElMenu :default-active="nav.find(active)?.key" style="border-right: 0; width: 100%">
    <ElMenuItem v-for="item in nav" :key="item.key" :index="item.key">
      <Link :href="itemHref(item)" style="display: inline-flex; align-items: center; gap: 10px; width: 100%; text-decoration: none">
        <AppIcon :icon="item.icon" width="18" height="18" />
        <span>{{ item.label }}</span>
      </Link>
    </ElMenuItem>
  </ElMenu>
</template>
