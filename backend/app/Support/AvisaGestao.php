<?php

namespace App\Support;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AvisaGestao
{
    // Avisa a gestão por e-mail. Nunca deixa uma falha de envio quebrar o cadastro.
    public static function enviar(Mailable $email): void
    {
        $destino = config('ong.email_gestao');

        if (empty($destino)) {
            return;
        }

        try {
            Mail::to($destino)->send($email);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
