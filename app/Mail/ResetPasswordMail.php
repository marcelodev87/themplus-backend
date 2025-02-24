<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// use Illuminate\Mail\Mailables\Address;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public $name;

    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de Redefinição de Senha',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ResetPassword',
            with: [
                'code' => $this->code,
                'user' => $this->name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
