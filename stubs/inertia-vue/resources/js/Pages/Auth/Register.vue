<script setup>
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}
</script>

<template layout="AuthLayout">
  <Head title="Register" />
  <h2 style="margin: 0 0 20px; font-size: 22px">Create account</h2>
  <ElForm label-position="top" @submit.prevent="submit">
    <ElFormItem label="Name" :error="form.errors.name">
      <ElInput v-model="form.name" autocomplete="name" autofocus />
    </ElFormItem>
    <ElFormItem label="Email" :error="form.errors.email">
      <ElInput v-model="form.email" type="email" autocomplete="username" />
    </ElFormItem>
    <ElFormItem label="Password" :error="form.errors.password">
      <ElInput v-model="form.password" type="password" autocomplete="new-password" show-password />
    </ElFormItem>
    <ElFormItem label="Confirm password" :error="form.errors.password_confirmation">
      <ElInput v-model="form.password_confirmation" type="password" autocomplete="new-password" show-password />
    </ElFormItem>
    <ElButton type="primary" native-type="submit" :loading="form.processing" style="width: 100%">Register</ElButton>
  </ElForm>
  <p style="text-align: center; margin: 18px 0 0">
    <Link :href="route('login')" style="color: var(--el-color-primary); text-decoration: none">Already registered?</Link>
  </p>
</template>
