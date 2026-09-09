<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoacaoUnica extends Model
{
    use HasFactory;

    protected $table = 'doacoes_unicas';

    public $timestamps = false;

    protected $fillable = [
        'apoiador_id',
        'valor',
        'metodo_pagamento',
        'data_doacao',
    ];

    public function apoiador(): BelongsTo
    {
        return $this->belongsTo(Apoiador::class, 'apoiador_id');
    }
}
