<script setup>
defineProps({
  mustVerifyEmail: Boolean,
  status: String
})

const user = usePage().props.auth.user
const form = useForm({
  name: user.name,
  email: user.email
})

const submit = () => form.patch(route('profile.update'))
</script>

<template>
  <ElCard shadow="never">
    <template #header>Profile information</template>
    <ElForm label-position="top" @submit.prevent="submit">
      <ElFormItem label="Name" :error="form.errors.name">
        <ElInput v-model="form.name" autocomplete="name" />
      </ElFormItem>
      <ElFormItem label="Email" :error="form.errors.email">
        <ElInput v-model="form.email" type="email" autocomplete="username" />
      </ElFormItem>
      <ElAlert v-if="mustVerifyEmail && user.email_verified_at === null" type="warning" show-icon :closable="false" style="margin-bottom: 16px">
        <template #title>Your email address is unverified.</template>
        <Link :href="route('verification.send')" method="post" as="button" style="background: none; border: 0; padding: 0; color: var(--el-color-primary)"
          >Click here to resend the verification email.</Link
        >
      </ElAlert>
      <ElSpace>
        <ElButton type="primary" native-type="submit" :loading="form.processing">Save</ElButton>
        <ElText v-if="form.recentlySuccessful" type="success">Saved.</ElText>
      </ElSpace>
    </ElForm>
  </ElCard>
</template>
