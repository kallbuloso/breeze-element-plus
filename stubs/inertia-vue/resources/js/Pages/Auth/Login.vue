<script setup>
defineProps({
  canResetPassword: Boolean,
  status: String
})

const { t } = useI18n({ useScope: 'global' })
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
  <Head :title="t('auth.login.pageTitle')" />

  <AppFormCard>
    <template #title>{{ t('auth.login.title') }}</template>
    <template #description>{{ t('auth.login.description') }}</template>

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
      <ElFormItem :error="form.errors.password">
        <div style="display: flex; align-items: baseline; justify-content: space-between; width: 100%; margin-bottom: 6px; line-height: 1.4">
          <label style="font-size: var(--el-form-label-font-size); font-weight: 500; color: var(--el-text-color-regular)">{{ t('common.password') }}</label>
          <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            style="font-size: 12px; color: var(--el-color-primary)"
          >
            {{ t('auth.login.forgot') }}
          </Link>
        </div>
        <ElInput
          v-model="form.password"
          type="password"
          autocomplete="current-password"
          show-password
        />
      </ElFormItem>
      <ElFormItem>
        <ElCheckbox v-model="form.remember">{{ t('auth.login.remember') }}</ElCheckbox>
      </ElFormItem>
      <ElFormItem>
        <ElButton
          type="primary"
          native-type="submit"
          :loading="form.processing"
          style="width: 100%"
        >
          {{ t('auth.login.submit') }}
        </ElButton>
      </ElFormItem>
    </ElForm>

    <p style="text-align: center; margin: 8px 0 0; font-size: 14px">
      <Link
        :href="route('register')"
        style="color: var(--el-color-primary); text-decoration: none"
      >
        {{ t('auth.login.createAccount') }}
      </Link>
    </p>
  </AppFormCard>
</template>
