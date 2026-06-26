<script setup>
const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>

<template>
  <ElDropdown trigger="click">
    <ElButton text>
      <span style="display: inline-flex; align-items: center; gap: 8px">
        <ElAvatar :size="28">{{ user?.name?.charAt(0) ?? 'U' }}</ElAvatar>
        <span style="max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap">{{ user?.name }}</span>
      </span>
    </ElButton>
    <template #dropdown>
      <ElDropdownMenu>
        <ElDropdownItem>
          <Link :href="route('profile.edit')" style="text-decoration: none">Profile</Link>
        </ElDropdownItem>
        <ElDropdownItem divided>
          <Link :href="route('logout')" method="post" as="button" style="background: none; border: 0; padding: 0">Log Out</Link>
        </ElDropdownItem>
      </ElDropdownMenu>
    </template>
  </ElDropdown>
</template>
