# Research Findings: Manuscript Search by Reference Number

## Decision

Extend existing manuscript search functionality to include `reference_no`.

## Rationale

The user explicitly requested to use the same approach as the current title search, leveraging the existing search input and the `ManuscriptController`'s `index` method. This minimizes new development and integrates seamlessly with current patterns.

## Alternatives Considered

-   **Creating a separate search endpoint:** Rejected because the user requested to use the existing `index` method and a single search input, which implies a unified search mechanism.
-   **Implementing a dedicated search service:** Rejected for the same reason as above; the current `ManuscriptController` is sufficient for this extension.
