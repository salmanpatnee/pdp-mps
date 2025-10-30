<script setup lang="ts">
import { Icon } from '@iconify/vue'

definePage({
  meta: {
    requiresAuth: true,
  },
})

const route = useRoute('/users/[id]')
const router = useRouter()
const { fetchUser, deleteUser } = useUsers()

const {
  data: user,
  isPending,
  isError,
  error,
} = fetchUser(computed(() => (route.params.id ? Number(route.params.id) : null)))

const deleteMutation = deleteUser()

watch(
  () => error.value,
  (err) => {
    setError(err)
  },
)

const handleDelete = async () => {
  if (!user.value) return

  if (confirm(`Are you sure you want to delete? This action cannot be undone.`)) {
    try {
      await deleteMutation.mutateAsync(user.value.id, {
        onSuccess: () => {
          showDeletedToast('User')
          router.push('/users')
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
            <LoadingPageTitle :isPending :isError :text="user?.name" />
            <AllButton path="/users" label="All Users" />
          </div>
        </div>
        <div class="card-body">
          <FormSkeleton v-if="isPending" />
          <!-- user Data -->
          <div v-else-if="user">
            <div class="row">
              <div class="col">
                <Cell label="Name" :text="user.name" :bold="true" />
              </div>
              <div class="col">
                <Cell label="Email" :text="user.email" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <Cell label="Country" :text="user.country" />
              </div>
              <div class="col">
                <Cell label="Affiliation" :text="user.affiliation" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <Cell label="Profile Link" :text="user.profile_link || 'N/A'" />
              </div>
              <div class="col">
                <dl class="row">
                  <Cell label="Role" :text="user?.role?.name" />
                </dl>
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col">
                <dl class="row">
                  <Cell label="Journal" :text="user?.journal?.name" />
                </dl>
              </div>
              <div class="col"></div>
            </div>
            <hr />

            <!-- Action Buttons -->
            <hr />
            <div class="row">
              <div class="col">
                <div class="d-flex gap-2">
                  <router-link :to="`/users/form?id=${user.id}`" class="btn btn-primary">
                    <Icon icon="mdi:pencil" class="me-1" />
                    Edit user
                  </router-link>
                  <button
                    type="button"
                    class="btn btn-danger"
                    :disabled="deleteMutation.isPending.value"
                    @click="handleDelete"
                  >
                    <Icon icon="mdi:delete" class="me-1" />
                    {{ deleteMutation.isPending.value ? 'Deleting...' : 'Delete user' }}
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
