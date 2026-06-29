<script setup>
defineProps({ status: String })

const { t } = useI18n({ useScope: 'global' })
const form = useForm({ email: '' })
const submit = () => form.post(route('password.email'))
</script>

<template layout="AuthLayout">
  <Head :title="t('auth.forgotPassword.pageTitle')" />

  <AppFormCard>
    <template #title>{{ t('auth.forgotPassword.title') }}</template>
    <template #description>{{ t('auth.forgotPassword.description') }}</template>

    <ElAlert
      v-if="status"
      :title="status"
      type="success"
      show-icon
      :closable="false"
      style="margin-bottom: 20px"
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
      <ElFormItem>
        <ElButton
          type="primary"
          native-type="submit"
          :loading="form.processing"
          style="width: 100%"
        >
          {{ t('auth.forgotPassword.submit') }}
        </ElButton>
      </ElFormItem>
    </ElForm>

    <ElDivider />

    <p style="text-align: center; margin: 0; font-size: 14px; color: var(--el-text-color-secondary)">
      {{ t('auth.forgotPassword.remembered') }}
      <Link
        :href="route('login')"
        style="color: var(--el-color-primary)"
      >
        {{ t('auth.forgotPassword.login') }}
      </Link>
    </p>
  </AppFormCard>
</template>
