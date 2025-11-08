<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailTrap extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $status;


     public function __construct(string $name, string $status)
    {
        $this->name = $name;
        $this->status = $status;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mail Trap',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.test-email',
            with: ['name' => $this->name]
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

     public function build()
    {
        $subject = $this->status === 'approved'
            ? 'Your Archive Access Request is Approved'
            : 'Your Archive Access Request is Rejected';

        return $this->subject($subject)
                    ->view('mail.test-email')
                    ->with([
                        'name' => $this->name,
                        'status' => $this->status,
                    ]);
    }
}
