<script setup>
const page = usePage()
const user = computed(() => page.props.auth?.user)
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
  </ElRow>
</template>
