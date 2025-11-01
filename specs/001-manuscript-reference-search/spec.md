# Feature Specification: Manuscript Search by Reference Number

**Feature Branch**: `001-manuscript-reference-search`  
**Created**: 2025-11-01  
**Status**: Draft  
**Input**: User description: "feature manuscript search - Manuscripts can be search by reference no and title currently it can be search by title but it is not practical if we can't search it by reference no so, I want it to be search by the reference no as well."

## Success Criteria *(mandatory)*

### Measurable Outcomes

-   **SC-001**: 95% of searches (by reference number or title) must return results in under 2 seconds.
-   **SC-002**: Users must be able to find a manuscript by its full reference number with 100% accuracy.
-   **SC-003**: The introduction of search by reference number should not degrade the performance of search by title.

## Clarifications

### Session 2025-11-01

- Q: Who should be able to use this search functionality? → A: All authenticated users
- Q: How should the system behave when a user enters a partial reference number? → A: Support partial matching (e.g., "REF-123" matches "REF-12345")

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Search by Reference Number (Priority: P1)

As an authenticated user, I want to be able to search for a manuscript by its reference number so that I can quickly find a specific manuscript.

**Why this priority**: This is the core functionality requested by the user and is essential for efficient manuscript lookup.

**Independent Test**: This can be tested independently by entering a known reference number into the search field and verifying that the correct manuscript is returned.

**Acceptance Scenarios**:

1.  **Given** an authenticated user is on the manuscript list page, **When** they enter a full or partial, existing reference number into the search field and trigger the search, **Then** the system MUST display all manuscripts with matching reference numbers.
2.  **Given** an authenticated user is on the manuscript list page, **When** they enter a non-existent reference number (full or partial) into the search field and trigger the search, **Then** the system MUST display a "No results found" message.

---

### User Story 2 - Search by Title (Priority: P2)

As an authenticated user, I want to continue to be able to search for manuscripts by title so that I can find manuscripts when I don't know the reference number.

**Why this priority**: This is existing functionality that needs to be maintained.

**Independent Test**: This can be tested by entering a known manuscript title into the search field and verifying that the correct manuscript(s) are returned.

**Acceptance Scenarios**:

1.  **Given** an authenticated user is on the manuscript list page, **When** they enter a full or partial title into the search field and trigger the search, **Then** the system MUST display a list of manuscripts that match the title.

---

### Edge Cases

-   When a user enters a partial reference number, the system will return all manuscripts with reference numbers containing the entered partial string.
-   How does the system handle searches with both reference number and title criteria? (e.g., if the search is a single field). The system should prioritize reference number matching if the search term looks like a reference number.

## Requirements *(mandatory)*

### Functional Requirements

-   **FR-001**: The system MUST provide a single search input field on the manuscript list page for searching by title or reference number.
-   **FR-002**: The system MUST allow authenticated users to search for manuscripts by their exact or partial reference number.
-   **FR-003**: The system MUST continue to allow authenticated users to search for manuscripts by their title.
-   **FR-004**: The search MUST be case-insensitive for both title and reference number.
-   **FR-005**: If the search term matches the format of a reference number, the system SHOULD prioritize searching by reference number.
-   **FR-006**: If no manuscripts match the search criteria, the system MUST display a "No results found" message.
-   **FR-007**: The system MUST recognize reference numbers in the format `PDP-JJJJ-YYYY-NNNN`, where `PDP` is a fixed prefix, `JJJJ` is a journal prefix, `YYYY` is the current year, and `NNNN` is an autoincrementing number (e.g., `PDP-IJAI-2025-0001`).