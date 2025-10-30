<script setup lang="ts">
import { Icon } from '@iconify/vue'
const props = withDefaults(
  defineProps<{
    resource?: string
  }>(),
  {
    resource: 'records',
  },
)
</script>

<template>
  <div class="text-center py-5">
    <Icon icon="mdi:emoticon-sad-outline" width="64" height="64" class="text-muted mb-3" />
    <h5 class="text-muted">No {{ props.resource }} found</h5>
    <p class="text-muted">There are no {{ props.resource }} to display at the moment.</p>
  </div>
</template>
