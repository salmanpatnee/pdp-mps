<script setup lang="ts">
definePage({
  meta: {
    requiresAuth: true,
  },
})

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => route.query.id !== undefined)
const journalId = computed(() => (isEdit.value ? Number(route.query.id) : null))

const { createJournal, updateJournal, fetchJournal } = useJournals()
const createJournalMutation = createJournal()
const updateJournalMutation = updateJournal()

// Fetch user data for edit mode
const { data: existingJournal, isPending: isFetchingJournal } = fetchJournal(journalId)

// Use the appropriate mutation based on mode
const currentMutation = computed(() =>
  isEdit.value ? updateJournalMutation : createJournalMutation,
)

const handleSubmit = async (data: JournalFormPayload, node?: FormKitNode) => {
  try {
    if (isEdit.value && journalId.value) {
      await updateJournalMutation.mutateAsync(
        {
          id: journalId.value,
          data: data as Journal,
        },
        {
          onSuccess: () => {
            showUpdatedToast('Journal')
            router.push('/journals')
          },
          onError: (error) => {
            console.log(error)
          },
        },
      )
    } else {
      await createJournalMutation.mutateAsync(data, {
        onSuccess: () => {
          showCreatedToast('Journal')
          router.push('/journals')
        },
        onError: (error) => {
          console.log('Error:', error)
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
  isEdit.value && existingJournal.value ? { ...fields, ...existingJournal.value } : { ...fields },
)
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h1 class="h3 mb-0 text-middle">
              {{ isEdit ? 'Edit Journal' : 'Add New Journal' }}
            </h1>
            <AllButton path="/journals" label="All Journals" />
          </div>
        </div>
        <div class="card-body">
          <!-- Loading State for Edit Mode -->

          <div class="row" v-if="isEdit && isFetchingJournal">
            <div class="col">
              <FormSkeleton />
            </div>
            <div class="col">
              <FormSkeleton />
            </div>
          </div>

          <div class="row">
            <FormKit
              v-if="!isEdit || existingJournal"
              type="form"
              :value="formData"
              :submit-label="isEdit ? 'Update Journal' : 'Add Journal'"
              :disabled="currentMutation.isPending.value || (isEdit && isFetchingJournal)"
              @submit="handleSubmit"
            >
              <div class="row">
                <div class="col">
                  <FormKit
                    label="Name"
                    name="name"
                    placeholder="e.g. Journal of Modern Science"
                    validation="required|length:3,255"
                    :validation-messages="{
                      required: 'Journal name is required',
                      length: 'Journal name must be between 3 and 255 characters',
                    }"
                  />
                </div>
                <div class="col">
                  <FormKit label="Publisher" name="publisher" placeholder="e.g. Springer" />
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <FormKit label="ISSN" name="issn" placeholder="e.g. 1234-5678" />
                </div>
                <div class="col">
                  <FormKit label="EISSN" name="eissn" placeholder="e.g. 8765-4321" />
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <FormKit
                    label="Abbreviation"
                    name="abbreviation"
                    placeholder="e.g. J. Mod. Sci."
                  />
                </div>
                <div class="col">
                  <FormKit
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="e.g. contact@journal.com"
                    validation="email"
                    :validation-messages="{
                      email: 'Please enter a valid email address',
                    }"
                  />
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <FormKit
                    label="Website URL"
                    name="website_url"
                    type="url"
                    placeholder="e.g. https://www.journal.com"
                    validation="url"
                    :validation-messages="{
                      url: 'Please enter a valid URL',
                    }"
                  />
                </div>
                <div class="col">
                  <FormKit label="Active" name="is_active" type="checkbox" />
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <FormKit
                    label="Description"
                    name="description"
                    type="textarea"
                    placeholder="e.g. This journal publishes research in modern science."
                    style="width: 100%"
                  />
                </div>
              </div>
            </FormKit>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
