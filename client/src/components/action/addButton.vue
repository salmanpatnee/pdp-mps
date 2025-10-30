<script setup lang="ts">
import { Icon } from '@iconify/vue'

const props = defineProps<{
  path: string
  label: string
}>()
</script>

<template>
  <router-link :to="props.path" class="btn btn-primary d-flex gap-1">
    <Icon icon="mdi-light:plus-circle" width="24" height="24" /> <span>{{ props.label }}</span>
  </router-link>
</template>
