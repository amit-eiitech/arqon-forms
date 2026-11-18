<?php

namespace Arqon\Forms\Mail;

use Arqon\Forms\Models\Form;
use Arqon\Forms\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormSubmissionNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Form $form,
        public FormSubmission $submission,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New form submission: '.$this->form->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'arqon-forms::emails.submission-notification',
            with: [
                'form' => $this->form,
                'submission' => $this->submission,
            ],
        );
    }
}
