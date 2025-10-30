<script setup lang="ts">
usePageStore().pageData.title = 'profile'

definePage({
  meta: {
    requiresAuth: true,
  },
})

const { user } = storeToRefs(useAuthStore())
</script>

<template>
  <div>
    <h1>Home</h1>
    <pre>
      {{ user }}
    </pre>
  </div>
</template>
