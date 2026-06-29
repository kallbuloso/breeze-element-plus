<script setup>
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})
const { t } = useI18n({ useScope: 'global' })

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}
</script>

<template layout="AuthLayout">
  <Head :title="t('auth.register.pageTitle')" />

  <AppFormCard>
    <template #title>{{ t('auth.register.title') }}</template>
    <template #description>{{ t('auth.register.description') }}</template>

    <ElForm
      label-position="top"
      @submit.prevent="submit"
    >
      <ElFormItem
        :label="t('common.name')"
        :error="form.errors.name"
      >
        <ElInput
          v-model="form.name"
          autocomplete="name"
          autofocus
        />
      </ElFormItem>
      <ElFormItem
        :label="t('common.email')"
        :error="form.errors.email"
      >
        <ElInput
          v-model="form.email"
          type="email"
          autocomplete="username"
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
          {{ t('auth.register.submit') }}
        </ElButton>
      </ElFormItem>
    </ElForm>

    <ElDivider />

    <p style="text-align: center; margin: 0; font-size: 14px; color: var(--el-text-color-secondary)">
      {{ t('auth.register.alreadyRegistered') }}
      <Link
        :href="route('login')"
        style="color: var(--el-color-primary)"
      >
        {{ t('auth.register.login') }}
      </Link>
    </p>
  </AppFormCard>
</template>
