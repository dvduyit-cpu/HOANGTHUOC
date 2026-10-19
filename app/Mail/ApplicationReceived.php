<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application)
    {
        $this->application->loadMissing('job');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'CV mới: '.$this->application->job->title.' - '.$this->application->name,
            replyTo: [$this->application->email],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-received');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('local', $this->application->cv_path)
                ->as($this->application->cv_original_name),
        ];
    }
}
