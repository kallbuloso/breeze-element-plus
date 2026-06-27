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

  <AppFormCard>
    <template #title>Create account</template>
    <template #description>Fill in the information below to get started.</template>

    <ElForm
      label-position="top"
      @submit.prevent="submit"
    >
      <ElFormItem
        label="Name"
        :error="form.errors.name"
      >
        <ElInput
          v-model="form.name"
          autocomplete="name"
          autofocus
        />
      </ElFormItem>
      <ElFormItem
        label="Email"
        :error="form.errors.email"
      >
        <ElInput
          v-model="form.email"
          type="email"
          autocomplete="username"
        />
      </ElFormItem>
      <ElFormItem
        label="Password"
        :error="form.errors.password"
      >
        <ElInput
          v-model="form.password"
          type="password"
          autocomplete="new-password"
          show-password
        />
      </ElFormItem>
      <ElFormItem
        label="Confirm password"
        :error="form.errors.password_confirmation"
      >
        <ElInput
          v-model="form.password_confirmation"
          type="password"
          autocomplete="new-password"
          show-password
        />
      </ElFormItem>
      <ElFormItem>
        <ElButton
          type="primary"
          native-type="submit"
          :loading="form.processing"
          style="width: 100%"
        >
          Register
        </ElButton>
      </ElFormItem>
    </ElForm>

    <ElDivider />

    <p style="text-align: center; margin: 0; font-size: 14px; color: var(--el-text-color-secondary)">
      Already registered?
      <Link
        :href="route('login')"
        style="color: var(--el-color-primary)"
      >
        Log in
      </Link>
    </p>
  </AppFormCard>
</template>
