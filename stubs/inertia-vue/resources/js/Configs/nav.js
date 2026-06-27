export const nav = [
  {
    key: 'dashboard',
    label: 'Dashboards',
    icon: 'ri:dashboard-line',
    features: [
      {
        key: 'dashboard.main',
        label: 'Principal',
        icon: 'ri:bar-chart-box-line',
        route: 'dashboard'
      }
    ]
  },
  {
    key: 'account',
    label: 'Conta',
    icon: 'ri:user-settings-line',
    features: [
      {
        key: 'account.profile',
        label: 'Perfil',
        icon: 'ri:user-3-line',
        route: 'profile.edit'
      }
    ]
  }
]
