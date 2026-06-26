<script setup>
const form = useForm({ password: '' })
const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => form.reset('password')
  })
}
</script>

<template layout="AuthLayout">
  <Head title="Confirm password" />
  <h2 style="margin: 0 0 8px; font-size: 22px">Confirm password</h2>
  <p style="margin: 0 0 20px; color: var(--el-text-color-secondary)">This is a secure area. Please confirm your password.</p>
  <ElForm label-position="top" @submit.prevent="submit">
    <ElFormItem label="Password" :error="form.errors.password">
      <ElInput v-model="form.password" type="password" autocomplete="current-password" autofocus show-password />
    </ElFormItem>
    <ElButton type="primary" native-type="submit" :loading="form.processing" style="width: 100%">Confirm</ElButton>
  </ElForm>
</template>
