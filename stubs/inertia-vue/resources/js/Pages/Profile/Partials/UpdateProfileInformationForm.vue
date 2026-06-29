<script setup>
defineProps({
  mustVerifyEmail: Boolean,
  status: String
})

const { t } = useI18n({ useScope: 'global' })
const user = usePage().props.auth.user
const form = useForm({
  name: user.name,
  email: user.email
})

const submit = () => form.patch(route('profile.update'))
</script>

<template>
  <ElCard shadow="never">
    <template #header>{{ t('profile.information.title') }}</template>
    <ElForm label-position="top" @submit.prevent="submit">
      <ElFormItem :label="t('common.name')" :error="form.errors.name">
        <ElInput v-model="form.name" autocomplete="name" />
      </ElFormItem>
      <ElFormItem :label="t('common.email')" :error="form.errors.email">
        <ElInput v-model="form.email" type="email" autocomplete="username" />
      </ElFormItem>
      <ElAlert v-if="mustVerifyEmail && user.email_verified_at === null" type="warning" show-icon :closable="false" style="margin-bottom: 16px">
        <template #title>{{ t('profile.information.unverifiedEmail') }}</template>
        <Link :href="route('verification.send')" method="post" as="button" style="background: none; border: 0; padding: 0; color: var(--el-color-primary)"
          >{{ t('profile.information.resendVerification') }}</Link
        >
      </ElAlert>
      <ElSpace>
        <ElButton type="primary" native-type="submit" :loading="form.processing">{{ t('common.save') }}</ElButton>
        <ElText v-if="form.recentlySuccessful" type="success">{{ t('common.saved') }}</ElText>
      </ElSpace>
    </ElForm>
  </ElCard>
</template>
