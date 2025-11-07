<script setup lang="ts">
import type { JournalFormPayload, ManuscriptFormPayload } from '@/types'
import { serialNo } from '@/utils'
import { VueDraggableNext as draggable } from 'vue-draggable-next'
import { Icon } from '@iconify/vue'
import SingleFileUploader from '@/components/SingleFileUploader.vue' // New import
definePage({
  meta: {
    requiresAuth: true,
  },
})
// import ManuscriptFileUploader from './ManuscriptFileUploader.vue'

const fileUploader = ref(null)
const router = useRouter()
const route = useRoute('/manuscripts/form')

const articleTypes = useArticleTypes()

const { fetchJournals } = useJournals()
const { data: journals, isLoading: isJournalLoading } = fetchJournals()

const { fetchUsers, searchAuthors, createUser } = useUsers()
const { data: users, isLoading: isUserLoading } = fetchUsers()

const { createManuscript, updateManuscript, fetchManuscript, downloadFile } = useManuscripts()

const createManuscriptMutation = createManuscript()
const updateUserMutation = updateManuscript()
const createUserMutation = createUser()

// Check if we're in edit mode
const isEditMode = computed(() => !!route.query.id)
const manuscriptId = computed(() => (route.query.id ? Number(route.query.id) : null))

// Fetch manuscript data for edit mode
const { data: manuscript, isLoading: isManuscriptLoading } = fetchManuscript(manuscriptId)

const journalOptions = computed(
  () =>
    journals.value?.data?.map((journal) => ({
      value: journal.id,
      label: journal.name,
    })) ?? [],
)

const articleTypeOptions = computed(
  () =>
    articleTypes.data.value?.map((articleType) => ({
      value: articleType.id,
      label: articleType.name,
      id: articleType.id,
    })) ?? [],
)

const nextTempId = ref(-1)
const coAuthors = ref<ManuscriptCoAuthor[]>([])

const authorEmail = ref('')
const isSearchingAuthor = ref(false)
const authorNotFound = ref(false)
const authorAlreadyExists = ref(false)
const showNewAuthorForm = ref(false)
const newAuthor = ref<Partial<User>>({
  name: '',
  email: '',
  country: '',
  affiliation: '',
})

async function handleAuthorSearch() {
  if (!authorEmail.value) {
    showErrorToast('Author email is required to search.')
    return
  }
  isSearchingAuthor.value = true
  authorNotFound.value = false
  showNewAuthorForm.value = false
  authorAlreadyExists.value = false

  try {
    const coAuthor = await searchAuthors(authorEmail.value)
    console.log({ coAuthor })

    if (coAuthor && coAuthor.email) {
      if (coAuthors.value.some((c) => c.email === coAuthor.email)) {
        authorAlreadyExists.value = true
      } else {
        const nameParts = coAuthor.name ? coAuthor.name.split(' ') : [coAuthor.email, '']
        const fullname = nameParts[0]

        coAuthors.value.push({
          id: coAuthor.id,
          user_id: null, // No longer linking to system users
          name: fullname,
          email: coAuthor.email,
          affiliation: coAuthor.affiliation,
          country: coAuthor.country,
          is_principal: false,
          order: coAuthors.value.length + 1,
        })

        showSuccessToast('Author added successfully.')
        authorEmail.value = ''
      }
    } else {
      authorNotFound.value = true
      newAuthor.value.email = authorEmail.value
    }
  } catch (error) {
    console.error('Author search failed:', error)
    showErrorToast('Failed to search for author.')
  } finally {
    isSearchingAuthor.value = false
  }
}

async function handleCreateAuthor() {
  const { email, name, country, affiliation } = newAuthor.value

  // Validation for required fields
  if (!name || !email || !country || !affiliation) {
    showErrorToast(
      'Please fill all required fields for the new author: First Name, Last Name, Email, Country, and Affiliation.',
    )
    return
  }

  // Check for duplicate email
  if (coAuthors.value.some((c) => c.email === email)) {
    showErrorToast('A contributor with this email already exists in the list.')
    return
  }

  // Add the new contributor as a guest
  coAuthors.value.push({
    id: nextTempId.value--, // Assign a temporary negative ID
    user_id: null,
    name,
    email,
    affiliation,
    country,
    is_principal: false,
    order: coAuthors.value.length + 1,
  })

  // Reset form state
  showNewAuthorForm.value = false
  authorNotFound.value = false
  authorEmail.value = ''
  newAuthor.value = { name: '', email: '', country: '', affiliation: '' }
}

