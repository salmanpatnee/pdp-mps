<script setup lang="ts">
import { Icon } from '@iconify/vue'
import FormSkeleton from './journalSkeleton.vue'
import { serialNo } from '@/utils'

definePage({
  meta: {
    requiresAuth: true,
  },
})

const router = useRouter()
const route = useRoute('/manuscripts/[id]')
const { fetchManuscript, deleteManuscript, downloadFile } = useManuscripts()

const {
  data: manuscript,
  isPending,
  isError,
  error,
} = fetchManuscript(computed(() => (route.params.id ? Number(route.params.id) : null)))

const deleteMutation = deleteManuscript()
const isDownloading = ref(false)

const authorCols = ['S.No.', 'First Name', 'Last Name', 'Email', 'Country', 'Affiliation', 'Principal Author']
const fileCols = ['S.No.', 'File Name', 'File Type', 'File Extension', 'Action']

watch(
  () => error.value,
  (err) => {
    setError(err)
  },
)

const handleDelete = async () => {
  if (!manuscript.value) return

  if (
    confirm(
      `Are you sure you want to delete "${manuscript.value.title}"? This action cannot be undone.`,
    )
  ) {
    try {
      await deleteMutation.mutateAsync(manuscript.value.id, {
        onSuccess: () => {
          showDeletedToast('Manuscript')
          router.push('/manuscripts')
        },
      })
    } catch (error) {
      handleErrorToast(error, 'An error occurred while deleting the journal')
    }
  }
}

const authors = computed(() => {
  let contributorsList = manuscript.value?.contributors ? [...manuscript.value.contributors] : []
  const mainAuthor = manuscript.value?.author
  if (mainAuthor) {
    // Check if the main author is already in the contributors list
    const exists = contributorsList.some((c) => c.user_id === mainAuthor.id)
    if (!exists) {
      // Add main author as a contributor if not already present
      contributorsList = [
        {
          id: mainAuthor.id, // Use main author's ID as a temporary ID for display
          user_id: mainAuthor.id,
          first_name: mainAuthor.name.split(' ')[0],
          last_name: mainAuthor.name.split(' ').slice(1).join(' '),
          email: mainAuthor.email,
          affiliation: mainAuthor.affiliation || '',
          country: mainAuthor.country || '',
          is_principal: true, // Main author is always principal
          user: mainAuthor, // Attach the user object for easy access
        },
        ...contributorsList,
      ]
    }
  }
  return contributorsList
})

const handleDownload = async (fileId: number, fileName: string) => {
  try {
    isDownloading.value = true
    await downloadFile(fileId, fileName)
  } catch (error) {
    console.error('Download failed:', error)
    // You could add a toast notification here for error handling
  } finally {
    isDownloading.value = false
  }
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="card mb-3">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <LoadingPageTitle :isPending :isError :text="manuscript?.reference_no" />
            <AllButton path="/manuscripts" label="All Manuscripts" />
          </div>
        </div>
        <div class="card-body">
          <div class="row" v-if="isPending">
            <div class="col">
              <FormSkeleton />
            </div>
            <div class="col">
              <FormSkeleton />
            </div>
          </div>

          <!-- Manuscript Data -->
          <div v-else-if="manuscript">
            <div class="row">
              <div class="col">
                <Cell label="Manuscript Title" :text="manuscript.title" :bold="true" />
              </div>
              <div class="col">
                <Cell label="Journal" :text="manuscript.journal.name" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <Cell label="Article Type" :text="manuscript.article_type.name" />
              </div>
              <div class="col">
                <Cell label="Submission Type" :text="manuscript.submission_type" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <dl class="row">
                  <dt class="col-sm-12">Abstract</dt>
                  <dd class="col-sm-12">
                    <p v-if="manuscript.abstract" class="mb-0">{{ manuscript.abstract }}</p>
                    <p v-else class="text-muted mb-0">No description available.</p>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <LoadingPageTitle :isPending :isError text="Author Details" />
          </div>
        </div>
        <div class="card-body">
          <div class="row" v-if="isPending">
            <div class="col">
              <FormSkeleton />
            </div>
            <div class="col">
              <FormSkeleton />
            </div>
          </div>

          <!-- Manuscript Data -->
          <div v-else-if="authors.length">
            <table class="table" role="table">
              <TableHead>
                <TableHeaderCell v-for="(col, index) in authorCols" :key="index" :text="col" />
              </TableHead>
              <tbody>
                <tr v-for="(c, index) in authors" :key="c.id || c.user_id" class="align-middle">
                  <td>
                    {{ serialNo(c, index) }}
                  </td>
                  <td>
                    <strong>{{ c.first_name }}</strong>
                  </td>
                  <td>
                    <strong>{{ c.last_name }}</strong>
                  </td>
                  <td>{{ c.email }}</td>
                  <td>{{ c.affiliation ?? 'N/A' }}</td>
                  <td>{{ c.country ?? 'N/A' }}</td>
                  <td>{{ c.is_principal ? 'Yes' : 'No' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <LoadingPageTitle :isPending :isError text="Files Details" />
          </div>
        </div>
        <div class="card-body">
          <div class="row" v-if="isPending">
            <div class="col">
              <FormSkeleton />
            </div>
            <div class="col">
              <FormSkeleton />
            </div>
          </div>

          <!-- Manuscript Data -->
          <div v-else-if="manuscript?.files.length">
            <table class="table" role="table">
              <TableHead>
                <TableHeaderCell v-for="(col, index) in fileCols" :key="index" :text="col" />
              </TableHead>
              <tbody>
                <tr v-for="(c, index) in manuscript.files" :key="c.id" class="align-middle">
                  <td>
                    {{ serialNo(c, index) }}
                  </td>
                  <td>
                    <strong>{{ c.file_name }}</strong>
                  </td>
                  <td class="text-capitalize">{{ c.file_type }}</td>
                  <td>{{ c.file_extension ?? 'N/A' }}</td>
                  <td>
                    <button
                      @click="handleDownload(c.id, c.file_name || 'download')"
                      class="btn btn-sm btn-primary"
                      :disabled="isDownloading"
                    >
                      <i class="fas fa-download"></i> Download
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <hr />
      <div class="row">
        <div class="col">
          <!-- <div class="d-flex gap-2">
            <router-link :to="`/manuscripts/form?id=${manuscript?.id}`" class="btn btn-primary">
              <Icon icon="mdi:pencil" class="me-1" />
              Edit Manuscript
            </router-link>
            <button
              type="button"
              class="btn btn-danger"
              :disabled="deleteMutation.isPending.value"
              @click="handleDelete"
            >
              <Icon icon="mdi:delete" class="me-1" />
              {{ deleteMutation.isPending.value ? 'Deleting...' : 'Delete Manuscript' }}
            </button>
          </div> -->
        </div>
      </div>
    </div>
  </div>
</template>
