<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voluntario extends Model
{
    use HasFactory;

    protected $table = 'voluntarios';

    public $timestamps = false;

    protected $fillable = [
        'nome_completo',
        'idade',
        'telefone',
        'email',
        'area_atuacao',
        'arquivo_curriculo',
        'data_cadastro',
    ];
}
