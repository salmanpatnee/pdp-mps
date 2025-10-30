<script setup lang="ts">
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import localizedFormat from 'dayjs/plugin/localizedFormat'
import type { PropType } from 'vue'

dayjs.extend(relativeTime)
dayjs.extend(localizedFormat)

const props = defineProps({
  timestamp: {
    type: [Number, String, Date] as PropType<number | string | Date>,
    required: true,
  },
})

const diffForHumans = () => dayjs(props.timestamp).format('MMM D, YYYY')
</script>

<template>
  <span>
    {{ diffForHumans() }}
  </span>
</template>
