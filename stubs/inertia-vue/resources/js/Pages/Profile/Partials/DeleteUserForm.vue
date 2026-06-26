<script setup>
const confirming = ref(false)
const form = useForm({ password: '' })

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
    <template #header>Delete account</template>
    <p style="margin-top: 0; color: var(--el-text-color-secondary)">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
    <ElButton type="danger" @click="confirming = true">Delete account</ElButton>
    <ElDialog v-model="confirming" title="Delete account" width="min(92vw, 460px)">
      <p style="margin-top: 0">Please enter your password to confirm you would like to permanently delete your account.</p>
      <ElInput v-model="form.password" type="password" autocomplete="current-password" show-password @keyup.enter="deleteUser" />
      <ElText v-if="form.errors.password" type="danger">{{ form.errors.password }}</ElText>
      <template #footer>
        <ElButton @click="confirming = false">Cancel</ElButton>
        <ElButton type="danger" :loading="form.processing" @click="deleteUser">Delete account</ElButton>
      </template>
    </ElDialog>
  </ElCard>
</template>
