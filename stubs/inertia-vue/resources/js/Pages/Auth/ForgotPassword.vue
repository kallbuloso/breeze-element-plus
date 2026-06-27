<script setup>
defineProps({ status: String })

const form = useForm({ email: '' })
const submit = () => form.post(route('password.email'))
</script>

<template layout="AuthLayout">
  <Head title="Forgot password" />

  <AppFormCard>
    <template #title>Forgot password?</template>
    <template #description>Enter your email and we will send you a password reset link.</template>

    <ElAlert
      v-if="status"
      :title="status"
      type="success"
      show-icon
      :closable="false"
      style="margin-bottom: 20px"
    />

    <ElForm
      label-position="top"
      @submit.prevent="submit"
    >
      <ElFormItem
        label="Email"
        :error="form.errors.email"
      >
        <ElInput
          v-model="form.email"
          type="email"
          autocomplete="username"
          autofocus
        />
      </ElFormItem>
      <ElFormItem>
        <ElButton
          type="primary"
          native-type="submit"
          :loading="form.processing"
          style="width: 100%"
        >
          Email password reset link
        </ElButton>
      </ElFormItem>
    </ElForm>

    <ElDivider />

    <p style="text-align: center; margin: 0; font-size: 14px; color: var(--el-text-color-secondary)">
      Remembered your password?
      <Link
        :href="route('login')"
        style="color: var(--el-color-primary)"
      >
        Log in
      </Link>
    </p>
  </AppFormCard>
</template>
