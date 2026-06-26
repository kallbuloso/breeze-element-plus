<script setup>
defineProps({ status: String })

const form = useForm({ email: '' })
const submit = () => form.post(route('password.email'))
</script>

<template layout="AuthLayout">
  <Head title="Forgot password" />
  <h2 style="margin: 0 0 8px; font-size: 22px">Forgot password</h2>
  <p style="margin: 0 0 20px; color: var(--el-text-color-secondary)">Enter your email and we will send a reset link.</p>
  <ElAlert v-if="status" :title="status" type="success" show-icon :closable="false" style="margin-bottom: 16px" />
  <ElForm label-position="top" @submit.prevent="submit">
    <ElFormItem label="Email" :error="form.errors.email">
      <ElInput v-model="form.email" type="email" autocomplete="username" autofocus />
    </ElFormItem>
    <ElButton type="primary" native-type="submit" :loading="form.processing" style="width: 100%">Email password reset link</ElButton>
  </ElForm>
</template>
