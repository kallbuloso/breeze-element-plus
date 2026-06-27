<script setup>
const props = defineProps({ status: Number })

const icon = computed(
  () =>
    ({
      403: 'tabler:face-id-error',
      404: 'tabler:error-404',
      500: 'mdi:server-network-off',
      503: 'gravity-ui:person-worker'
    })[props.status]
)

const headline = computed(
  () =>
    ({
      403: 'Erro 403',
      404: 'Erro 404',
      500: 'Erro 500',
      503: 'Erro 503'
    })[props.status]
)

const title = computed(
  () =>
    ({
      403: 'Acesso proibido',
      404: 'Página não encontrada',
      500: 'Erro no servidor',
      503: 'Serviço indisponível'
    })[props.status]
)

const description = computed(
  () =>
    ({
      403: 'Você não tem permissão para acessar esta página.',
      404: 'Desculpe, a página que você está procurando não foi encontrada.',
      500: 'Algo deu errado em nossos servidores. Tente novamente em breve.',
      503: 'Estamos em manutenção. Por favor, volte em alguns instantes.'
    })[props.status]
)
</script>

<template layout="AuthLayout">
  <Head :title="title" />

  <AppFormCard>
    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 8px 0 24px">
      <AppIcon
        :icon="icon"
        width="64"
        height="64"
        style="color: var(--el-color-primary); margin-bottom: 16px"
      />

      <p style="font-size: 13px; font-weight: 600; color: var(--el-text-color-secondary); letter-spacing: 0.05em; text-transform: uppercase; margin: 0 0 8px">
        {{ headline }}
      </p>

      <h2 style="font-size: 20px; font-weight: 700; color: var(--el-text-color-primary); margin: 0 0 12px">
        {{ title }}
      </h2>

      <p style="font-size: 14px; color: var(--el-text-color-secondary); line-height: 1.6; margin: 0 0 28px">
        {{ description }}
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
