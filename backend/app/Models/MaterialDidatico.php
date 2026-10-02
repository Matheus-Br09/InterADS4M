<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialDidatico extends Model
{
    use HasFactory;

    protected $table = 'materiais_didaticos';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descricao',
        'arquivo_pdf',
        'imagem_capa',
        'data_upload',
    ];
}
