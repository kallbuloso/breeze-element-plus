<script setup>
defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String
})
</script>

<template>
  <GuestLayout>
    <Head title="Welcome" />
    <main style="min-height: 100vh; display: grid; place-items: center; padding: 24px">
      <ElCard shadow="never" style="width: min(100%, 720px)">
        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px; color: var(--el-color-primary)">
          <ApplicationLogo style="width: 44px; height: 44px; fill: currentColor" />
          <div>
            <h1 style="margin: 0; font-size: 24px">{{ $page.props.appName }}</h1>
            <p style="margin: 4px 0 0; color: var(--el-text-color-secondary)">Laravel, Inertia, Vue, TypeScript, and Element Plus.</p>
          </div>
        </div>
        <ElSpace wrap>
          <Link v-if="canLogin" :href="route('login')">
            <ElButton type="primary">Log in</ElButton>
          </Link>
          <Link v-if="canRegister" :href="route('register')">
            <ElButton>Register</ElButton>
          </Link>
          <Link v-if="$page.props.auth?.user" :href="route('dashboard')">
            <ElButton type="primary">Dashboard</ElButton>
          </Link>
        </ElSpace>
        <p style="margin: 20px 0 0; color: var(--el-text-color-secondary); font-size: 13px">Laravel {{ laravelVersion }} / PHP {{ phpVersion }}</p>
      </ElCard>
    </main>
  </GuestLayout>
</template>
