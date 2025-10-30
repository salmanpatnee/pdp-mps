<script setup lang="ts">
import { serialNo } from '@/utils'
import { Icon } from '@iconify/vue'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

definePage({
  meta: {
    requiresAuth: true,
  },
})

const cols = ['S.No.', 'Journal', 'Reference No.', 'Date', 'Title', 'Status', 'Actions']

const { fetchManuscripts, deleteManuscript } = useManuscripts()
const { data, error, queries, isPending, isError } = fetchManuscripts()
const deleteMutation = deleteManuscript()

watch(
  () => error.value,
  (err) => {
    setError(err)
  },
)

const handleDelete = async (manuscript: Manuscript) => {
  if (confirm(`Are you sure you want to delete? This action cannot be undone.`)) {
    try {
      await deleteMutation.mutateAsync(manuscript.id, {
        onSuccess: () => {
          showDeletedToast('Manuscript')
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
            <PageTitle text="Manscripts" />
            <div class="d-flex align-items-center justify-content-between">
              <AddButton label="Add Manuscript" path="/manuscripts/form" />
            </div>
          </div>
        </div>
        <div class="card-body">
          <!-- Search/Filter Section -->
          <div class="d-flex justify-content-end mb-3 row">
            <div class="col-md-6">
              <SearchInput v-model="queries['filter[title]']" placeholder="Search manuscripts..." />
            </div>
          </div>

          <TableSkeleton v-if="isPending" />
          <PageError v-else-if="isError" :error title="Error loading manuscripts" />

          <!-- Data Table -->
          <div v-else-if="data?.data?.length" class="card-body p-0">
            <table class="table" role="table">
              <TableHead>
                <TableHeaderCell v-for="(col, index) in cols" :key="index" :text="col" />
              </TableHead>
              <tbody>
                <tr
                  v-for="(manuscript, index) in data.data"
                  :key="manuscript.id"
                  class="align-middle"
                >
                  <td>
                    {{ serialNo(data, index) }}
                  </td>
                  <td>
                    {{ manuscript.journal.name }}
                  </td>
                  <td>{{ manuscript.reference_no }}</td>
                  <td><AppDate :timestamp="manuscript.created_at" /></td>
                  <td>{{ manuscript.title }}</td>
                  <td>
                    <span class="badge bg-warning">{{ manuscript.status }}</span>
                  </td>

                  <td>
                    <div
                      class="btn-group btn-group-sm"
                      role="group"
                      aria-label="Small button group"
                    >
                      <router-link
                        :to="`/manuscripts/${manuscript.id}`"
                        type="button"
                        class="btn btn-outline-info"
                      >
                        <Icon icon="fa7-solid:eye" />
                      </router-link>
                      <!-- <router-link
                        :to="`/manuscripts/form?id=${manuscript.id}`"
                        type="button"
                        class="btn btn-outline-secondary"
                        :title="`Edit ${manuscript.id}`"
                      >
                        <Icon icon="fa7-solid:edit" />
                      </router-link>
                      <button
                        type="button"
                        class="btn btn-outline-danger"
                        :disabled="deleteMutation.isPending.value"
                        @click="handleDelete(manuscript)"
                        :title="`Delete ${manuscript.id}`"
                      >
                        <Icon icon="fa7-solid:trash" />
                      </button> -->
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
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
