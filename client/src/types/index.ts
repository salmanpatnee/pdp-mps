export interface Role {
  id: number
  name: string
}
export interface User {
  id: number
  role_id: number
  journal_id: number
  role?: Role
  journal?: Journal
  name: string
  email: string
  country?: string
  affiliation?: string
  profile_link?: string
  created_at: Date
}

export type UserFormPayload = Omit<User, 'id' | 'role' | 'journal' | 'created_at'> & {
  password?: string
  password_confirmation?: string
}
export interface LoginFormPayload {
  email: string
  password: string
}

export interface RegisterFormPayload extends LoginFormPayload {
  password_confirmation: string
}

export interface CustomError extends Error {
  customCode?: number
}

export interface ErrorResponse {
  message: string
  errors: Record<string, string[]>
}
export interface Journal {
  id: number
  name: string
  issn?: string
  eissn?: string
  abbreviation?: string
  description?: string
  publisher?: string
  email?: string
  website_url?: string
  is_active: boolean
}

export interface ArticleType {
  id: number
  name: string
}

export type JournalFormPayload = Omit<Journal, 'id'>

export type File = {
  id: number
  manuscript_id: number
  uploaded_by: number
  file_name?: string
  file_path?: string
  file_type?: string
  file_extension?: string
  uploader?: string
  created_at?: Date
  updated_at: Date
}

export interface ManuscriptCoAuthor {
  id?: number; // Optional for new contributors
  manuscript_id?: number; // Optional for new contributors
  user_id?: number | null;
  name?: string;
  email?: string;
  affiliation?: string;
  country?: string;
  is_principal: boolean;
  order?: number;
  user?: User; // To load user details if user_id is present
}

export interface Manuscript {
  id: number
  reference_no: string
  journal_id: number
  author_id?: number
  article_type_id?: number
  journal: Journal
  coAuthors: ManuscriptCoAuthor[]
  article_type: ArticleType
  submission_type: 'manuscript' | 'proposed_abstract' | 'thematic_issue'
  title: string
  abstract: string
  keywords: string
  status:
    | 'Awaiting Editorial Approval'
    | 'submitted'
    | 'under_review'
    | 'revision'
    | 'accepted'
    | 'rejected'
  copyright: Copyright
  files: File[]
  created_at: Date
  updated_at: Date
}

interface Copyright {
  is_corporate_interest?: 'Yes' | 'No'
  has_human_subjects?: 'Yes' | 'No'
  has_animal_subjects?: 'Yes' | 'No'
  is_conflict_interest?: 'Yes' | 'No'
  has_us_government_author?: 'Yes' | 'No'
  used_ai_technology?: 'Yes' | 'No'
}
export interface ManuscriptFormPayload {
  journal_id: number
  article_type_id?: number
  article_type: ArticleType
  submission_type: 'manuscript' | 'proposed_abstract' | 'thematic_issue'
  title: string
  abstract: string
  keywords: string
  status: 'draft' | 'submitted' | 'under_review' | 'revision' | 'accepted' | 'rejected'
  coAuthors: ManuscriptCoAuthor[]
  copyright?: Copyright
  is_authorship_confirmed?: boolean
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  first_page_url: string | null
  from: number
  last_page: number
  last_page_url: string | null
  links: {
    url: string | null
    label: string
    active: boolean
  }[]
  next_page_url: string | null
  path: string
  per_page: number
  prev_page_url: string | null
  to: number
  total: number
}

export interface APIResponse<T> {
  message: string
  data: T
  status: number
}

export type ToastType = 'success' | 'error' | 'info' | 'warning'
