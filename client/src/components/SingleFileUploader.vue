<template>
  <div class="mb-2">
    <label class="font-bold mb-2">{{ label }}</label>

    <div :id="dropzoneId" class="dropzone border-dashed border-2 p-6 text-center rounded-lg">
      <p class="text-gray-500">Drag & drop your file here, or click to browse</p>
    </div>

    <ul v-if="uploadedFile" class="mt-4 text-sm text-gray-700 space-y-1">
      <li>📄 {{ uploadedFile.file_name }}</li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import Dropzone from 'dropzone'

Dropzone.autoDiscover = false // Prevent auto init

interface UploadedFileResponse {
  file_name: string
  file_path: string // Assuming the backend returns the temporary path
  [key: string]: unknown
}

const props = defineProps<{
  label: string
  uploadUrl: string
  dropzoneId: string
  acceptedFiles?: string
  maxFilesize?: number
}>()

const emit = defineEmits<{
  'file-uploaded': [file: UploadedFileResponse | null]
  'file-removed': []
}>()

const uploadedFile = ref<UploadedFileResponse | null>(null)
let dz: Dropzone | null = null

onMounted(() => {
  const fullUploadUrl = `${import.meta.env.VITE_BACKEND_URL}${props.uploadUrl}` // Construct full URL
  dz = new Dropzone(`#${props.dropzoneId}`, {
    url: fullUploadUrl, // Use the full URL
    method: 'post',
    paramName: 'file',
    headers: {
      Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
    },
    acceptedFiles: props.acceptedFiles || '.pdf,.doc,.docx,.jpg,.png',
    maxFilesize: props.maxFilesize || 5, // Default to 5MB
    maxFiles: 1, // Single file upload
    addRemoveLinks: true,
    // dictDefaultMessage: 'Drag & drop your file here, or click to browse',
    dictRemoveFile: 'Remove file',
  })

  dz.on('success', (file, response: UploadedFileResponse) => {
    uploadedFile.value = response
    emit('file-uploaded', response)
  })

  dz.on('removedfile', (file) => {
    uploadedFile.value = null
    emit('file-removed')
  })

  // Handle existing files if any (for edit mode) - not implemented yet, but good to consider
})

onBeforeUnmount(() => {
  if (dz) dz.destroy()
})

// Expose the uploaded file data
defineExpose({
  uploadedFile,
})
</script>

<style scoped>
.dropzone {
  border-color: #cbd5e1;
  background-color: #f8fafc;
  transition: border-color 0.2s ease;
}
.dropzone:hover {
  border-color: #2563eb;
}
</style>
