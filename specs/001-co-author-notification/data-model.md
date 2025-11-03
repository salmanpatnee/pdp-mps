# Data Model: Co-author Notification

This feature introduces a new table to log notification emails sent to co-authors.

## `notification_logs`

This table will store the status of each notification email sent to a co-author.

### Schema

| Column | Type | Modifiers | Description |
|---|---|---|---|
| `id` | `bigint` | `unsigned`, `auto_increment`, `primary key` | The primary key. |
| `manuscript_id` | `bigint` | `unsigned` | Foreign key to the `manuscripts` table. |
| `co_author_email` | `varchar(255)` | | The email address of the co-author. |
| `status` | `enum('pending', 'sent', 'failed')` | | The status of the notification. |
| `sent_at` | `timestamp` | `nullable` | The timestamp when the email was sent. |
| `failed_at` | `timestamp` | `nullable` | The timestamp when the email failed to send. |
| `error_message` | `text` | `nullable` | The error message if the email failed to send. |
| `created_at` | `timestamp` | | The timestamp when the record was created. |
| `updated_at` | `timestamp` | | The timestamp when the record was last updated. |
