<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeria extends Model
{
    use HasFactory;

    protected $table = 'galeria';

    public $timestamps = false;

    protected $fillable = [
        'legenda',
        'nome_imagem',
        'data_upload',
    ];
}
