<script setup>
const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})
const { t } = useI18n({ useScope: 'global' })

const submit = () => {
  form.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => form.reset('password', 'password_confirmation')
  })
}
</script>

<template>
  <ElCard shadow="never">
    <template #header>{{ t('profile.password.title') }}</template>
    <ElForm label-position="top" @submit.prevent="submit">
      <ElFormItem :label="t('common.currentPassword')" :error="form.errors.current_password">
        <ElInput v-model="form.current_password" type="password" autocomplete="current-password" show-password />
      </ElFormItem>
      <ElFormItem :label="t('common.newPassword')" :error="form.errors.password">
        <ElInput v-model="form.password" type="password" autocomplete="new-password" show-password />
      </ElFormItem>
      <ElFormItem :label="t('common.confirmPassword')" :error="form.errors.password_confirmation">
        <ElInput v-model="form.password_confirmation" type="password" autocomplete="new-password" show-password />
      </ElFormItem>
      <ElSpace>
        <ElButton type="primary" native-type="submit" :loading="form.processing">{{ t('common.save') }}</ElButton>
        <!-- <ElText v-if="form.recentlySuccessful" type="success">{{ t('common.saved') }}</ElText> -->
      </ElSpace>
    </ElForm>
  </ElCard>
</template>
