<?php

namespace App\Models;

use App\Rules\Cpf;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'tipo_usuario',
        'data_cadastro',
    ];

    // Campos que nunca podem sair em JSON. As views Blade leem os atributos
    // direto, então o painel do próprio apoiador continua mostrando os dados dele.
    protected $hidden = [
        'senha',
        'cpf',
        'celular',
        'email',
    ];

    // Diz ao Laravel que o campo de senha da tabela é 'senha' e não 'password'
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // Guarda o CPF só com dígitos, venha ele mascarado ou não. Assim a
    // unicidade do cadastro não depende de como a pessoa digitou.
    protected function cpf(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value) => Cpf::apenasDigitos($value),
        );
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
