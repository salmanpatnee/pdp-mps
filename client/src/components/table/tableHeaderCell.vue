<script setup lang="ts">
const props = defineProps<{
  text: string
}>()
</script>
<template>
  <th scope="col" class="bg-primary text-white">{{ props.text }}</th>
</template>
