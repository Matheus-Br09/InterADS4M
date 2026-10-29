<?php

namespace App\Mail;

use App\Models\Voluntario;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NovoVoluntario extends Mailable
{
    public function __construct(public Voluntario $voluntario) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Nova inscrição de voluntário — {$this->voluntario->nome_completo}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.novo-voluntario');
    }
}
