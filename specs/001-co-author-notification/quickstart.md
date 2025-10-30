# Quickstart Guide: Co-Author Notification Feature

This guide provides a quick overview of how to use and test the co-author notification feature.

## 1. Inviting a Co-Author

To invite a co-author to a manuscript:

1.  Ensure you are authenticated as the primary author of the target manuscript.
2.  Make a `POST` request to `/api/manuscripts/{manuscript_id}/co-authors` with the co-author's email in the request body.

    **Example Request:**
    ```http
    POST /api/manuscripts/123e4567-e89b-12d3-a456-426614174000/co-authors
    Content-Type: application/json

    {
        "email": "coauthor@example.com"
    }
    ```

3.  A `201 Created` response indicates a successful invitation. The co-author will receive an email notification.

## 2. Accepting/Declining an Invitation

Co-authors will receive an email with a secure link to accept or decline the invitation.

1.  **Accepting:** Click the "Accept" link in the email, which will typically lead to a `GET` request to `/api/co-author-invitations/{token}/accept`.
2.  **Declining:** Click the "Decline" link in the email, which will typically lead to a `GET` request to `/api/co-author-invitations/{token}/decline`.

    **Example Accept Link:**
    `https://your-platform.com/api/co-author-invitations/some_secure_token_here/accept`

    **Example Decline Link:**
    `https://your-platform.com/api/co-author-invitations/some_secure_token_here/decline`

3.  A `200 OK` response indicates the action was successful.

## 3. Handling Unregistered Users

If an invited co-author's email is not registered in the system, a pending user account will be automatically created for them. They will receive an invitation email that also guides them through the registration process.

## 4. Invitation Expiry

Co-author invitations expire after 7 days if not responded to. After expiry, the invitation link will no longer be valid.
