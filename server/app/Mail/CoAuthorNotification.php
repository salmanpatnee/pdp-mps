<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoAuthorNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $manuscript;
    public $coAuthor;

    /**
     * Create a new message instance.
     */
    public function __construct($manuscript, $coAuthor)
    {
        $this->manuscript = $manuscript;
        $this->coAuthor = $coAuthor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Submission Acknowledgement | ' . $this->manuscript->reference_no . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.co_author_notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
