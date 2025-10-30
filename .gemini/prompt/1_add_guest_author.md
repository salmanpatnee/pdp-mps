### Task: Fix and Enhance Author Creation in Manuscript Submission Using Hybrid Model

#### Context
This system is the **Manuscript Processing System (MPS)** built with **Laravel 12 (API backend)** and **Vue 3 (frontend SPA)**.

- The backend uses **Sanctum** for authentication.
- Manuscript submission currently allows the **corresponding author** to add co-authors.
- Co-authors are selected from the existing `users` table.
- If a co-author does not exist, the frontend allows creating a new one.

#### Current Problem
When a co-author (not in the system) is added, the backend tries to create a new `user` entry, resulting in validation errors:

```
"The journal_id field is required."
"The password field is required."
"The role_id field is required."
```


This happens because the system is treating the inline co-author creation as a full **user registration**, while it should only store lightweight information for a co-author related to that manuscript.

---

### Goal
Implement and enhance a **Hybrid Author Model** with better usability and integrity features:

1. Allow adding co-authors who are not system users (guest authors).
2. Automatically retrieve author details if they already exist in previous manuscripts.
3. Allow reordering of co-authors through drag-and-drop.
4. Require authorship confirmation before final submission.

---

### Recommended Solution (Hybrid Model)
Create a new table named `manuscript_contributors` (or `manuscript_authors`) that:
- Links to `manuscripts` via `manuscript_id`
- Optionally links to `users` via `user_id` (nullable)
- Stores lightweight co-author data when `user_id` is null.

#### Database Structure
```php
$table->id();
$table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
$table->string('first_name');
$table->string('last_name');
$table->string('email')->nullable();
$table->string('affiliation')->nullable();
$table->string('country')->nullable();
$table->boolean('is_principal')->default(false);
$table->unsignedInteger('order')->default(1); // For manual sorting
$table->timestamps();
---

#New Enhancements
🧠 1. Auto-Retrieve Existing Author Info

- When an author is searched and already exists (in manuscript_contributors or users), their info should be auto-populated into the co-author form.

- If not found, allow full manual entry.

# Backend Action:

- Add an endpoint /api/v1/authors/search?query=... that searches both users and manuscript_contributors.

- Return existing author data if found.

# Frontend Action:

- On typing in co-author search, call the endpoint.

- If found, auto-fill details; if not, display manual input fields.


# 2. Drag-and-Drop Co-Author Ordering

- Allow users to reorder co-authors visually before submission.

- Use a library like Vue Draggable
.

- Update the order property dynamically when the list changes.

- Save order to backend when submitting.

# 3. Authorship Confirmation Checkbox
Before final submission, require a checkbox with text:

“Once authorship is submitted, no further changes will be allowed.”

### Rules:
- Add is_authorship_confirmed field to submission payload.
- Validate it as required|boolean|accepted in StoreManuscriptRequest.

### Required Changes

#### 1️⃣ Backend — Laravel
**Files Affected:**
- `app/Models/Manuscript.php`
- `app/Models/ManuscriptContributor.php` (new)
- `app/Services/ManuscriptService.php`
- `app/Http/Resources/ManuscriptResource.php`
- `app/Http/Requests/StoreManuscriptRequest.php`

**Actions:**
1. Create new `ManuscriptContributor` model + migration.
2. Add relationship in `Manuscript`:
   ```php
   public function contributors()
   {
       return $this->hasMany(ManuscriptContributor::class);
   }
   ```
3. Update `StoreManuscriptRequest` validation:
   ```php
   'contributors' => ['nullable', 'array'],
   'contributors.*.user_id' => ['nullable', 'integer', 'exists:users,id'],
   'contributors.*.first_name' => ['required_without:contributors.*.user_id'],
   'contributors.*.last_name' => ['required_without:contributors.*.user_id'],
   'contributors.*.email' => ['nullable', 'email'],
   ```
4. Update `ManuscriptService@store()`:
   - Loop through contributors.
   - If `user_id` exists → associate user.
   - Else → create new contributor record with given data.
5. Update `ManuscriptResource`:
   ```php
   'contributors' => ManuscriptContributorResource::collection($this->whenLoaded('contributors')),
   ```

---

#### 2️⃣ Frontend — Vue 3
**Files Affected:**
- `src/components/manuscripts/CoAuthorForm.vue`
- `src/pages/manuscripts/SubmissionStep2.vue`
- `src/types/ManuscriptFormPayload.ts`

**Actions:**
1. Update co-author form to allow manual entry of:
   - First Name
   - Last Name
   - Email
   - Affiliation
   - Country
2. Update form payload to send:
   ```ts
   contributors: [
     { user_id: 3, is_principal: true },
     { first_name: 'Jane', last_name: 'Doe', email: 'jane@example.com' }
   ]
   ```
3. No password, role, or journal should be required.
4. Handle display and editing of contributors in manuscript detail page.
5. Integrate vue.draggable.next for drag-and-drop.
6. Update payload structure:

```
contributors: [
  { user_id: 3, is_principal: true, order: 1 },
  { first_name: 'Jane', last_name: 'Doe', email: 'jane@example.com', order: 2 }
],
is_authorship_confirmed: true
```
7. Add search and auto-fill logic for author lookup.
8. Add mandatory confirmation checkbox that enables submission only when checked.
---

### Deliverables
✅ New manuscript_contributors table with order column.

✅ Author search endpoint for reusing existing author info.

✅ Updated validation and confirmation rules.

✅ Frontend UI for drag-and-drop co-author ordering.

✅ Confirmation checkbox integrated in submission form.

✅ Updated manuscript resource to return ordered contributors.
---

### References
- `GEMINI.md` → Core Modules → Manuscript Submission
- `docs/author_tutorial.pdf` → Author submission process
- `docs/HandlingEditor_tutorial.pdf` → Editor handling workflow

---

### Acceptance Criteria

- Submitting a manuscript with guest or existing co-authors works seamlessly.
- Co-authors can be reordered visually.
- Submission cannot proceed without authorship confirmation.
- Existing author info is auto-filled when available.
- API returns contributors in their defined order.
- Architecture remains modular and scalable for future user onboarding..

---

**Instruction to Gemini:**
> Use this plan to generate the migration, model, service updates, and validation rules according to the Hybrid Model described above.
> Implement backend logic first, then update the corresponding frontend form and payload structure.
> Ensure consistency with the Manuscript Submission workflow and existing API standards.