function updateCoAuthorOrder() {
  coAuthors.value.forEach((c, index) => {
    c.order = index + 1
  })
}

function removeCoAuthor(coAuthorId: number) {
  coAuthors.value = coAuthors.value.filter((c) => c.id !== coAuthorId)
}

function togglePrincipalCoAuthor(coAuthorId: number, value: boolean) {
  const entry = coAuthors.value.find((c) => c.id === coAuthorId)
  if (!entry) return
  if (value) {
    // Ensure only one principal at a time
    coAuthors.value.forEach((c) => (c.is_principal = false))
    entry.is_principal = true
  } else {
    entry.is_principal = false
  }
}

// Helper function to build FormData for nested objects and arrays
const buildFormData = (formData: FormData, data: any, parentKey?: string) => {
  if (data && typeof data === 'object' && !(data instanceof File)) {
    Object.keys(data).forEach((key) => {
      const value = data[key]
      const formKey = parentKey ? `${parentKey}[${key}]` : key

      if (value instanceof File) {
        // This should be hit for copyright files
        formData.append(formKey, value)
      } else if (Array.isArray(value)) {
        value.forEach((item, index) => {
          buildFormData(formData, item, `${formKey}[${index}]`)
        })
      } else if (typeof value === 'object' && value !== null) {
        buildFormData(formData, value, formKey)
      } else if (typeof value === 'boolean') {
        formData.append(formKey, value ? '1' : '0')
      } else if (value !== null && value !== undefined) {
        formData.append(formKey, value)
      }
    })
  } else {
    const formKey = parentKey || ''
    if (data instanceof File) {
      // This handles top-level File objects
      formData.append(formKey, data)
    } else if (typeof data === 'boolean') {
      formData.append(formKey, data ? '1' : '0')
    } else if (data !== null && data !== undefined) {
      formData.append(formKey, data)
    }
  }
}

const userById = computed(() => {
  const map = new Map<number, User>()
  if (users.value?.data) {
    users.value.data.forEach((u) => map.set(u.id, u))
  }
  return map
})



const isCoAuthorsStepValid = computed(() => {
  return coAuthors.value.length > 0 && coAuthors.value.some((c) => c.is_principal)
})

const authorCols = ['S.No.', 'Full Name', 'Email', 'Affiliation', 'Principal Author']

const formData = ref<
  Partial<ManuscriptFormPayload> & {
    copyright: Record<string, unknown> & {
      // Enhance copyright type
      human_subjects_approval_file?: string | null
      animal_subjects_approval_file?: string | null
      conflict_of_interest_details?: string
      us_government_permission_file?: string | null
      ai_usage_details?: string
    }
    is_authorship_confirmed: boolean
    is_information_accurate: boolean // New field
  }
