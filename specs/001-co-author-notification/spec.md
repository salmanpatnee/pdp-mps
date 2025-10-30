# Feature Specification: Co-author Notification

**Feature Branch**: `001-co-author-notification`
**Created**: 2025-10-29
**Status**: Draft
**Input**: User description: "As an editor, when I submit a manuscript with co-authors, the system should automatically send each co-author an email notification confirming their inclusion, along with manuscript title, reference number, journal name, and submission details."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Co-authors are notified of manuscript submission (Priority: P1)

As a co-author, I want to receive an email notification when a manuscript I am listed on is submitted, so that I am aware of the submission and can verify the details.

**Why this priority**: This is the core functionality of the feature and is essential for keeping all contributors informed.

**Independent Test**: This can be tested by submitting a manuscript with multiple co-authors and verifying that each co-author receives the correct email notification.

**Acceptance Scenarios**:

1.  **Given** an editor is submitting a new manuscript with one or more co-authors,
    **When** the editor successfully submits the manuscript,
    **Then** each co-author listed on the manuscript receives an email notification.

2.  **Given** a co-author has received a notification email,
    **When** they open the email,
    **Then** the email contains the manuscript title, reference number, journal name, and submission date.

### Edge Cases

-   What happens if a co-author's email address is invalid or bounces?
-   What happens if the manuscript submission fails after the notifications have been sent?
-   What happens if a co-author is added or removed after the initial submission?

## Requirements *(mandatory)*

### Functional Requirements

-   **FR-001**: The system MUST automatically send an email notification to all co-authors listed on a manuscript upon successful submission.
-   **FR-002**: The email notification MUST include the manuscript title.
-   **FR-003**: The email notification MUST include the manuscript reference number.
-   **FR-004**: The email notification MUST include the journal name.
-   **FR-005**: The email notification MUST include the submission date.
-   **FR-006**: The system MUST log the sending of each email notification.
-   **FR-007**: The system MUST log the sending of notifications to co-authors in the manuscript's history/activity log.

## Assumptions

-   The system has a mechanism for logging events to a manuscript's history or activity log.
-   The editor does not require an active, immediate confirmation message that notifications have been sent. The log entry is sufficient.

### Key Entities *(include if feature involves data)*

-   **Manuscript**: Represents the submitted work. Key attributes: title, reference number, submission date, list of co-authors.
-   **Co-author**: Represents a contributor to the manuscript. Key attributes: name, email address.
-   **Notification**: Represents the email sent to a co-author. Key attributes: recipient, subject, body, status (sent, failed).

## Success Criteria *(mandatory)*

### Measurable Outcomes

-   **SC-001**: 100% of co-authors on a successfully submitted manuscript receive an email notification within 5 minutes of submission.
-   **SC-002**: The bounce rate for notification emails is less than 1%.
- Co-author receives a notification with the manuscript title, primary author name, and a link to accept or decline the invitation.
- Upon acceptance, the co-author's acceptance is recorded, and they are associated with the manuscript. They do not gain edit access.
- Upon declining, the co-authorship is not established, and the primary author may be notified.
- Co-author invitations expire after 7 days if not responded to.

## 3. Non-Functional Requirements

### 3.1. Performance
- Notifications should be sent within 5 seconds of a co-author being added.

### 3.2. Security
- Co-authorship invitations should be secure and not easily guessable.
- Only the invited co-author should be able to accept or decline the invitation.

## 4. Data Model (Initial Draft)
- **Manuscript:**
    - `id`
    - `title`
    - `primary_author_id`
- **User:**
    - `id`
    - `email`
    - `name`
- **CoAuthorInvitation:**
    - `id`
    - `manuscript_id`
    - `co_author_email`
    - `token` (for secure link)
    - `status` (pending, accepted, declined)
    - `invited_at`
    - `responded_at`
    - `expires_at`
- **ManuscriptCoAuthor:** (New Join Table)
    - `manuscript_id`
    - `name`
    - `email`
    - `added_at`

## 5. Edge Cases
- If a primary author tries to add an email address that is not registered in the system, the system should store the co-author's email and name. No user account should be created.
- What happens if a co-author tries to accept an invitation that has already been accepted or declined?

## 6. Open Questions / TODOs
- (All questions resolved)

## Clarifications
### Session 2025-10-30
- Q: When a co-author accepts an invitation, what level of access or permissions do they gain on the manuscript? → A: Co-authors do not get edit access. Their acceptance is recorded, and their information (name, email) is associated with the manuscript.
- Q: What specific information should be included in the co-author notification email? → A: Manuscript title, primary author name, accept/decline links.
- Q: If a primary author tries to add an email address that is not registered in the system, what should happen? → A: The system should store the co-author's email and name. No user account should be created.
- Q: Is there a time limit for a co-author to accept an invitation? If so, what happens after it expires? → A: 7 days (invitation expires).
- Q: How should an accepted co-author be formally linked to the manuscript in the data model? → A: Create a new `ManuscriptCoAuthor` join table.
