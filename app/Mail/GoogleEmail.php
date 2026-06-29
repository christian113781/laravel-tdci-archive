<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class GoogleEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $status;
    public $archiveTitle;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $status, $archiveTitle = null, $message = null)
    {
        $this->userName = $userName;
        $this->status = $status;
        $this->archiveTitle = $archiveTitle;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved'
            ? 'Your Archive Access Request - APPROVED'
            : 'Your Archive Access Request - REJECTED';

        return new Envelope(
            from: new Address('tagumdoctors727@gmail.com', 'TDCI Archive'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.gmail-email',
            with: [
                'userName' => $this->userName,
                'status' => $this->status,
                'archiveTitle' => $this->archiveTitle,
                'message' => $this->message,
            ],
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
