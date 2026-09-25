<?php

/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
|
| O site em inter-ong/ roda em outra porta que o backend, então o navegador
| precisa de permissão para ler as respostas. Em desenvolvimento qualquer
| origem pode falar com a API.
|
| ANTES DE PUBLICAR: troque 'allowed_origins' pela origem real do site
| (ex.: ['https://ongsos.org.br']) e volte 'supports_credentials' para
| false se continuar sem cookie de sessão.
|
*/

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
