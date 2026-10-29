<?php

// Dados da ONG usados em doações por Pix e nos canais de contato.
// Os valores reais ficam no .env (nunca no código).
return [
    // Chave Pix que recebe as doações. Confirmar diretamente com a ONG.
    'pix_chave' => env('ONG_PIX_CHAVE', ''),

    // Nome do recebedor no Pix (máx. 25 caracteres) e cidade (máx. 15).
    'pix_nome' => env('ONG_PIX_NOME', ''),
    'pix_cidade' => env('ONG_PIX_CIDADE', ''),

    // WhatsApp somente com dígitos, ex.: 5581999999999.
    'whatsapp' => env('ONG_WHATSAPP', ''),
    'instagram' => env('ONG_INSTAGRAM', ''),

    // E-mail interno da gestão (não é exposto publicamente).
    'email_gestao' => env('ONG_EMAIL_GESTAO', ''),

    // Dias até uma doação pendente expirar.
    'doacao_expira_dias' => (int) env('ONG_DOACAO_EXPIRA_DIAS', 7),
];
