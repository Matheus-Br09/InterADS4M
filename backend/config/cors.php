<?php

use App\Support\OrigensCors;

/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
|
| O site em inter-ong/ roda em outra porta que o backend, então o navegador
| precisa de permissão para ler as respostas. Em desenvolvimento qualquer
| origem pode falar com a API.
|
| ANTES DE PUBLICAR: não edite este arquivo. A lista de origens vem do .env,
| na linha CORS_ALLOWED_ORIGINS (ex.: https://ongsos.org.br). O motivo é que
| o ajuste de produção é uma configuração de máquina, não um commit de código:
| editar o config faria o '*' de desenvolvimento acabar em produção junto.
|
*/

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => OrigensCors::aPartirDe(env('CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
