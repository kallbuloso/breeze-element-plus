<script setup>
defineProps({
  canResetPassword: Boolean,
  status: String
})

const form = useForm({
  email: '',
  password: '',
  remember: false
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password')
  })
}
</script>

<template layout="AuthLayout">
  <Head title="Log in" />

  <AppFormCard>
    <template #title>Log in</template>
    <template #description>Use your credentials to continue.</template>

    <ElAlert
      v-if="status"
      :title="status"
      type="success"
      show-icon
      :closable="false"
      style="margin-bottom: 16px"
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
      <ElFormItem :error="form.errors.password">
        <template #label>
          <span style="display: flex; justify-content: space-between; width: 100%">
            Password
            <Link
              v-if="canResetPassword"
              :href="route('password.request')"
              style="color: var(--el-color-primary); text-decoration: none"
            >
              Forgot?
            </Link>
          </span>
        </template>
        <ElInput
          v-model="form.password"
          type="password"
          autocomplete="current-password"
          show-password
        />
      </ElFormItem>
      <ElFormItem>
        <ElCheckbox v-model="form.remember">Remember me</ElCheckbox>
      </ElFormItem>
      <ElFormItem>
        <ElButton
          type="primary"
          native-type="submit"
          :loading="form.processing"
          style="width: 100%"
        >
          Log in
        </ElButton>
      </ElFormItem>
    </ElForm>

    <p style="text-align: center; margin: 8px 0 0; font-size: 14px">
      <Link
        :href="route('register')"
        style="color: var(--el-color-primary); text-decoration: none"
      >
        Create an account
      </Link>
    </p>
  </AppFormCard>
</template>
