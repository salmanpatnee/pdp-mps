<script setup lang="ts">
import { Icon } from '@iconify/vue'

const props = defineProps<{
  path: string
  label: string
}>()
</script>

<template>
  <router-link :to="props.path" class="btn btn-primary d-flex gap-1">
    <Icon icon="mdi:arrow-left" width="20" height="20" />
    <span>{{ props.label }}</span>
  </router-link>
</template>
