<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'

defineProps({
  mustVerifyEmail: Boolean,
  status: String
})

const { t } = useI18n({ useScope: 'global' })
</script>

<template layout="AuthenticatedLayout">
  <Head :title="t('profile.pageTitle')" />
  <div style="display: grid; gap: 16px; max-width: 920px">
    <el-tabs class="c-tabs" tab-position="left">
      <el-tab-pane :label="t('profile.information.menu')">
        <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
      </el-tab-pane>
      <el-tab-pane :label="t('profile.password.menu')">
        <UpdatePasswordForm />
      </el-tab-pane>
      <el-tab-pane :label="t('profile.delete.menu')">
        <DeleteUserForm />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<style scoped>
.c-tabs > .el-tabs__content {
  padding: 56px;
}

.el-tabs--left .el-tabs__content {
  height: 100%;
}
</style>
