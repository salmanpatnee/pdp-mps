<script setup lang="ts">
import { serialNo } from '@/utils'
import { Icon } from '@iconify/vue'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

definePage({
  meta: {
    requiresAuth: true,
  },
})

const cols = ['S.No.', 'Name', 'Country', 'Affiliation', 'Role', 'Actions']

const { fetchUsers, deleteUser } = useUsers()
const { data, error, queries, isPending, isError } = fetchUsers()
const deleteMutation = deleteUser()

watch(
  () => error.value,
  (err) => {
    setError(err)
  },
)

const handleDelete = async (user: User) => {
  if (confirm(`Are you sure you want to delete? This action cannot be undone.`)) {
    try {
      await deleteMutation.mutateAsync(user.id, {
        onSuccess: () => {
          showDeletedToast('User')
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
            <PageTitle text="Users" />
            <div class="d-flex align-items-center justify-content-between">
              <AddButton label="Add User" path="/users/form" />
            </div>
          </div>
        </div>
        <div class="card-body">
          <!-- Search/Filter Section -->
          <div class="d-flex justify-content-end mb-3 row">
            <div class="col-md-6">
              <SearchInput
                v-model="queries['filter[name]']"
                placeholder="Search users by name..."
              />
            </div>
          </div>

          <TableSkeleton v-if="isPending" />
          <PageError v-else-if="isError" :error title="Error loading users" />

          <!-- Data Table -->
          <div v-else-if="data?.data?.length" class="card-body p-0">
            <table class="table" role="table">
              <TableHead>
                <TableHeaderCell v-for="(col, index) in cols" :key="index" :text="col" />
              </TableHead>
              <tbody>
                <tr v-for="(user, index) in data.data" :key="user.id" class="align-middle">
                  <td>
                    {{ serialNo(data, index) }}
                  </td>
                  <td>
                    <strong>{{ user.name }}</strong>
                  </td>
                  <td>{{ user.country || 'N/A' }}</td>
                  <td>{{ user.affiliation || 'N/A' }}</td>
                  <td>{{ user.role?.name || 'N/A' }}</td>

                  <td>
                    <div
                      class="btn-group btn-group-sm"
                      role="group"
                      aria-label="Small button group"
                    >
                      <router-link
                        :to="`/users/${user.id}`"
                        type="button"
                        class="btn btn-outline-info"
                      >
                        <Icon icon="fa7-solid:eye" />
                      </router-link>
                      <router-link
                        :to="`/users/form?id=${user.id}`"
                        type="button"
                        class="btn btn-outline-secondary"
                        :title="`Edit ${user.name}`"
                      >
                        <Icon icon="fa7-solid:edit" />
                      </router-link>
                      <button
                        type="button"
                        class="btn btn-outline-danger"
                        :disabled="deleteMutation.isPending.value"
                        @click="handleDelete(user)"
                        :title="`Delete ${user.name}`"
                      >
                        <Icon icon="fa7-solid:trash" />
                      </button>
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