>({
  journal_id: undefined,
  article_type_id: undefined,
  submission_type: undefined,
  keywords: '',
  title: '',
  abstract: '',
  coAuthors: [],
  copyright: {
    is_corporate_interest: 'No',
    has_human_subjects: 'No',
    human_subjects_approval_file: null,
    has_animal_subjects: 'No',
    animal_subjects_approval_file: null,
    is_conflict_interest: 'No',
    conflict_of_interest_details: '',
    has_us_government_author: 'No',
    us_government_permission_file: null,
    used_ai_technology: 'No',
    ai_usage_details: '',
  },
    is_authorship_confirmed: false,
    is_information_accurate: false, // Initialize new field
  })
  
  // Populate form data when manuscript is loaded (edit mode)
  watch(
    manuscript,
    (manuscriptData) => {
      if (manuscriptData && isEditMode.value) {
        formData.value = {
          journal_id: manuscriptData.journal_id,
          article_type_id: manuscriptData.article_type_id,
          submission_type: manuscriptData.submission_type,
          keywords: manuscriptData.keywords,
          title: manuscriptData.title,
          abstract: manuscriptData.abstract,
                  coAuthors: manuscriptData.coAuthors.map((c) => ({
                    id: c.id,
                    first_name: c.first_name,
                    last_name: c.last_name,
                    email: c.email,
                    affiliation: c.affiliation,
                    country: c.country,
                    is_principal: c.is_principal,
                  })),          copyright: {
            is_corporate_interest: manuscriptData.copyright?.is_corporate_interest ? 'Yes' : 'No',
            has_human_subjects: manuscriptData.copyright?.has_human_subjects ? 'Yes' : 'No',
            has_animal_subjects: manuscriptData.copyright?.has_animal_subjects ? 'Yes' : 'No',
            is_conflict_interest: manuscriptData.copyright?.is_conflict_interest ? 'Yes' : 'No',
            has_us_government_author: manuscriptData.copyright?.has_us_government_author
              ? 'Yes'
              : 'No',
            used_ai_technology: manuscriptData.copyright?.used_ai_technology ? 'Yes' : 'No',
          },
        }
        // Set coAuthors with principal author information
        coAuthors.value = manuscriptData.coAuthors.map((c) => ({
          id: c.id,
          first_name: c.first_name,
          last_name: c.last_name,
          email: c.email,
          affiliation: c.affiliation,
          country: c.country,
          is_principal: c.is_principal,
        }))
        console.log('Setting coAuthors:', coAuthors.value)
        // Set existing files for the file uploader
        if (manuscriptData.files && manuscriptData.files.length > 0) {
          // Populate the file uploader with existing files
          nextTick(() => {
            const uploaderComponent = fileUploader.value as unknown as {
              setExistingFiles?: (files: unknown[]) => void
              existingFiles?: unknown[]
            }
            if (uploaderComponent) {
              if (uploaderComponent.setExistingFiles) {
                uploaderComponent.setExistingFiles(manuscriptData.files)
              } else if (uploaderComponent.existingFiles !== undefined) {
                uploaderComponent.existingFiles = manuscriptData.files
              }
            }
          })
        }
      }
    },
    { immediate: true },
  )

