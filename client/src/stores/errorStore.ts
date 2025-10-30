export const useErrorStore = defineStore('error', () => {
  const activeError = ref<null | CustomError>(null)

  const setError = ({ error, customCode }: { error: string | Error; customCode?: number }) => {
    if (typeof error === 'string' || error instanceof Error) {
      activeError.value = typeof error === 'string' ? Error(error) : error
      activeError.value.customCode = customCode || 500
      return
    }
  }

  const clearError = () => (activeError.value = null)

  return {
    activeError,
    setError,
    clearError,
  }
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useErrorStore, import.meta.hot))
}
