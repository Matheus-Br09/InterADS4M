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

    /**
     * `senha_alterada_em` é a data da última rotação de senha da conta. A
     * tabela `apoiadores` nunca teve `created_at`/`updated_at` (o model desliga
     * timestamp de propósito), então não existia nenhuma forma de responder
     * "essa conta já foi rotacionada?" — que era a pergunta que sobrava depois
     * de Exposure de hashes no histórico do git.
     */
    protected $casts = [
        'senha_alterada_em' => 'datetime',
        'trocar_senha_obrigatorio' => 'boolean',
    ];

    protected $fillable = [
        'nome_completo',
        'email',
        'senha',
        'senha_alterada_em',
        'trocar_senha_obrigatorio',
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

    // Endereço e papel também são dado pessoal (e 'tipo_usuario' revelaria
    // quem é a gestão da ONG): ficam fora de qualquer JSON. As views Blade leem
    // os atributos direto, então o painel continua mostrando os dados dele.
    protected $hidden = [
        'senha',
        'cpf',
        'celular',
        'email',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'tipo_usuario',
        // Quando a senha foi trocada é informação de segurança: quem controla a
        // conta não precisa publicá-la, e quem lê a API menos ainda.
        'senha_alterada_em',
        // Estado interno: se a conta está presa na troca de senha. Não interessa
        // a ninguém fora do sistema, e a tela de troca lê o atributo direto.
        'trocar_senha_obrigatorio',
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
