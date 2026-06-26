import { Icon } from '@iconify/vue'
import { h } from 'vue'

export function iconify(icon) {
  return () => h(Icon, { icon })
}
