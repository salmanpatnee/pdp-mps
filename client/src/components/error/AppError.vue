<script setup lang="ts">
const router = useRouter()

const errorStore = useErrorStore()
const error = ref(errorStore.activeError)

const message = ref('')
const errorCode = ref<number>(500)

const ErrorComponent = import.meta.env.DEV
  ? defineAsyncComponent(() => import('@/components/error/AppErrorDev.vue'))
  : defineAsyncComponent(() => import('@/components/error/AppErrorProd.vue'))
if (error.value) {
  message.value = error.value.message
  errorCode.value = error.value.customCode || 500
}

router.afterEach(() => {
  errorStore.clearError()
})
</script>

<template>
  <ErrorComponent :message :errorCode />
</template>
