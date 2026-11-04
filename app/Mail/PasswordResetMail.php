<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $url,
        public readonly ?string $thongBao = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Đặt lại mật khẩu Scaros');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-reset', with: [
            'url' => $this->url,
            'thongBao' => $this->thongBao,
        ]);
    }
}
