<script setup lang="ts">
const props = defineProps<{
  text: string
}>()
</script>

<template>
  <h1 class="h3 mb-0 text-middle">{{ props.text }}</h1>
</template>
