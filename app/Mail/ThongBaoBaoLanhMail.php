<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ThongBaoBaoLanhMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $tieuDe,
        public readonly string $thongBao,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->tieuDe);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.thong-bao-bao-lanh', with: [
            'thongBao' => $this->thongBao,
        ]);
    }
}
