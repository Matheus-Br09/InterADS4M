<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crianca extends Model
{
    use HasFactory;

    protected $table = 'criancas';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'data_nascimento',
        'historico',
        'imagem_perfil',
        'status',
        'data_cadastro',
    ];

    public function apadrinhamentos(): HasMany
    {
        return $this->hasMany(Apadrinhamento::class, 'crianca_id');
    }
}
