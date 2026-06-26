<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'

defineProps({
  mustVerifyEmail: Boolean,
  status: String
})
</script>

<template layout="AuthenticatedLayout">
  <Head title="Profile" />
  <div style="display: grid; gap: 16px; max-width: 920px">
    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
    <UpdatePasswordForm />
    <DeleteUserForm />
  </div>
</template>
