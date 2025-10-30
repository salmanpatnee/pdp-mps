<!--
Sync Impact Report:
- Version change: 1.0.0 → 1.1.0
- Modified principles: None
- Added sections: 
  - Governance
  - 9. Comprehensive Documentation
- Removed sections: None
- Templates requiring updates: None
- Follow-up TODOs:
  - TODO(RATIFICATION_DATE): Set the date when the constitution is first adopted.
-->

# Project Constitution — Manuscript Processing System (MPS)

## Purpose
Establish clear and enforceable principles for all AI-assisted and human-led development within the MPS workspace.  
This constitution defines standards for architecture, quality, testing, and user experience across both backend (Laravel) and frontend (Vue 3) codebases.

---

## Governance

- **Version:** 1.1.0
- **Ratification Date:** TODO(RATIFICATION_DATE): Set the date when the constitution is first adopted.
- **Last Amended Date:** 2025-10-29
- **Amendment Process:** Changes to this constitution must be proposed as a pull request and approved by the project owners.

---

## 1. Architectural Principles
- Follow **modular, service-driven architecture** in Laravel using Services, Resources, and Form Requests for separation of concerns.
- Maintain **component-based design** in Vue 3 using Composition API and TypeScript for type safety.
- Use **RESTful conventions** for all API endpoints.
- Ensure **scalability and reusability** — no tightly coupled logic between backend and frontend.

---

## 2. Code Quality Standards
- All code must be clean, self-documenting, and follow PSR-12 (Laravel) and ESLint/Prettier (Vue) rules.
- Business logic must reside in **service classes** — never inside controllers or Vue components.
- Follow the **Single Responsibility Principle (SRP)** and maintain clear boundaries between data, logic, and presentation.
- Use consistent naming, folder structure, and imports across the workspace.

---

## 3. Testing & Validation
- All backend features must include **Pest tests** covering API endpoints, validation rules, and model relationships.
- All frontend features must include **Vitest tests** for composables, stores, and components.
- No feature is considered complete without automated tests confirming critical paths.
- Prefer test-driven or spec-driven development (start from `/spec` files).

---

## 4. User Experience (UX) Consistency
- Maintain visual and behavioral consistency across all modules (authors, manuscripts, reviewers, editors, etc.).
- Use **FormKit** for all forms and **Dropzone.js** for uploads.
- Follow accessible design principles — ensure keyboard navigation and semantic HTML.
- Provide helpful error and success feedback using unified toast notifications.

---

## 5. Performance & Optimization
- Optimize all backend queries with Eloquent relationships and eager loading.
- Use **Vue Query (TanStack)** for data fetching and caching to avoid redundant API calls.
- Lazy-load routes and components in Vue for faster initial loads.
- Ensure uploaded files are handled asynchronously and efficiently stored using Laravel’s filesystem.

---

## 6. Security & Data Integrity
- Use Laravel Sanctum for authentication.
- Validate every input server-side using Form Requests.
- Protect sensitive data; never expose IDs, tokens, or raw database fields directly in the API responses.
- Apply consistent role-based access control (RBAC) for all modules.

---

## 7. Workflow & Collaboration
- Use **Git branching strategy** (`feature/`, `fix/`, `refactor/`, `release/`).
- Commit messages follow **Conventional Commits**.
- Each new feature starts with a Spec-Kit `/sp.spec` document defining purpose, scope, and acceptance criteria.
- Gemini CLI is the designated AI agent for planning, code generation, and review.

---

## 8. Continuous Improvement
- Regularly refactor repetitive patterns into reusable modules.
- Encourage documentation updates alongside every feature.
- Review test coverage and improve weak areas each sprint.

---

## 9. Comprehensive Documentation
- **Definition:** All new features, architectural decisions, and public APIs must be clearly documented.
- **Rationale:** Good documentation is essential for onboarding new developers, maintaining the system, and ensuring long-term project success.

---

## Summary
This constitution serves as the governing guideline for the Manuscript Processing System project.  
All contributors — human or AI — must adhere to these principles to ensure consistent quality, maintainability, and scalability of the system. This constitution is currently at version 1.1.0.