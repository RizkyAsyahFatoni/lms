<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $bodyHtml;
    public ?string $actionUrl;

    public function __construct(string $subject, string $bodyHtml, ?string $actionUrl = null)
    {
        $this->emailSubject = $subject;
        $this->bodyHtml = $bodyHtml;
        $this->actionUrl = $actionUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dynamic_notification',
            with: [
                'emailSubject' => $this->emailSubject,
                'bodyHtml'     => $this->bodyHtml,
                'actionUrl'    => $this->actionUrl,
            ],
        );
    }
}

