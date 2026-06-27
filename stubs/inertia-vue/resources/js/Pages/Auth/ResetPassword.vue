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

  <AppFormCard>
    <template #title>Reset password</template>
    <template #description>Choose a new password for your account.</template>

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
          Reset password
        </ElButton>
      </ElFormItem>
    </ElForm>
  </AppFormCard>
</template>
