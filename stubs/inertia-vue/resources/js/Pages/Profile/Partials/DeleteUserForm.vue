<script setup>
const confirming = ref(false)
const form = useForm({ password: '' })
const { t } = useI18n({ useScope: 'global' })

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => (confirming.value = false),
    onFinish: () => form.reset()
  })
}
</script>

<template>
  <ElCard shadow="never">
    <template #header>{{ t('profile.delete.title') }}</template>
    <p style="margin-top: 0; color: var(--el-text-color-secondary)">{{ t('profile.delete.description') }}</p>
    <ElButton type="danger" @click="confirming = true">{{ t('profile.delete.submit') }}</ElButton>
    <ElDialog v-model="confirming" :title="t('profile.delete.title')" width="min(92vw, 460px)">
      <p style="margin-top: 0">{{ t('profile.delete.confirmation') }}</p>
      <ElInput v-model="form.password" type="password" autocomplete="current-password" show-password @keyup.enter="deleteUser" />
      <ElText v-if="form.errors.password" type="danger">{{ form.errors.password }}</ElText>
      <template #footer>
        <ElButton @click="confirming = false">{{ t('common.cancel') }}</ElButton>
        <ElButton type="danger" :loading="form.processing" @click="deleteUser">{{ t('profile.delete.submit') }}</ElButton>
      </template>
    </ElDialog>
  </ElCard>
</template>
