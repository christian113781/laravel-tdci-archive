<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class PatronVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $status;
    public $statusMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $status, $statusMessage = null)
    {
        $this->userName = $userName;
        $this->status = $status;
        $this->statusMessage = $statusMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'verified'
            ? 'Account Verification - APPROVED'
            : 'Account Verification - REJECTED';

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
            view: 'mail.patron-verification-email',
            with: [
                'userName' => $this->userName,
                'status' => $this->status,
                'statusMessage' => $this->statusMessage,
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
