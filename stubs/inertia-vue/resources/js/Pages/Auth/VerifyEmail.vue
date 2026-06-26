<script setup>
defineProps({ status: String })

const submit = () => router.post(route('verification.send'))
</script>

<template layout="AuthLayout">
  <Head title="Email verification" />
  <h2 style="margin: 0 0 8px; font-size: 22px">Verify your email</h2>
  <p style="margin: 0 0 20px; color: var(--el-text-color-secondary)">Thanks for signing up. Please verify your email address before continuing.</p>
  <ElAlert
    v-if="status === 'verification-link-sent'"
    title="A new verification link has been sent."
    type="success"
    show-icon
    :closable="false"
    style="margin-bottom: 16px"
  />
  <ElSpace wrap>
    <ElButton type="primary" @click="submit">Resend verification email</ElButton>
    <Link :href="route('logout')" method="post" as="button">
      <ElButton>Log out</ElButton>
    </Link>
  </ElSpace>
</template>
