<script setup>
defineProps({ status: String })

const { t } = useI18n({ useScope: 'global' })
const submit = () => router.post(route('verification.send'))
</script>

<template layout="AuthLayout">
  <Head :title="t('auth.verifyEmail.pageTitle')" />

  <AppFormCard>
    <template #title>{{ t('auth.verifyEmail.title') }}</template>
    <template #description>{{ t('auth.verifyEmail.description') }}</template>

    <ElAlert
      v-if="status === 'verification-link-sent'"
      :title="t('auth.verifyEmail.sent')"
      type="success"
      show-icon
      :closable="false"
      style="margin-bottom: 20px"
    />

    <ElForm @submit.prevent="submit">
      <ElFormItem>
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 16px">
          <ElButton
            type="primary"
            native-type="submit"
          >
            {{ t('auth.verifyEmail.resend') }}
          </ElButton>

          <Link
            :href="route('logout')"
            method="post"
            as="button"
            style="font-size: 14px; color: var(--el-text-color-secondary); background: none; border: none; cursor: pointer"
          >
            {{ t('auth.verifyEmail.logout') }}
          </Link>
        </div>
      </ElFormItem>
    </ElForm>
  </AppFormCard>
</template>
