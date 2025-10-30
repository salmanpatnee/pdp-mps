<script setup lang="ts">
const props = defineProps<{
  isPending: boolean
  isError: boolean
  text: string | undefined
}>()
</script>

<template>
  <h1 class="h3 mb-0 text-middle">
    <template v-if="props.isPending">Loading...</template>
    <template v-else-if="props.isError">Journal Not Found</template>
    <template v-else>{{ props.text }}</template>
  </h1>
</template>
