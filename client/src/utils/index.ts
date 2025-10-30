import { AxiosError, isAxiosError } from 'axios'
import { toast, type ToastOptions } from 'vue3-toastify'

export function handleInvalidForm(error: any, node?: FormKitNode) {
  if (error instanceof AxiosError && error.response?.status === 422) {
    node?.setErrors([], error.response.data.errors)
  }
}

export function setError(error: any) {
  if (isAxiosError(error)) {
    useErrorStore().setError({
      error: error.response?.data.message,
      customCode: error.status,
    })
  }
}

export function serialNo(data: any, index: number) {
  return Number.isFinite(data.current_page) && Number.isFinite(data.per_page)
    ? (data.current_page - 1) * data.per_page + index + 1
    : index + 1
}

export type ToastPosition = string

export interface ToastConfig {
  type?: ToastType
  position?: ToastPosition
  autoClose?: number
  hideProgressBar?: boolean
  closeOnClick?: boolean
  pauseOnHover?: boolean
  draggable?: boolean
}

// Generic toast function
export function showToast(message: string, config: ToastConfig = {}) {
  const defaultConfig: ToastConfig = {
    type: 'info',
    position: toast.POSITION.TOP_RIGHT,
    autoClose: 5000,
    hideProgressBar: false,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
  }

  const finalConfig = { ...defaultConfig, ...config }

  toast(message, {
    type: finalConfig.type,
    position: finalConfig.position,
    autoClose: finalConfig.autoClose,
    hideProgressBar: finalConfig.hideProgressBar,
    closeOnClick: finalConfig.closeOnClick,
    pauseOnHover: finalConfig.pauseOnHover,
    draggable: finalConfig.draggable,
  } as ToastOptions)
}

// Convenience functions for different toast types
export function showSuccessToast(
  message: string,
  position: ToastPosition = toast.POSITION.TOP_RIGHT,
) {
  showToast(message, { type: 'success', position })
}

export function showErrorToast(
  message: string,
  position: ToastPosition = toast.POSITION.TOP_RIGHT,
) {
  showToast(message, { type: 'error', position })
}

export function showInfoToast(message: string, position: ToastPosition = toast.POSITION.TOP_RIGHT) {
  showToast(message, { type: 'info', position })
}

export function showWarningToast(
  message: string,
  position: ToastPosition = toast.POSITION.TOP_RIGHT,
) {
  showToast(message, { type: 'warning', position })
}

// Generic error handler for toast messages
export function handleErrorToast(error: unknown, fallbackMessage: string = 'An error occurred') {
  let message = fallbackMessage

  if (error instanceof Error) {
    message = error.message
  } else if (typeof error === 'string') {
    message = error
  } else if (error && typeof error === 'object' && 'message' in error) {
    message = String(error.message)
  }

  showErrorToast(message)
}

// Generic success handler for toast messages
export function handleSuccessToast(
  message: string,
  position: ToastPosition = toast.POSITION.TOP_RIGHT,
) {
  showSuccessToast(message, position)
}

// Generic CRUD operation toasts
export function showCreatedToast(resource: string = 'Item') {
  showSuccessToast(`${resource} created successfully.`)
}

export function showUpdatedToast(resource: string = 'Item') {
  showSuccessToast(`${resource} updated successfully.`)
}

export function showDeletedToast(resource: string = 'Item') {
  showSuccessToast(`${resource} deleted successfully.`)
}

export function showSavedToast(
  resource: string = 'Item',
  position: ToastPosition = toast.POSITION.BOTTOM_RIGHT,
) {
  showSuccessToast(`${resource} saved successfully.`, position)
}
