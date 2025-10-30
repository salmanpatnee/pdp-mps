<script setup lang="ts">
import { Icon } from '@iconify/vue'

interface Props {
  modelValue: string
  placeholder?: string
  icon?: string
  size?: 'sm' | 'md' | 'lg'
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Search...',
  icon: 'mdi:magnify',
  size: 'md',
  class: '',
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const sizeClasses = {
  sm: 'form-control-sm',
  md: '',
  lg: 'form-control-lg',
}

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}
</script>

<template>
  <div class="input-group" :class="props.class">
    <input
      :value="modelValue"
      type="text"
      :class="['form-control', sizeClasses[size]]"
      :placeholder="placeholder"
      @input="handleInput"
    />
    <span class="input-group-text">
      <Icon :icon="icon" />
    </span>
  </div>
</template>
