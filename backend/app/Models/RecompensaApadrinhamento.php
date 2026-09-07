<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecompensaApadrinhamento extends Model
{
    use HasFactory;

    protected $table = 'recompensas_apadrinhamento';

    public $timestamps = false;

    protected $fillable = [
        'apadrinhamento_id',
        'titulo',
        'mensagem',
        'arquivo_midia',
        'data_envio',
    ];

    public function apadrinhamento(): BelongsTo
    {
        return $this->belongsTo(Apadrinhamento::class, 'apadrinhamento_id');
    }
}
