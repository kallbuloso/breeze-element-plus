<template>
  <GuestLayout>
    <main style="min-height: 100vh; display: grid; place-items: center; padding: 24px">
      <section style="width: min(100%, 440px)">
        <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 20px; color: var(--el-color-primary)">
          <Link href="/" style="display: inline-flex; color: inherit">
            <ApplicationLogo style="width: 40px; height: 40px; fill: currentColor" />
          </Link>
          <strong style="font-size: 18px; color: var(--el-text-color-primary)">{{ $page.props.appName }}</strong>
        </div>
        <ElCard shadow="never">
          <slot />
        </ElCard>
      </section>
    </main>
  </GuestLayout>
</template>
