<script setup>
const props = defineProps({
  email: String,
  token: String
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}
</script>

<template layout="AuthLayout">
  <Head title="Reset password" />
  <h2 style="margin: 0 0 20px; font-size: 22px">Reset password</h2>
  <ElForm label-position="top" @submit.prevent="submit">
    <ElFormItem label="Email" :error="form.errors.email">
      <ElInput v-model="form.email" type="email" autocomplete="username" autofocus />
    </ElFormItem>
    <ElFormItem label="Password" :error="form.errors.password">
      <ElInput v-model="form.password" type="password" autocomplete="new-password" show-password />
    </ElFormItem>
    <ElFormItem label="Confirm password" :error="form.errors.password_confirmation">
      <ElInput v-model="form.password_confirmation" type="password" autocomplete="new-password" show-password />
    </ElFormItem>
    <ElButton type="primary" native-type="submit" :loading="form.processing" style="width: 100%">Reset password</ElButton>
  </ElForm>
</template>
