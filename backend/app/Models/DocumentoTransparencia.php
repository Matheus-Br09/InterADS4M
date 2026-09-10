<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoTransparencia extends Model
{
    use HasFactory;

    protected $table = 'documentos_transparencia';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'ano_referencia',
        'tipo_documento',
        'arquivo_pdf',
        'data_upload',
    ];
}
