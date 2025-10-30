<script setup lang="ts">
const props = defineProps<{
  label: string
  text: string | undefined
  bold?: boolean
}>()
</script>

<template>
  <dl class="row">
    <dt class="col-sm-3">{{ props.label }}</dt>
    <dd class="col-sm-9">
      <strong v-if="props.bold">{{ props.text }}</strong>
      <span v-else>{{ props.text }}</span>
    </dd>
  </dl>
</template>
