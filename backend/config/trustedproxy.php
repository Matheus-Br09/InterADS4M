<?php

use App\Support\ProxiesConfiaveis;

// Lido pelo middleware TrustProxies do Laravel. Sem TRUSTED_PROXIES: nenhum proxy é confiável.
return [
    'proxies' => ProxiesConfiaveis::aPartirDe(env('TRUSTED_PROXIES')),
];
