# Data Model for Co-Author Notification Feature

This document outlines the data entities, their attributes, relationships, and validation rules for the co-author notification feature.

## Entities

### Manuscript
- `id`: Primary Key (UUID/BIGINT)
- `title`: String
- `primary_author_id`: Foreign Key to `User.id`

### User
- `id`: Primary Key (UUID/BIGINT)
- `email`: String (Unique, Valid Email Format)
- `name`: String

### CoAuthorInvitation
- `id`: Primary Key (UUID/BIGINT)
- `manuscript_id`: Foreign Key to `Manuscript.id`
- `co_author_email`: String (Valid Email Format)
- `token`: String (Unique, Securely generated for invitation link)
- `status`: Enum (`pending`, `accepted`, `declined`)
- `invited_at`: Datetime
- `responded_at`: Datetime (Nullable)
- `expires_at`: Datetime (7 days after `invited_at`)

### ManuscriptCoAuthor (Join Table)
- `manuscript_id`: Foreign Key to `Manuscript.id` (Part of Composite Primary Key)
- `user_id`: Foreign Key to `User.id` (Part of Composite Primary Key)
- `added_at`: Datetime
- `permissions`: Enum (`edit_content`) - Reflects the access level granted upon acceptance.

## Relationships

- A `Manuscript` has one `primary_author` (`User`).
- A `Manuscript` can have many `CoAuthorInvitations`.
- A `CoAuthorInvitation` belongs to one `Manuscript`.
- A `Manuscript` can have many `CoAuthors` (`User`) through the `ManuscriptCoAuthor` join table.
- A `User` can be a co-author of many `Manuscripts` through the `ManuscriptCoAuthor` join table.

## Validation Rules

- `CoAuthorInvitation.co_author_email` must be a valid email format.
- `CoAuthorInvitation.token` must be unique.
- `CoAuthorInvitation.status` must be one of the defined enum values (`pending`, `accepted`, `declined`).
- `CoAuthorInvitation.expires_at` is automatically set to 7 days after `invited_at`.
- `ManuscriptCoAuthor.permissions` must be one of the defined enum values (`edit_content`).