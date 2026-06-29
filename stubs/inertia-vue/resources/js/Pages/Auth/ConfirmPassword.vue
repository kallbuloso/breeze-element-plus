<script setup>
const form = useForm({ password: '' })
const { t } = useI18n({ useScope: 'global' })
const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => form.reset('password')
  })
}
</script>

<template layout="AuthLayout">
  <Head :title="t('auth.confirmPassword.pageTitle')" />

  <AppFormCard>
    <template #title>{{ t('auth.confirmPassword.title') }}</template>
    <template #description>{{ t('auth.confirmPassword.description') }}</template>

    <ElForm
      label-position="top"
      @submit.prevent="submit"
    >
      <ElFormItem
        :label="t('common.password')"
        :error="form.errors.password"
      >
        <ElInput
          v-model="form.password"
          type="password"
          autocomplete="current-password"
          autofocus
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
          {{ t('auth.confirmPassword.submit') }}
        </ElButton>
      </ElFormItem>
    </ElForm>
  </AppFormCard>
</template>