const handleSubmit = async (data: JournalFormPayload, node?: FormKitNode) => {
  const raw_manuscript_files = (fileUploader?.value as unknown as { uploadedFiles?: unknown[] })
    ?.uploadedFiles

  // Transform manuscript_files into the format expected by the backend
  const manuscript_files_for_backend =
    raw_manuscript_files?.map((fileItem: any) => ({
      file_name: fileItem.name,
      file_path: fileItem.file?.path || '', // Assuming file.path exists for temp files
    })) || []

  const payload = {
    journal_id: formData.value.journal_id!,
    article_type_id: formData.value.article_type_id,
    article_type: {
      id: formData.value.article_type_id!,
      name:
        articleTypeOptions.value.find((at) => at.value === formData.value.article_type_id)?.label ||
        '',
    },
    title: formData.value.title!,
    abstract: formData.value.abstract!,
    keywords: formData.value.keywords!,
    submission_type: formData.value.submission_type!,
    status: formData.value.status || 'Awaiting Editorial Approval',
    coAuthors: coAuthors.value.map((c) => ({
      name: c.name,
      email: c.email,
      affiliation: c.affiliation,
      country: c.country,
      is_principal: c.is_principal,
      order: c.order,
    })),
    manuscript_files: manuscript_files_for_backend, // Use the transformed array
    copyright: formData.value.copyright,
    is_authorship_confirmed: formData.value.is_authorship_confirmed,
  }

  const finalFormData = new FormData()
  buildFormData(finalFormData, payload)

  try {
    if (isEditMode.value && manuscriptId.value) {
      // Update existing manuscript
      await updateManuscriptMutation.mutateAsync(
        { id: manuscriptId.value, data: finalFormData }, // Pass FormData
        {
          onSuccess: () => {
            showUpdatedToast('Manuscript')
            router.push('/manuscripts')
          },
          onError: (error) => {
            showErrorToast(error.message)
          },
        },
      )
    } else {
      // Create new manuscript
      
      await createManuscriptMutation.mutateAsync(finalFormData, {
        // Pass FormData
        onSuccess: () => {
          showCreatedToast('Manuscript')
          router.push('/manuscripts')
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

const handleDownload = async (fileId: number, fileName: string) => {
  try {
    await downloadFile(fileId, fileName)
  } catch (error) {
    console.error('Download failed:', error)
  }
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h1 class="h3 mb-0 text-middle">
              {{ isEditMode ? 'Edit Manuscript' : 'Add New Manuscript' }}
            </h1>
            <AllButton path="/manuscripts" label="All Manuscripts" />
          </div>
        </div>
        <div class="card-body">
          <!-- Loading State for Edit Mode -->
          <div v-if="isEditMode && isManuscriptLoading" class="row">
            <div class="col">
              <FormSkeleton />
            </div>
            <div class="col">
              <FormSkeleton />
            </div>
          </div>

          <div v-else class="row">
            <FormKit type="form" :actions="false" @submit="handleSubmit">
              <FormKit type="multi-step" tab-style="progress" :allow-incomplete="false">

                

                <FormKit type="step" name="submissionDetails">
                  <div class="row">
                    <div class="col">
                      <FormKit
                        label="Journal"
                        name="journal_id"
                        v-model="formData.journal_id"
                        type="select"
                        :options="journalOptions"
                        placeholder="Select Journal"
                        :disabled="isJournalLoading"
                        validation="required"
                      />
                    </div>
                    <div class="col">
                      <FormKit
                        label="Article Type"
                        name="article_type_id"
                        v-model="formData.article_type_id"
                        type="select"
                        :options="articleTypeOptions"
                        placeholder="Select Article Type"
                        :disabled="articleTypes.isLoading.value"
                        validation="required"
                      />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <FormKit
                        label="Submission Type"
                        name="submission_type"
                        v-model="formData.submission_type"
                        type="select"
                        :options="{
                          manuscript: 'Manuscript',
                          proposed_abstract: 'Proposed Abstract',
                          thematic_issue: 'Thematic Issue',
                        }"
                        placeholder="Select Submission Type"
                        :disabled="articleTypes.isLoading.value"
                        validation="required"
                      />
                    </div>
                    <div class="col">
                      <FormKit
                        label="Title"
                        name="title"
                        v-model="formData.title"
                        placeholder="e.g. AI-Powered Drug Discovery"
                        validation="required"
                      />
                    </div>
                  </div>
                  <!-- <div class="row">
                    <div class="col">
                      <FormKit
                        label="Keywords"
                        name="keywords"
                        v-model="formData.keywords"
                        placeholder="e.g. science, food"
                        help="Add comma (,) seperated keywords"
                      />
                    </div>
                    <div class="col"></div>
                  </div> -->
                  <div class="row">
                    <div class="col-12">
                      <FormKit
                        label="Abstract"
                        name="abstract"
                        v-model="formData.abstract"
                        type="textarea"
                        placeholder="e.g. This study examines the role of AI tools in manuscript..."
                        style="width: 100%"
                        validation="required"
                      />
                    </div>
                  </div>
                </FormKit>
                
                <FormKit type="step" name="coAuthors">
                  <div class="row">
                    <div class="col-md-6">
                      <FormKit
                        id="author_search_email"
                        type="email"
                        label="Author Email"
                        v-model="authorEmail"
                        placeholder="Enter author's email to search"
                        :classes="{
                          outer: 'mb-4',
                        }"
                        validation="email"
                      />
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                      <button
                        @click.prevent="handleAuthorSearch"
                        class="btn btn-primary"
                        :disabled="isSearchingAuthor"
                      >
                        <span
                          v-if="isSearchingAuthor"
                          class="spinner-border spinner-border-sm"
                          role="status"
                          aria-hidden="true"
                        ></span>
                        {{ isSearchingAuthor ? 'Searching...' : 'Search' }}
                      </button>
                    </div>
                  </div>

                  <div v-if="authorAlreadyExists" class="alert alert-info my-3">
                    This author has already been added to the list.
                  </div>

                  <div v-if="authorNotFound">
                    <p>Author not found. Would you like to create a new one?</p>
                    <button
                      @click.prevent="showNewAuthorForm = true"
                      class="btn btn-info text-light"
                    >
                      Create New Author
                    </button>
                  </div>

                  <div v-if="showNewAuthorForm" class="card my-3">
                    <div class="card-header">
                      <h5 class="card-title">Create New Author</h5>
                    </div>

                    <div class="card-body">
                      <div>
                        <div class="row">
                          <div class="col">
                            <FormKit
                              type="text"
                              label="First Name"
                              v-model="newAuthor.name"
                              validation="required"
                            />
                          </div>
                          <div class="col">
                           <FormKit
                              type="email"
                              label="Email"
                              v-model="newAuthor.email"
                              validation="required|email"
                            />
                          </div>
                        </div>
                        <div class="row">
                          <div class="col">
                            <FormKit
                              type="text"
                              label="Country"
                              v-model="newAuthor.country"
                              validation="required"
                            />
                          </div>
                          <div class="col">
                             <FormKit
                              type="text"
                              label="Affiliation"
                              v-model="newAuthor.affiliation"
                              validation="required"
                            />
                          </div>
                        </div>
                        <div class="row">
                          <div class="col">
                           
                          </div>
                          <div class="col">
                            <button
                              @click.prevent="handleCreateAuthor"
                              class="btn btn-primary"
                              :disabled="createUserMutation.isPending.value"
                            >
                              {{
                                createUserMutation.isPending.value
                                  ? 'Creating...'
                                  : 'Create and Add Author'
                              }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <table class="table mt-2" role="table">
                    <TableHead>
                      <TableHeaderCell
                        v-for="(col, index) in ['Order', ...authorCols, 'Actions']"
                        :key="index"
                        :text="col"
                      />
                    </TableHead>
                    <draggable
                      v-model="coAuthors"
                      tag="tbody"
                      item-key="id"
                      @end="updateCoAuthorOrder"
                    >
                      <template v-if="coAuthors.length">
                        <tr
                          v-for="(c, index) in coAuthors"
                          :key="c.id"
                          class="align-middle draggable-item"
                        >
                          <td class="drag-handle" title="Drag to reorder">
                            <Icon icon="mdi:reorder-horizontal" width="24" height="24" />
                          </td>
                          <td>
                            {{ serialNo(c, index) }}
                          </td>
                          <td>
                            <strong>{{ c.name || '—' }}</strong>
                          </td>
                          <td>{{ c.email || '—' }}</td>
                          <td>{{ c.affiliation || 'N/A' }}</td>
                          <td>
                            <FormKit
                              type="checkbox"
                              :modelValue="c.is_principal"
                              @update:modelValue="(val) => togglePrincipalCoAuthor(c.id!, !!val)"
                              :classes="{
                                outer: 'mb-0',
                              }"
                            />
                          </td>
                          <td>
                            <button
                              @click.prevent="removeCoAuthor(c.id!)"
                              class="btn btn-sm btn-danger"
                            >
                              Remove
                            </button>
                          </td>
                        </tr>
                      </template>
                      <template v-else>
                        <tr class="text-center">
                          <td colspan="9">No authors added yet.</td>
                        </tr>
                      </template>
                    </draggable>
                  </table>
                  <FormKit
                    type="checkbox"
                    label="Please ensure that all corresponding and co-author names, affiliations, and authorship details are complete and accurate, as no changes or additions will be allowed after submission. Also confirm that the author information matches the manuscript."
                    name="is_authorship_confirmed"
                    v-model="formData.is_authorship_confirmed"
                    validation="required|accepted"
                    :classes="{
                      outer: 'my-4',
                      label: 'form-check-label fw-semibold',
                      input: 'form-check-input',
                    }"
                  />
                  <template #stepNext="{ handlers }">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                      <FormKit
                        type="button"
                        label="Next Step"
                        @click="handlers.next"
                        :disabled="!isCoAuthorsStepValid"
                      />
                    </div>
                  </template>

                  <p v-if="!isCoAuthorsStepValid" class="text-danger text-center">
                    You must add at least one author and select one as Principal Author.
                  </p>
                </FormKit>

                <FormKit type="step" name="fileUploading">
                  <Uploader
                    ref="fileUploader"
                    accepted-files=".doc,.docx,.pdf,.txt"
                    help="Only .doc, .docx, .pdf, and .txt files are accepted."
                  />

                  <!-- Display existing files in edit mode -->
                  <div v-if="isEditMode && manuscript?.files?.length" class="mt-4">
                    <h6 class="mb-3">Existing Files:</h6>
                    <div class="table-responsive">
                      <table class="table table-sm">
                        <thead>
                          <tr>
                            <th>File Name</th>
                            <th>File Type</th>
                            <th>Extension</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="file in manuscript.files" :key="file.id">
                            <td>{{ file.file_name }}</td>
                            <td>{{ file.file_type }}</td>
                            <td>{{ file.file_extension || 'N/A' }}</td>
                            <td>
                              <button
                                @click="handleDownload(file.id, file.file_name || 'download')"
                                class="btn btn-sm btn-outline-primary"
                              >
                                <i class="fas fa-download"></i> Download
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </FormKit>
                
                <FormKit type="step" name="copyright">
                  <FormKit
                    label="Is the Work likely to be of particular interest to pharmaceutical or biotechnology companies or any other corporate entities?"
                    type="radio"
                    :options="['Yes', 'No']"
                    v-model="formData.copyright.is_corporate_interest"
                    :classes="{
                      options: 'd-flex gap-3',
                      legend: 'mb-2',
                    }"
                  />
                  <hr class="my-4" />
                  <h6>DECLARATION OF COMPLIANCE WITH APPLICABLE STANDARDS:</h6>
                  <FormKit
                    label="Does the Work report experiments involving human subjects?"
                    type="radio"
                    :options="['Yes', 'No']"
                    v-model="formData.copyright.has_human_subjects"
                    :classes="{
                      options: 'd-flex gap-3',
                      legend: 'mb-2',
                    }"
                  />
                  <SingleFileUploader
                    v-if="formData.copyright.has_human_subjects === 'Yes'"
                    label="Ethical Approval Document (Human Subjects)"
                    upload-url="/temp-upload"
                    dropzone-id="human-subjects-dropzone"
                    @file-uploaded="
                      (file) => (formData.copyright.human_subjects_approval_file = file.file_path)
                    "
                    @file-removed="formData.copyright.human_subjects_approval_file = null"
                  />

                  <FormKit
                    label="Does the Work report experiments involving animals?"
                    type="radio"
                    :options="['Yes', 'No']"
                    v-model="formData.copyright.has_animal_subjects"
                    :classes="{
                      options: 'd-flex gap-3 mt-3',
                      legend: 'mb-2',
                    }"
                  />
                  <SingleFileUploader
                    v-if="formData.copyright.has_animal_subjects === 'Yes'"
                    label="Ethical Approval Document (Animal Subjects)"
                    upload-url="/temp-upload"
                    dropzone-id="animal-subjects-dropzone"
                    @file-uploaded="
                      (file) => (formData.copyright.animal_subjects_approval_file = file.file_path)
                    "
                    @file-removed="formData.copyright.animal_subjects_approval_file = null"
                  />
                  <hr class="my-4" />
                  <h6>CONFLICTS OF INTEREST</h6>
                  <FormKit
                    label="Are there any actual, or potential, conflicts of interest?"
                    type="radio"
                    :options="['Yes', 'No']"
                    v-model="formData.copyright.is_conflict_interest"
                    :classes="{
                      options: 'd-flex gap-3',
                      legend: 'mb-2',
                    }"
                  />
                  <FormKit
                    v-if="formData.copyright.is_conflict_interest === 'Yes'"
                    type="textarea"
                    label="Conflict of Interest Details"
                    name="conflict_of_interest_details"
                    v-model="formData.copyright.conflict_of_interest_details"
                    placeholder="Describe the nature of the conflict."
                    validation="required"
                    :validation-messages="{
                      required: 'Details for the conflict of interest are required.',
                    }"
                  />
                  <hr class="my-4" />
                  <h6>US GOVERNMENT EMPLOYEES / INDEPENDENT CONTRACTORS:</h6>
                  <FormKit
                    label="Was any Author a US government employee or independent contractor to the US government when the Work was created?"
                    type="radio"
                    help="(If Yes, the relevant Author/s must each execute and submit to Bentham Science, using Bentham Science’s form letter, the supplemental terms applicable to the Author.)"
                    :options="['Yes', 'No']"
                    v-model="formData.copyright.has_us_government_author"
                    :classes="{
                      options: 'd-flex gap-3',
                      legend: 'mb-2',
                    }"
                  />
                  <SingleFileUploader
                    v-if="formData.copyright.has_us_government_author === 'Yes'"
                    label="U.S. Government Permission Document"
                    upload-url="/temp-upload"
                    dropzone-id="us-government-dropzone"
                    @file-uploaded="
                      (file) => (formData.copyright.us_government_permission_file = file.file_path)
                    "
                    @file-removed="formData.copyright.us_government_permission_file = null"
                  />
                  <hr class="my-4" />
                  <h6>TRANSPARENT REPORTING ON AI AND AI-ASSISTED TECHNOLOGIES:</h6>
                  <FormKit
                    label="Authors are fully responsible for the content of their manuscript, including parts produced with the assistance of an AI tool, and are thus liable for any breach of publication ethics. Was AI technology used in the article?"
                    type="radio"
                    :options="['Yes', 'No']"
                    v-model="formData.copyright.used_ai_technology"
                    :classes="{
                      options: 'd-flex gap-3',
                      legend: 'mb-2',
                    }"
                  />
                  <FormKit
                    v-if="formData.copyright.used_ai_technology === 'Yes'"
                    type="textarea"
                    label="AI Technology Usage Details"
                    name="ai_usage_details"
                    v-model="formData.copyright.ai_usage_details"
                    placeholder="Specify how AI was used (e.g., writing assistance, image generation)."
                    validation="required"
                    :validation-messages="{
                      required: 'Details of AI technology usage are required.',
                    }"
                  />
                </FormKit>
                <FormKit type="step" name="previewAndSubmit">
                  <div class="container my-4">
                    <div class="card border-0 shadow-sm">
                      <div class="card-header bg-light">
                        <h4 class="mb-0 fw-semibold">
                          <i class="bi bi-eye me-2"></i> Preview Manuscript Submission
                        </h4>
                        <small class="text-muted"
                          >Please review all details before final submission.</small
                        >
                      </div>

                      <div class="card-body">
                        <!-- Journal -->
                        <div class="mb-4">
                          <h6 class="text-muted mb-1">Journal</h6>
                          <p class="mb-0 fs-6 fw-medium">
                            {{
                              journals?.data.find((j) => j.id === formData.journal_id)?.name || '—'
                            }}
                          </p>
                        </div>

                        <!-- Article Type -->
                        <div class="mb-4">
                          <h6 class="text-muted mb-1">Article Type</h6>
                          <p class="mb-0 fs-6 fw-medium">
                            {{
                              articleTypeOptions.find((at) => at.id === formData.article_type_id)
                                ?.label || '—'
                            }}
                          </p>
                        </div>

                        <!-- Submission Type -->
                        <div class="mb-4">
                          <h6 class="text-muted mb-1">Submission Type</h6>
                          <p class="mb-0 fs-6 fw-medium">
                            {{
                              formData.submission_type
                                ? formData.submission_type
                                    .replace(/_/g, ' ')
                                    .replace(/\b\w/g, (l) => l.toUpperCase())
                                : '—'
                            }}
                          </p>
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                          <h6 class="text-muted mb-1">Title</h6>
                          <p class="mb-0 fs-6 fw-medium">{{ formData.title || '—' }}</p>
                        </div>

                        <!-- Abstract -->
                        <div class="mb-4">
                          <h6 class="text-muted mb-1">Abstract</h6>
                          <p class="text-secondary mb-0">{{ formData.abstract || '—' }}</p>
                        </div>

                        <!-- Keywords -->
                        <!-- <div class="mb-4">
                          <h6 class="text-muted mb-1">Keywords</h6>
                          <p class="text-secondary mb-0">{{ formData.keywords || '—' }}</p>
                        </div> -->

                        <!-- Co-Authors -->
                        <div class="mb-4">
                          <h6 class="text-muted mb-1">Co-Authors</h6>
                          <ul class="list-group list-group-flush">
                            <li
                              v-for="c in coAuthors"
                              :key="c.id"
                              class="list-group-item px-0 border-0"
                            >
                              <i class="bi bi-person me-2 text-secondary"></i>
                              {{ c.name || 'Unknown' }}
                              <span v-if="c.is_principal" class="badge bg-success ms-2"
                                >Principal</span
                              >
                            </li>
                            <li
                              v-if="!coAuthors || !coAuthors.length"
                              class="text-muted ps-2"
                            >
                              None
                            </li>
                          </ul>
                        </div>

                        <!-- Copyright & Declarations -->
                        <div class="mb-4">
                          <h6 class="text-muted mb-1">Copyright & Declarations</h6>
                          <div class="table-responsive">
                            <table class="table table-sm mb-0">
                              <tbody>
                                <tr>
                                  <td>
                                    <strong>Corporate Interest Declaration</strong><br />
                                    <span class="d-block mb-2">
                                      Is the Work likely to be of particular interest to
                                      pharmaceutical or biotechnology companies or any other
                                      corporate entities?
                                    </span>
                                  </td>
                                  <td>
                                    <span v-if="formData.copyright?.is_corporate_interest">
                                      {{
                                        formData.copyright.is_corporate_interest === 'Yes'
                                          ? 'Yes'
                                          : 'No'
                                      }}
                                    </span>
                                    <span v-else>—</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="2" class="pt-3"></td>
                                </tr>
                                <tr>
                                  <td>
                                    <strong>Human Subjects Declaration</strong><br />
                                    <span class="d-block mb-2">
                                      Does this research include human subjects?
                                    </span>
                                  </td>
                                  <td>
                                    <span v-if="formData.copyright?.has_human_subjects">
                                      {{
                                        formData.copyright.has_human_subjects === 'Yes'
                                          ? 'Yes'
                                          : 'No'
                                      }}
                                    </span>
                                    <span v-else>—</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="2" class="pt-3"></td>
                                </tr>
                                <tr>
                                  <td>
                                    <strong>Animal Subjects Declaration</strong><br />
                                    <span class="d-block mb-2">
                                      Does this research include animal subjects?
                                    </span>
                                  </td>
                                  <td>
                                    <span v-if="formData.copyright?.has_animal_subjects">
                                      {{
                                        formData.copyright.has_animal_subjects === 'Yes'
                                          ? 'Yes'
                                          : 'No'
                                      }}
                                    </span>
                                    <span v-else>—</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="2" class="pt-3"></td>
                                </tr>
                                <tr>
                                  <td>
                                    <strong>Conflict of Interest</strong><br />
                                    <span class="d-block mb-2">
                                      Are there any conflicts of interest to declare?
                                    </span>
                                  </td>
                                  <td>
                                    <span v-if="formData.copyright?.is_conflict_interest">
                                      {{
                                        formData.copyright.is_conflict_interest === 'Yes'
                                          ? 'Yes'
                                          : 'No'
                                      }}
                                    </span>
                                    <span v-else>—</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="2" class="pt-3"></td>
                                </tr>
                                <tr>
                                  <td>
                                    <strong>US Government Author</strong><br />
                                    <span class="d-block mb-2">
                                      Is there a US government author associated with this work?
                                    </span>
                                  </td>
                                  <td>
                                    <span v-if="formData.copyright?.has_us_government_author">
                                      {{
                                        formData.copyright.has_us_government_author === 'Yes'
                                          ? 'Yes'
                                          : 'No'
                                      }}
                                    </span>
                                    <span v-else>—</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="2" class="pt-3"></td>
                                </tr>
                                <tr>
                                  <td>
                                    <strong>Use of AI Technology</strong><br />
                                    <span class="d-block mb-2">
                                      Was AI technology used during the preparation of this work?
                                    </span>
                                  </td>
                                  <td>
                                    <span v-if="formData.copyright?.used_ai_technology">
                                      {{
                                        formData.copyright.used_ai_technology === 'Yes'
                                          ? 'Yes'
                                          : 'No'
                                      }}
                                    </span>
                                    <span v-else>—</span>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <!-- Uploaded Files -->
                        <!-- <div class="mb-4">
                          <h6 class="text-muted mb-1">Uploaded Files</h6>
                          <ul class="list-group list-group-flush">
                            <li
                              v-for="f in uploadedFiles"
                              :key="f.file_name"
                              class="list-group-item px-0 border-0"
                            >
                              <i class="bi bi-file-earmark-text me-2 text-secondary"></i>
                              {{ f.file_name }}
                            </li>
                            <li
                              v-if="!uploadedFiles || !uploadedFiles.length"
                              class="text-muted ps-2"
                            >
                              No files uploaded.
                            </li>
                          </ul>
                        </div> -->
                      </div>
                    </div>
                  </div>
                  <FormKit
                    type="checkbox"
                    label="I hereby confirm that the information provided above is accurate and complete, and I accept full responsibility for its authenticity."
                    name="is_information_accurate"
                    v-model="formData.is_information_accurate"
                    validation="required|accepted"
                    :classes="{
                      outer: 'my-4',
                      label: 'form-check-label',
                      input: 'form-check-input',
                    }"
                  />
                  <template #stepNext>
                    <FormKit type="submit" />
                  </template>
                </FormKit>
              </FormKit>
            </FormKit>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.draggable-item {
  cursor: grab;
}

.draggable-item.sortable-ghost {
  opacity: 0.5;
}
</style>
