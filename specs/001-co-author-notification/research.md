# Research: Co-author Notification

## Research Tasks

- **Task 1**: Research free-tier limitations of Mailtrap and Brevo to determine which service is more suitable for the expected volume of emails.
- **Task 2**: Determine the best practices for implementing Laravel queues for sending emails.
- **Task 3**: Design the `notification_logs` table schema.

## Findings

### Email Service Comparison (Mailtrap vs. Brevo)

**Mailtrap**:
- **Free Tier**: Offers a free plan with limitations on the number of emails per month and per day. Good for development and testing, but may not be suitable for a high-volume production environment.
- **Features**: Provides email testing, and debugging features.

**Brevo (formerly Sendinblue)**:
- **Free Tier**: Offers a more generous free plan with a higher daily sending limit compared to Mailtrap. Suitable for low to medium volume production applications.
- **Features**: Provides email marketing, and transactional email services.

**Decision**: We will use **Mailtrap** for sending emails.

### Laravel Queues for Emails

**Best Practices**:
- Use a dedicated queue for sending emails to avoid blocking other jobs.
- Configure queue workers to process jobs in the background.
- Implement a failure handling mechanism to retry failed jobs or log them for manual review.
- Use the `ShouldQueue` interface on the Mailable class.

**Decision**: We will use a dedicated `emails` queue and configure a supervisor to keep the queue worker running.

### `notification_logs` Table Schema

**Decision**: The `notification_logs` table will have the following schema:

- `id` (Primary Key, BigInt, Auto-increment)
- `manuscript_id` (Foreign Key to `manuscripts` table)
- `co_author_email` (String)
- `status` (Enum: `pending`, `sent`, `failed`)
- `sent_at` (Timestamp, nullable)
- `failed_at` (Timestamp, nullable)
- `error_message` (Text, nullable)
- `created_at` (Timestamp)
- `updated_at` (Timestamp)
