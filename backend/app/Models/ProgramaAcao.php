<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaAcao extends Model
{
    use HasFactory;

    protected $table = 'programas_acoes';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'resumo',
        'texto_completo',
        'categoria',
        'imagem_capa',
        'status',
        'data_criacao',
    ];
}
