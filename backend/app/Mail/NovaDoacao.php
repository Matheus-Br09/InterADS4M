<?php

namespace App\Mail;

use App\Models\DoacaoUnica;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NovaDoacao extends Mailable
{
    public function __construct(public DoacaoUnica $doacao) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Nova doação registrada — protocolo {$this->doacao->protocolo}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.nova-doacao');
    }
}
