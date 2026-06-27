import { usePage } from '@inertiajs/vue3'

export function can(resourceOrPermission, action = null) {
  const user = usePage().props.auth?.user

  if (!user) return false
  if (user.is_owner) return true

  const permission = action ? `${resourceOrPermission} ${action}` : resourceOrPermission
  const permissions = user.permissions ?? []

  if (Array.isArray(permissions)) {
    return permissions.includes(permission)
  }

  return Boolean(permissions?.[permission])
}
