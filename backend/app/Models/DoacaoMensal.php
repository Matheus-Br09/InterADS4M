<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoacaoMensal extends Model
{
    use HasFactory;

    protected $table = 'doacoes_mensais';

    public $timestamps = false;

    protected $fillable = [
        'apoiador_id',
        'valor_mensal',
        'dia_vencimento',
        'status',
        'data_assinatura',
    ];

    public function apoiador(): BelongsTo
    {
        return $this->belongsTo(Apoiador::class, 'apoiador_id');
    }
}
