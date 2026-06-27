<script setup>
const page = usePage()
const user = computed(() => page.props.auth?.user)
const notifying = ref(null)

const notificationTypes = [
  { type: 'success', label: 'Sucesso', buttonType: 'success', icon: 'ri:checkbox-circle-line' },
  { type: 'warning', label: 'Atenção', buttonType: 'warning', icon: 'ri:alert-line' },
  { type: 'error', label: 'Erro', buttonType: 'danger', icon: 'ri:close-circle-line' },
  { type: 'info', label: 'Informação', buttonType: 'info', icon: 'ri:information-line' }
]

const sendNotification = (type) => {
  notifying.value = type

  router.post(
    route('notifications.preview', { type }),
    {},
    {
      preserveScroll: true,
      onFinish: () => {
        notifying.value = null
      }
    }
  )
}
</script>

<template layout="AuthenticatedLayout">
  <Head title="Dashboard" />
  <ElRow :gutter="16">
    <ElCol :xs="24" :md="12">
      <ElCard shadow="never">
        <template #header>Welcome</template>
        <p style="margin: 0; color: var(--el-text-color-regular)">
          You are signed in as <strong>{{ user?.name }}</strong
          >.
        </p>
      </ElCard>
    </ElCol>
    <ElCol :xs="24" :md="12">
      <ElCard shadow="never">
        <template #header>Starter kit</template>
        <ElSpace wrap>
          <ElTag type="primary">Inertia</ElTag>
          <ElTag type="success">Vue</ElTag>
          <ElTag type="info">TypeScript</ElTag>
          <ElTag type="warning">Element Plus</ElTag>
        </ElSpace>
      </ElCard>
    </ElCol>
    <ElCol :span="24">
      <ElCard shadow="never">
        <template #header>Notificações do servidor</template>
        <ElSpace wrap>
          <ElButton
            v-for="notification in notificationTypes"
            :key="notification.type"
            :type="notification.buttonType"
            :loading="notifying === notification.type"
            :disabled="notifying !== null"
            @click="sendNotification(notification.type)"
          >
            <AppIcon
              v-if="notifying !== notification.type"
              :icon="notification.icon"
            />
            {{ notification.label }}
          </ElButton>
        </ElSpace>
      </ElCard>
    </ElCol>
  </ElRow>
</template>
