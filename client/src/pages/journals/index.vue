<script setup lang="ts">
import { serialNo } from '@/utils'
import { Icon } from '@iconify/vue'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

definePage({
  meta: {
    requiresAuth: true,
  },
})

const cols = ['S.No.', 'Journal Name', 'Publisher', 'Status', 'Actions']
const { fetchJournals, deleteJournal } = useJournals()
const { data, error, queries, isPending, isError } = fetchJournals()
const deleteMutation = deleteJournal()

watch(
  () => error.value,
  (err) => {
    setError(err)
  },
)

const handleDelete = async (journal: Journal) => {
  if (confirm(`Are you sure you want to delete "${journal.name}"? This action cannot be undone.`)) {
    try {
      await deleteMutation.mutateAsync(journal.id, {
        onSuccess: () => {
          showDeletedToast('Journal')
        },
      })
    } catch (error) {
      setError(error)
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
            <PageTitle text="Journals" />
            <div class="d-flex align-items-center justify-content-between">
              <AddButton label="Add Journal" path="/journals/form" />
            </div>
          </div>
        </div>
        <div class="card-body">
          <!-- Search/Filter Section -->
          <div class="d-flex justify-content-end mb-3 row">
            <div class="col-md-6">
              <SearchInput
                v-model="queries['filter[name]']"
                placeholder="Search journals by name..."
              />
            </div>
          </div>

          <TableSkeleton v-if="isPending" />
          <PageError v-else-if="isError" :error title="Error loading journals" />

          <!-- Data Table -->
          <div v-else-if="data?.data?.length" class="card-body p-0">
            <table class="table" role="table">
              <TableHead>
                <TableHeaderCell v-for="(col, index) in cols" :key="index" :text="col" />
              </TableHead>
              <tbody>
                <tr v-for="(journal, index) in data.data" :key="journal.id" class="align-middle">
                  <td>
                    {{ serialNo(data, index) }}
                  </td>
                  <td>
                    <div>
                      <strong>{{ journal.name }}</strong>
                    </div>
                    <div class="text-muted small">
                      <span v-if="journal.issn">ISSN: {{ journal.issn }}</span>
                      <span v-if="journal.issn && journal.eissn" class="mx-1">|</span>
                      <span v-if="journal.eissn">eISSN: {{ journal.eissn }}</span>
                    </div>
                  </td>
                  <td>{{ journal.publisher || 'N/A' }}</td>
                  <td>
                    <IsActiveBadge :is-active="journal.is_active" />
                  </td>
                  <td>
                    <div
                      class="btn-group btn-group-sm"
                      role="group"
                      aria-label="Small button group"
                    >
                      <router-link
                        :to="`/journals/${journal.id}`"
                        type="button"
                        class="btn btn-outline-info"
                      >
                        <Icon icon="fa7-solid:eye" />
                      </router-link>
                      <router-link
                        :to="`/journals/form?id=${journal.id}`"
                        type="button"
                        class="btn btn-outline-secondary"
                        :title="`Edit ${journal.name}`"
                      >
                        <Icon icon="fa7-solid:edit" />
                      </router-link>
                      <button
                        type="button"
                        class="btn btn-outline-danger"
                        :disabled="deleteMutation.isPending.value"
                        @click="handleDelete(journal)"
                        :title="`Delete ${journal.name}`"
                      >
                        <Icon icon="fa7-solid:trash" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="data.last_page > 1" class="d-flex justify-content-center mt-3">
              <Bootstrap5Pagination
                :data="data"
                @pagination-change-page="(page: number) => (queries.page = page)"
              />
            </div>
          </div>

          <!-- Empty State -->
          <NoRecordFound v-else />
        </div>
      </div>
    </div>
  </div>
</template>
