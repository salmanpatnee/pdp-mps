# Tasks: Co-author Notification

This file breaks down the implementation of the co-author notification feature into actionable tasks.

## Backend

- [x] **Task 1: Create `notification_logs` table migration**
  - Create a new database migration file for the `notification_logs` table with the schema defined in `data-model.md`.

- [x] **Task 2: Create `NotificationLog` model**
  - Create a new Eloquent model for the `notification_logs` table.

- [x] **Task 3: Create `CoAuthorNotification` Mailable**
  - Create a new Mailable class named `CoAuthorNotification`.
  - The Mailable should accept the manuscript and co-author data.
  - Use a markdown template for the email body, including the manuscript title and reference number.

- [x] **Task 4: Create `SendCoAuthorNotification` Job**
  - Create a new Job class named `SendCoAuthorNotification` that implements the `ShouldQueue` interface.
  - The job should accept the manuscript and co-author data.
  - In the `handle` method, send the `CoAuthorNotification` mailable.
  - Implement logic to create and update the `notification_logs` table with the status of the email.

- [x] **Task 5: Create Manuscript Submission Listener**
  - Create a new listener that listens for the manuscript submission event.
  - In the listener, iterate through the co-authors and dispatch the `SendCoAuthorNotification` job for each co-author.

- [x] **Task 6: Configure Mailtrap and Queues**
  - Configure the Mailtrap mail driver in `config/mail.php` and add the necessary credentials to the `.env` file.
  - Configure a dedicated `emails` queue and set up a supervisor to run the queue worker.

## Frontend

- No frontend changes are required for this feature.

## Documentation

- [x] **Task 7: Update Deployment Documentation**
  - Update the deployment documentation to include instructions for configuring and running the queue worker for the `emails` queue.
