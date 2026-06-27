<script setup>
const form = useForm({ password: '' })
const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => form.reset('password')
  })
}
</script>

<template layout="AuthLayout">
  <Head title="Confirm password" />

  <AppFormCard>
    <template #title>Confirm password</template>
    <template #description>This is a secure area. Please confirm your password before continuing.</template>

    <ElForm
      label-position="top"
      @submit.prevent="submit"
    >
      <ElFormItem
        label="Password"
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
          Confirm
        </ElButton>
      </ElFormItem>
    </ElForm>
  </AppFormCard>
</template>
