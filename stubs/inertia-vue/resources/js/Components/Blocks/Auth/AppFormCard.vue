<script setup>
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?raw'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?raw'
</script>

<template>
  <div style="position: relative; width: 100%; min-width: 320px; max-width: 460px; color: var(--el-color-primary)">
    <VNodeRenderer
      :nodes="h('div', { innerHTML: authV1TopShape })"
      style="position: absolute; top: -77px; left: -45px; line-height: 0; pointer-events: none; z-index: 0"
    />

    <VNodeRenderer
      :nodes="h('div', { innerHTML: authV1BottomShape })"
      style="position: absolute; bottom: -58px; right: -58px; line-height: 0; pointer-events: none; z-index: 0"
    />

    <ElCard
      style="position: relative; z-index: 1"
      body-style="padding: 32px"
    >
      <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 28px">
        <Link
          href="/"
          style="display: inline-flex"
        >
          <ApplicationLogo style="width: 40px; height: 40px; fill: var(--el-color-primary)" />
        </Link>
        <h1 style="font-size: 1.375rem; font-weight: 700; margin: 0; color: var(--el-text-color-primary); letter-spacing: 0.25px; text-transform: capitalize">
          {{ $page.props.appName }}
        </h1>
      </div>

      <div
        v-if="$slots.title || $slots.description"
        style="margin-bottom: 16px"
      >
        <h2
          v-if="$slots.title"
          style="font-size: 1.5rem; font-weight: 600; margin: 0 0 4px; color: var(--el-text-color-primary)"
        >
          <slot name="title" />
        </h2>
        <p
          v-if="$slots.description"
          style="font-size: 14px; color: var(--el-text-color-secondary); margin: 0; line-height: 1.6"
        >
          <slot name="description" />
        </p>
      </div>

      <slot />
    </ElCard>
  </div>
</template>
