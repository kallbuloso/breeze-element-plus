<script setup>
defineProps({ status: String })

const submit = () => router.post(route('verification.send'))
</script>

<template layout="AuthLayout">
  <Head title="Email verification" />

  <AppFormCard>
    <template #title>Verify your email</template>
    <template #description> Thanks for signing up. Please verify your email address before continuing. If you did not receive the email, we can send another one. </template>

    <ElAlert
      v-if="status === 'verification-link-sent'"
      title="A new verification link has been sent."
      type="success"
      show-icon
      :closable="false"
      style="margin-bottom: 20px"
    />

    <ElForm @submit.prevent="submit">
      <ElFormItem>
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 16px">
          <ElButton
            type="primary"
            native-type="submit"
          >
            Resend verification email
          </ElButton>

          <Link
            :href="route('logout')"
            method="post"
            as="button"
            style="font-size: 14px; color: var(--el-text-color-secondary); background: none; border: none; cursor: pointer"
          >
            Log out
          </Link>
        </div>
      </ElFormItem>
    </ElForm>
  </AppFormCard>
</template>
