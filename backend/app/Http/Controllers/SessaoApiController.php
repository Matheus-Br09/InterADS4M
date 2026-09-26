<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessaoApiController extends Controller
{
    public function csrf(Request $request): array
    {
        return ['token' => $request->session()->token()];
    }
}
