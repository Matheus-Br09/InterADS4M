<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apadrinhamento extends Model
{
    use HasFactory;

    protected $table = 'apadrinhamentos';

    public $timestamps = false;

    protected $fillable = [
        'apoiador_id',
        'crianca_id',
        'valor_mensal',
        'data_inicio',
        'status',
    ];

    public function apoiador(): BelongsTo
    {
        return $this->belongsTo(Apoiador::class, 'apoiador_id');
    }

    public function crianca(): BelongsTo
    {
        return $this->belongsTo(Crianca::class, 'crianca_id');
    }

    public function recompensas(): HasMany
    {
        return $this->hasMany(RecompensaApadrinhamento::class, 'apadrinhamento_id');
    }
}
