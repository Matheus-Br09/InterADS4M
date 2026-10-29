<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voluntario extends Model
{
    use HasFactory;

    protected $table = 'voluntarios';

    public $timestamps = false;

    protected $fillable = [
        'apoiador_id',
        'nome_completo',
        'email',
        'celular',
        'data_nascimento',
        'area_atuacao',
        'disponibilidade',
        'arquivo_curriculo',
        'status',
        'data_entrevista',
        'mensagem_entrevista',
        'data_inscricao',
    ];

    public function apoiador(): BelongsTo
    {
        return $this->belongsTo(Apoiador::class, 'apoiador_id');
    }
}
