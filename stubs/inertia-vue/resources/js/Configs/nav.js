export const nav = [
  {
    key: 'dashboard',
    labelKey: 'navigation.dashboard',
    icon: 'ri:dashboard-line',
    features: [
      {
        key: 'dashboard.main',
        labelKey: 'navigation.main',
        icon: 'ri:bar-chart-box-line',
        route: 'dashboard'
      }
    ]
  },
  {
    key: 'account',
    labelKey: 'navigation.account',
    icon: 'ri:user-settings-line',
    features: [
      {
        key: 'account.profile',
        labelKey: 'navigation.profile',
        icon: 'ri:user-3-line',
        route: 'profile.edit'
      }
    ]
  }
]
