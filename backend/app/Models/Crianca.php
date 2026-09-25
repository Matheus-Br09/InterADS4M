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

    // 'historico' é texto sobre a criança (às vezes clínico) e
    // 'data_nascimento' é dado de menor: nenhum dos dois sai em JSON, nem
    // por engano. A data de nascimento continua acessível no Blade e a API
    // pública calcula a idade a partir dela.
    protected $hidden = [
        'historico',
        'data_nascimento',
    ];

    public function apadrinhamentos(): HasMany
    {
        return $this->hasMany(Apadrinhamento::class, 'crianca_id');
    }
}
