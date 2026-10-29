<?php

namespace App\Http\Controllers;

use App\Support\DadosDaConta;
use Illuminate\Http\Request;

class ContaApiController extends Controller
{
    public function perfil(Request $request): array
    {
        return ['dados' => DadosDaConta::de($request->user('apoiador'))];
    }
}
