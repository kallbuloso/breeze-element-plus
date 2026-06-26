<script setup>
const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

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
    <template #header>Update password</template>
    <ElForm label-position="top" @submit.prevent="submit">
      <ElFormItem label="Current password" :error="form.errors.current_password">
        <ElInput v-model="form.current_password" type="password" autocomplete="current-password" show-password />
      </ElFormItem>
      <ElFormItem label="New password" :error="form.errors.password">
        <ElInput v-model="form.password" type="password" autocomplete="new-password" show-password />
      </ElFormItem>
      <ElFormItem label="Confirm password" :error="form.errors.password_confirmation">
        <ElInput v-model="form.password_confirmation" type="password" autocomplete="new-password" show-password />
      </ElFormItem>
      <ElSpace>
        <ElButton type="primary" native-type="submit" :loading="form.processing">Save</ElButton>
        <ElText v-if="form.recentlySuccessful" type="success">Saved.</ElText>
      </ElSpace>
    </ElForm>
  </ElCard>
</template>
