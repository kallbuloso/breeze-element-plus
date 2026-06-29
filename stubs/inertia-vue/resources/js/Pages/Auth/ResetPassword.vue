<script setup>
const props = defineProps({
  email: String,
  token: String
})

const { t } = useI18n({ useScope: 'global' })
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
  <Head :title="t('auth.resetPassword.pageTitle')" />

  <AppFormCard>
    <template #title>{{ t('auth.resetPassword.title') }}</template>
    <template #description>{{ t('auth.resetPassword.description') }}</template>

    <ElForm
      label-position="top"
      @submit.prevent="submit"
    >
      <ElFormItem
        :label="t('common.email')"
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
        :label="t('common.password')"
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
        :label="t('common.confirmPassword')"
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
          {{ t('auth.resetPassword.submit') }}
        </ElButton>
      </ElFormItem>
    </ElForm>
  </AppFormCard>
</template>
