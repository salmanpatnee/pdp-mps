<script setup lang="ts">
import { Icon } from '@iconify/vue'
import FormSkeleton from './journalSkeleton.vue'

definePage({
  meta: {
    requiresAuth: true,
  },
})

const router = useRouter()
const route = useRoute('/journals/[id]')
const { fetchJournal, deleteJournal } = useJournals()

const {
  data: journal,
  isPending,
  isError,
  error,
} = fetchJournal(computed(() => (route.params.id ? Number(route.params.id) : null)))

const deleteMutation = deleteJournal()

watch(
  () => error.value,
  (err) => {
    setError(err)
  },
)

const handleDelete = async () => {
  if (!journal.value) return

  if (
    confirm(
      `Are you sure you want to delete "${journal.value.name}"? This action cannot be undone.`,
    )
  ) {
    try {
      await deleteMutation.mutateAsync(journal.value.id, {
        onSuccess: () => {
          showDeletedToast('Journals')
          router.push('/journals')
        },
      })
    } catch (error) {
      handleErrorToast(error, 'An error occurred while deleting the journal')
    }
  }
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <LoadingPageTitle :isPending :isError :text="journal?.name" />
            <AllButton path="/journals" label="All Journals" />
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

          <!-- Journal Data -->
          <div v-else-if="journal">
            <div class="row">
              <div class="col">
                <Cell label="Journal Title" :text="journal.name" :bold="true" />
              </div>
              <div class="col">
                <Cell label="Publisher" :text="journal.publisher" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <Cell label="ISSN" :text="journal.issn" />
              </div>
              <div class="col">
                <Cell label="EISSN" :text="journal.eissn" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <Cell label="Abbreviation" :text="journal.abbreviation || 'N/A'" />
              </div>
              <div class="col">
                <dl class="row">
                  <Cell label="Email" :text="journal.email" />
                </dl>
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <dl class="row">
                  <Cell label="Website URL" :text="journal.website_url" />
                </dl>
              </div>
              <div class="col">
                <dl class="row">
                  <dt class="col-sm-3">Status</dt>
                  <dd class="col-sm-9">
                    <IsActiveBadge :is-active="journal.is_active" />
                  </dd>
                </dl>
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <dl class="row">
                  <dt class="col-sm-12">Description</dt>
                  <dd class="col-sm-12">
                    <p v-if="journal.description" class="mb-0">{{ journal.description }}</p>
                    <p v-else class="text-muted mb-0">No description available.</p>
                  </dd>
                </dl>
              </div>
            </div>

            <!-- Action Buttons -->
            <hr />
            <div class="row">
              <div class="col">
                <div class="d-flex gap-2">
                  <router-link :to="`/journals/form?id=${journal.id}`" class="btn btn-primary">
                    <Icon icon="mdi:pencil" class="me-1" />
                    Edit Journal
                  </router-link>
                  <button
                    type="button"
                    class="btn btn-danger"
                    :disabled="deleteMutation.isPending.value"
                    @click="handleDelete"
                  >
                    <Icon icon="mdi:delete" class="me-1" />
                    {{ deleteMutation.isPending.value ? 'Deleting...' : 'Delete Journal' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
