<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Apoiador extends Authenticatable
{
    use Notifiable;

    protected $table = 'apoiadores';

    public $timestamps = false;

    protected $fillable = [
        'nome_completo',
        'email',
        'senha',
        'celular',
        'cpf',
        'sexo',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'data_cadastro',
    ];

    protected $hidden = [
        'senha',
    ];

    // Diz ao Laravel que o campo de senha da tabela é 'senha' e não 'password'
    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function apadrinhamentos(): HasMany
    {
        return $this->hasMany(Apadrinhamento::class, 'apoiador_id');
    }

    public function doacoesMensais(): HasMany
    {
        return $this->hasMany(DoacaoMensal::class, 'apoiador_id');
    }

    public function doacoesUnicas(): HasMany
    {
        return $this->hasMany(DoacaoUnica::class, 'apoiador_id');
    }

    public function voluntario(): HasOne
    {
        return $this->hasOne(Voluntario::class, 'apoiador_id');
    }
}