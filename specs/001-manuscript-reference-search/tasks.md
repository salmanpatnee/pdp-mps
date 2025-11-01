# Tasks: Manuscript Search by Reference Number

**Input**: Design documents from `/specs/001-manuscript-reference-search/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md

**Tests**: The examples below include test tasks. Tests are OPTIONAL - only include them if explicitly requested in the feature specification.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Single project**: `src/`, `tests/` at repository root
- **Web app**: `backend/src/`, `frontend/src/`
- **Mobile**: `api/src/`, `ios/src/` or `android/src/`
- Paths shown below assume single project - adjust based on plan.md structure

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [ ] T001 Create project structure per implementation plan
- [ ] T002 Initialize Laravel project with dependencies
- [ ] T003 Initialize Vue 3 project with dependencies
- [ ] T004 Configure linting and formatting tools

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [ ] T005 Setup database schema and migrations framework (Laravel)
- [ ] T006 Configure error handling and logging infrastructure (Laravel)
- [ ] T007 Setup environment configuration management (Laravel)

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Search by Reference Number (Priority: P1) 🎯 MVP

**Goal**: Allow authenticated users to search for manuscripts by their reference number.

**Independent Test**: Enter a known reference number into the search field on the manuscript list page and verify that the correct manuscript is returned. Also, test with partial and non-existent reference numbers.

### Implementation for User Story 1

**Backend (Laravel)**

- [ ] T008 [US1] Modify `ManuscriptController@index` to include search by `reference_no` in `server/app/Http/Controllers/ManuscriptController.php`
- [ ] T009 [US1] Ensure search is case-insensitive for `reference_no` in `server/app/Http/Controllers/ManuscriptController.php`
- [ ] T010 [US1] Implement logic to prioritize `reference_no` search if the term matches a reference number format in `server/app/Http/Controllers/ManuscriptController.php`

**Frontend (Vue 3)**

- [ ] T011 [US1] Update search input field to handle both title and reference number search in `client/src/pages/manuscripts/index.vue`
- [ ] T012 [US1] Modify `useManuscripts` composable to pass search term to backend for both title and reference number in `client/src/composables/useManuscripts.ts`
- [ ] T013 [US1] Display "No results found" message when no manuscripts match the search criteria in `client/src/pages/manuscripts/index.vue`

---

## Phase 4: User Story 2 - Search by Title (Priority: P2)

**Goal**: Maintain existing functionality to search for manuscripts by title.

**Independent Test**: Enter a known manuscript title into the search field on the manuscript list page and verify that the correct manuscript(s) are returned.

### Implementation for User Story 2

**Backend (Laravel)**

- [ ] T014 [US2] Verify `ManuscriptController@index` still supports search by title in `server/app/Http/Controllers/ManuscriptController.php`
- [ ] T015 [US2] Ensure search is case-insensitive for title in `server/app/Http/Controllers/ManuscriptController.php`

**Frontend (Vue 3)**

- [ ] T016 [US2] Verify search input field still functions correctly for title search in `client/src/pages/manuscripts/index.vue`
- [ ] T017 [US2] Verify `useManuscripts` composable still passes title search term to backend in `client/src/composables/useManuscripts.ts`

---

## Phase N: Polish & Cross-Cutting Concerns

- [ ] T018 [P] Add Pest tests for `ManuscriptController@index` covering search by reference number and title in `server/tests/Feature/ManuscriptSearchTest.php`
- [ ] T019 [P] Add Vitest tests for search input and `useManuscripts` composable in `client/tests/unit/`
- [ ] T020 Code cleanup and refactoring in `server/app/Http/Controllers/ManuscriptController.php` and `client/src/pages/manuscripts/index.vue`
- [ ] T021 Performance optimization for search queries in `server/app/Http/Controllers/ManuscriptController.php`
- [ ] T022 Update documentation (if any specific to search)
- [ ] T023 [P] Implement performance monitoring for search endpoints in `server/config/logging.php`
- [ ] T024 [P] Conduct load testing for search functionality and analyze results in `server/tests/Performance/SearchLoadTest.php`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User stories can then proceed in parallel (if staffed)
  - Or sequentially in priority order (P1 → P2 → P3)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after Foundational (Phase 2) - Depends on the existing search by title functionality.

### Within Each User Story

- Backend tasks before frontend tasks for a given search functionality.

### Parallel Opportunities

- All Setup tasks marked [P] can run in parallel
- All Foundational tasks marked [P] can run in parallel (within Phase 2)
- Once Foundational phase completes, User Story 1 and User Story 2 can be worked on in parallel, with careful coordination to avoid conflicts in `ManuscriptController@index` and `client/src/pages/manuscripts/index.vue`.
- Tasks T008, T009, T010 (backend logic) can be done sequentially or in parallel if the developer is careful.
- Tasks T011, T012, T013 (frontend logic) can be done sequentially or in parallel if the developer is careful.
- Tasks T018 and T019 (tests) can run in parallel.

---

## Parallel Example: User Story 1

```bash
# Backend Logic
Task: "T008 [US1] Modify ManuscriptController@index to include search by reference_no in server/app/Http/Controllers/ManuscriptController.php"
Task: "T009 [US1] Ensure search is case-insensitive for reference_no in server/app/Http/Controllers/ManuscriptController.php"
Task: "T010 [US1] Implement logic to prioritize reference_no search if the term matches a reference number format in server/app/Http/Controllers/ManuscriptController.php"

# Frontend Logic
Task: "T011 [US1] Update search input field to handle both title and reference number search in client/src/pages/manuscripts/index.vue"
Task: "T012 [US1] Modify useManuscripts composable to pass search term to backend for both title and reference number in client/src/composables/useManuscripts.ts"
Task: "T013 [US1] Display \"No results found\" message when no manuscripts match the search criteria in client/src/pages/manuscripts/index.vue"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1.  Complete Phase 1: Setup
2.  Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3.  Complete Phase 3: User Story 1
4.  **STOP and VALIDATE**: Test User Story 1 independently
5.  Deploy/demo if ready

### Incremental Delivery

1.  Complete Setup + Foundational → Foundation ready
2.  Add User Story 1 → Test independently → Deploy/Demo (MVP!)
3.  Add User Story 2 → Test independently → Deploy/Demo
4.  Each story adds value without breaking previous stories

### Parallel Team Strategy

With multiple developers:

1.  Team completes Setup + Foundational together
2.  Once Foundational is done:
    -   Developer A: User Story 1 (Backend)
    -   Developer B: User Story 1 (Frontend)
    -   Developer C: User Story 2 (Verification/Maintenance)
3.  Stories complete and integrate independently

---

## Notes

-   [P] tasks = different files, no dependencies
-   [Story] label maps task to specific user story for traceability
-   Each user story should be independently completable and testable
-   Verify tests fail before implementing
-   Commit after each task or logical group
-   Stop at any checkpoint to validate story independently
-   Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence
