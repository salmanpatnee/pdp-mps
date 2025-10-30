<template>
  <div class="mb-2">
    <label class="font-bold mb-2">Upload Files</label>

    <div
      id="manuscript-dropzone"
      class="dropzone border-dashed border-2 p-6 text-center rounded-lg"
    >
      <p class="text-gray-500">Drag & drop your manuscript files here, or click to browse</p>
    </div>

    <ul v-if="uploadedFiles.length" class="mt-4 text-sm text-gray-700 space-y-1">
      <li v-for="file in uploadedFiles" :key="file.file_name">📄 {{ file.file_name }}</li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Dropzone from 'dropzone'

Dropzone.autoDiscover = false // Prevent auto init

interface UploadedFile {
  file_name: string
  [key: string]: unknown
}

const uploadedFiles = ref<UploadedFile[]>([])
let dz: Dropzone | null = null

onMounted(() => {
  dz = new Dropzone('#manuscript-dropzone', {
    url: 'http://localhost:8000/api/manuscripts/upload-temp',
    method: 'post',
    paramName: 'file',
    headers: {
      Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
    },
    acceptedFiles: '.pdf,.doc,.docx,.zip,.jpg,.png',
    maxFilesize: 50,
    parallelUploads: 5,
    addRemoveLinks: true,
  })

  dz.on('success', (file, response) => {
    uploadedFiles.value.push(response)
  })
})

onBeforeUnmount(() => {
  if (dz) dz.destroy()
})

// expose uploaded files to parent
defineExpose({
  uploadedFiles,
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
