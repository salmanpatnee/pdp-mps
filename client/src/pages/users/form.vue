<script setup lang="ts">
definePage({
  meta: {
    requiresAuth: true,
  },
})

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => route.query.id !== undefined)
const userId = computed(() => (isEdit.value ? Number(route.query.id) : null))

const roles = useRoles()
const { fetchJournals } = useJournals()
const { data: journals, isLoading: isJournalLoading } = fetchJournals()

const { createUser, updateUser, fetchUser } = useUsers()
const createUserMutation = createUser()
const updateUserMutation = updateUser()

// Fetch user data for edit mode
const { data: existingUser, isPending: isFetchingUser } = fetchUser(userId)

// Use the appropriate mutation based on mode
const currentMutation = computed(() => (isEdit.value ? updateUserMutation : createUserMutation))

const handleSubmit = async (data: UserFormPayload, node?: FormKitNode) => {
  try {
    if (isEdit.value && userId.value) {
      await updateUserMutation.mutateAsync(
        {
          id: userId.value,
          data: data as User,
        },
        {
          onSuccess: () => {
            showUpdatedToast('User')
            router.push('/users')
          },
        },
      )
    } else {
      await createUserMutation.mutateAsync(data, {
        onSuccess: () => {
          showCreatedToast('User')
          router.push('/users')
        },
        onError: (error) => {
          showErrorToast(error.message)
        },
      })
    }
  } catch (error) {
    handleInvalidForm(error, node)
  }
}

const fields = {
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  country: '',
  affiliation: '',
  profile_link: '',
  role_id: '',
  journal_id: '',
}

const formData = computed(() =>
  isEdit.value && existingUser.value ? { ...fields, ...existingUser.value } : { ...fields },
)

// Transform for FormKit
const roleOptions = computed(
  () =>
    roles.data.value?.map((role) => ({
      value: role.id,
      label: role.name,
    })) ?? [],
)

const journalOptions = computed(
  () =>
    journals.value?.data?.map((journal) => ({
      value: journal.id,
      label: journal.name,
    })) ?? [],
)
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h1 class="h3 mb-0 text-middle">
              {{ isEdit ? 'Edit User' : 'Add New User' }}
            </h1>
            <AllButton path="/users" label="All Users" />
          </div>
        </div>
        <div class="card-body">
          <!-- Loading State for Edit Mode -->
          <FormSkeleton v-if="isEdit && isFetchingUser" />

          <div class="row" v-else>
            <FormKit
              type="form"
              :value="formData"
              :submit-label="isEdit ? 'Update User' : 'Add User'"
              :disabled="currentMutation.isPending.value || (isEdit && isFetchingUser)"
              @submit="handleSubmit"
            >
              <!-- <FormKit type="form" submit-label="Add User" @submit="handleSubmit"> -->
              <div class="row">
                <div class="col">
                  <FormKit
                    label="Name"
                    name="name"
                    placeholder="e.g. Jane Doe"
                    validation="required|length:3,255"
                    :validation-messages="{
                      required: 'Name is required',
                      length: 'Name must be between 3 and 255 characters',
                    }"
                  />
                </div>
                <div class="col">
                  <FormKit
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="e.g. jane@example.com"
                    validation="required|email"
                    :validation-messages="{
                      required: 'Email is required',
                      email: 'Please enter a valid email address',
                    }"
                  />
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <FormKit
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="Enter a secure password"
                    :validation="isEdit ? '' : 'required|length:6,255'"
                    :validation-messages="{
                      required: 'Password is required',
                      length: 'Password must be at least 6 characters',
                    }"
                  />
                </div>
                <div class="col">
                  <FormKit
                    label="Confirm Password"
                    name="password_confirmation"
                    type="password"
                    placeholder="Enter a secure password"
                    :validation="isEdit ? '' : 'required|length:6,255|confirm:password'"
                    :validation-messages="{
                      required: 'Password confirmation is required',
                      length: 'Password must be at least 6 characters',
                      confirm: 'Passwords do not match',
                    }"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <FormKit
                    label="Role"
                    name="role_id"
                    type="select"
                    :options="roleOptions"
                    placeholder="Select a role"
                    :disabled="roles.isLoading.value"
                  />
                </div>
                <div class="col">
                  <FormKit
                    label="Journal"
                    name="journal_id"
                    type="select"
                    :options="journalOptions"
                    placeholder="Select a journal"
                    :disabled="isJournalLoading"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <FormKit label="Country" name="country" placeholder="e.g. United States" />
                </div>
                <div class="col">
                  <FormKit
                    label="Affiliation"
                    name="affiliation"
                    placeholder="e.g. University of Somewhere"
                  />
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <FormKit
                    label="Profile Link"
                    name="profile_link"
                    type="url"
                    placeholder="e.g. https://linkedin.com/in/jane"
                    validation="url"
                    :validation-messages="{ url: 'Please enter a valid URL' }"
                  />
                </div>
              </div>

              <div class="row"></div>
            </FormKit>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
