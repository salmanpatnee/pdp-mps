<script setup lang="ts">
const props = defineProps<{
  title: string
  error: Error | null
}>()
</script>
<template>
  <div class="alert alert-danger">
    <h5 class="alert-heading">{{ props.title }}</h5>
    <p class="mb-0">{{ props.error?.message || 'An error occurred while fetching data.' }}</p>
  </div>
</template>
