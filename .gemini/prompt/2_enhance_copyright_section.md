### Task: Enhance Copyright Section in Manuscript Submission Form

#### Context
This system is the **Manuscript Processing System (MPS)** built using **Laravel 12** for the backend (API only) and **Vue 3** for the frontend (SPA).  
The copyright information is currently collected during the manuscript submission process through simple yes/no radio buttons.  

We now need to **enhance this section** by introducing conditional logic, file uploads, and validation to align with publishing standards.

---

### Goal
Improve the **copyright details** step in the manuscript submission workflow to make it dynamic and context-aware.

---

### Enhancement Requirements

#### 1️⃣ Human or Animal Subjects — Require Approval Document
- When the participant selects **“Yes”** for either *Human Subjects* or *Animal Subjects*:
  - Display a **file upload field** (PDF, DOC, or image) for submitting relevant ethical approval documents.
  - The file becomes **mandatory** if “Yes” is selected.

**Example logic (frontend):**
```ts
if (form.has_human_subjects || form.has_animal_subjects) {
  showField('ethical_approval_document');
}
```

**Backend updates:**
- Add optional file fields to `copyrights` table:
  - `human_subjects_approval_path`
  - `animal_subjects_approval_path`
- Validation rule:
  ```php
  'human_subjects_approval' => ['required_if:has_human_subjects,true', 'file', 'mimes:pdf,doc,docx,jpg,png'],
  'animal_subjects_approval' => ['required_if:has_animal_subjects,true', 'file', 'mimes:pdf,doc,docx,jpg,png'],
  ```

---

#### 2️⃣ Conflict of Interest — Require Details
- When “Yes” is selected for **Conflict of Interest**, display a **text box** where the author must describe the nature of the conflict.
- Make this text field **required** when “Yes” is chosen.

**Backend Validation:**
```php
'conflict_interest_details' => ['required_if:is_conflict_interest,true', 'string', 'max:5000'],
```

---

#### 3️⃣ U.S. Government Association — Require Permission File
- If the participant selects “Yes” for **U.S. Government Author**:
  - Display a **file upload field** for permission document.
  - If “Yes” is selected and no file is uploaded, prevent form submission and show a **mandatory warning message**.
  
**Backend Validation:**
```php
'us_government_permission' => ['required_if:has_us_government_author,true', 'file', 'mimes:pdf,doc,docx,jpg,png'],
```

**Frontend Behavior:**
- Show blocking toast or alert:  
  > “Permission document is required for U.S. Government associated manuscripts.”

---

#### 4️⃣ AI Technology Usage — Require Clarification
- If “Yes” is selected for **AI Technology Used**, show a **text box** to specify:
  - How AI was used (e.g., writing assistance, image generation, data analysis).
  - Clarify the role of AI in manuscript preparation.

**Backend Validation:**
```php
'ai_usage_details' => ['required_if:used_ai_technology,true', 'string', 'max:2000'],
```

---

### Frontend Implementation Details

**Files Affected:**
- `src/pages/manuscripts/SubmissionStep5.vue`
- `src/components/manuscripts/CopyrightSection.vue`
- `src/types/ManuscriptFormPayload.ts`
- `src/services/http.ts`

**Actions:**
1. Add conditional rendering for new fields based on “Yes” radio selections.
2. Use FormKit’s `show` / `if` conditions to dynamically display fields.
3. Use Dropzone.js for file uploads (already configured in manuscript submission).
4. Add visual warnings when required conditions are not met.
5. Update submission payload to include new file and text fields.

**Payload Example:**
```ts
{
  has_human_subjects: true,
  human_subjects_approval: File,
  has_animal_subjects: false,
  is_conflict_interest: true,
  conflict_interest_details: 'Reviewer is a collaborator in a related project',
  has_us_government_author: true,
  us_government_permission: File,
  used_ai_technology: true,
  ai_usage_details: 'Used ChatGPT for grammar correction'
}
```

---

### Backend Implementation Details

**Files Affected:**
- `app/Models/Copyright.php`
- `app/Http/Requests/StoreManuscriptRequest.php`
- `app/Services/ManuscriptService.php`
- `database/migrations/xxxx_add_fields_to_copyrights_table.php`
- `app/Http/Resources/CopyrightResource.php`

**Actions:**
1. Update migration to include new nullable file and text fields.  
2. Enhance validation rules in `StoreManuscriptRequest`.  
3. Handle conditional file upload in `ManuscriptService`.  
4. Ensure storage in `/public/copyright_docs/`.  
5. Update resource to return URLs for uploaded documents.

---

### Validation Summary
| Field | Condition | Type | Required |
|--------|------------|------|-----------|
| human_subjects_approval | if `has_human_subjects == true` | file | ✅ |
| animal_subjects_approval | if `has_animal_subjects == true` | file | ✅ |
| conflict_interest_details | if `is_conflict_interest == true` | text | ✅ |
| us_government_permission | if `has_us_government_author == true` | file | ✅ |
| ai_usage_details | if `used_ai_technology == true` | text | ✅ |

---

### Acceptance Criteria
- ✅ Ethical approval upload required for human/animal studies.  
- ✅ Conflict of interest details collected when applicable.  
- ✅ U.S. government permissions enforced with file upload requirement.  
- ✅ AI usage details captured through a conditional text field.  
- ✅ All logic enforced both on frontend (UI) and backend (validation).  
- ✅ Seamless user experience with dynamic form behavior.

---

### Instruction to Gemini
> Implement the described enhancements for the Copyright section of the Manuscript Submission module.  
> Ensure frontend dynamically adapts based on user responses.  
> Add new fields, validations, and file handling in Laravel service.  
> Maintain code modularity and reusability for future compliance additions.

---

**References**
- `docs/author_tutorial.pdf` — Section on manuscript submission form  
- `GEMINI.md` — Manuscript workflow module  
- `StoreManuscriptRequest` and `ManuscriptService` in backend implementation  
