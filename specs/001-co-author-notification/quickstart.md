# Quickstart: Co-author Notification

This guide provides a brief overview of how to set up and use the co-author notification feature.

## Setup

1.  **Configure Mailtrap API Key**:

    Add your Mailtrap API key to the `.env` file:

    ```
    MAILTRAP_API_KEY=your-api-key
    ```

2.  **Run the Queue Worker**:

    Start the queue worker to process email jobs:

    ```bash
    php artisan queue:work --queue=emails
    ```

## Usage

1.  **Submit a Manuscript**:

    Submit a new manuscript through the application and add one or more co-authors.

2.  **Check Notification Status**:

    After the manuscript is submitted, check the `notification_logs` table in the database to see the status of the notification emails.
