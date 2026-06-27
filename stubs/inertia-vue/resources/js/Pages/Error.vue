<script setup>
const props = defineProps({ status: Number })

const errors = {
  403: {
    icon: 'tabler:face-id-error',
    headline: 'Erro 403',
    title: 'Acesso proibido',
    description: 'Você não tem permissão para acessar esta página.'
  },
  404: {
    icon: 'tabler:error-404',
    headline: 'Erro 404',
    title: 'Página não encontrada',
    description: 'Desculpe, a página que você está procurando não foi encontrada.'
  },
  500: {
    icon: 'mdi:server-network-off',
    headline: 'Erro 500',
    title: 'Erro no servidor',
    description: 'Algo deu errado em nossos servidores. Tente novamente em breve.'
  },
  503: {
    icon: 'gravity-ui:person-worker',
    headline: 'Erro 503',
    title: 'Serviço indisponível',
    description: 'Estamos em manutenção. Por favor, volte em alguns instantes.'
  }
}

const error = computed(() => errors[props.status] ?? errors[404])
</script>

<template layout="AuthLayout">
  <Head :title="error.title" />

  <AppFormCard>
    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 8px 0 24px">
      <AppIcon
        :icon="error.icon"
        width="64"
        height="64"
        style="color: var(--el-color-primary); margin-bottom: 16px"
      />

      <p style="font-size: 13px; font-weight: 600; color: var(--el-text-color-secondary); letter-spacing: 0.05em; text-transform: uppercase; margin: 0 0 8px">
        {{ error.headline }}
      </p>

      <h2 style="font-size: 20px; font-weight: 700; color: var(--el-text-color-primary); margin: 0 0 12px">
        {{ error.title }}
      </h2>

      <p style="font-size: 14px; color: var(--el-text-color-secondary); line-height: 1.6; margin: 0 0 28px">
        {{ error.description }}
      </p>

      <ElButton
        type="primary"
        :icon="iconify('ri:home-line')"
        tag="a"
        href="/"
      >
        Voltar ao início
      </ElButton>
    </div>
  </AppFormCard>
</template>
