<script setup lang="ts">
const props = defineProps<{
  isActive: boolean
}>()
</script>
<template>
  <span :class="props.isActive ? 'badge bg-success' : 'badge bg-secondary'">
    {{ props.isActive ? 'Active' : 'Inactive' }}
  </span>
</template>
