<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ArchiveAccessRequest;

class RequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(ArchiveAccessRequest $request, string $status)
    {
        $this->request = $request;
        $this->status = $status;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Create subject line dynamically
        $subject = "Archive Request " . ucfirst($this->status) . ": " . $this->request->archive->title;

        return $this->subject($subject)
                    ->view('emails.requestMail')
                    ->with([
                        'request' => $this->request,
                        'status'  => $this->status,
                    ]);
    }
}
