<?php

namespace App\Models;

use App\Models\EnderecoCliente;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Cliente extends Authenticatable
{
    use HasFactory, Notifiable;
    public $timestamps = false;

    protected $guard = "cliente";
    protected $table = "cliente";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nome',
        'dddTelefone',
        'telefone',
        'sexo',
        'dataNascimento',
        'email',
        'senha',
        'tipoPessoa',
        'cpf',
        'cnpj',
        'precoAcertado',
        'logado',
        'rating',
        'pontuacao',
        'premios',
        'status',
        'regld',
        'ultimoLogin',
        'outrosContatos',
        'appVersion',
        'provider',
        'provider_id',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'senha',
        'remember_token',
    ];

     /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

	//SEXO
	const MASCULINO = 1;
	const FEMININO 	= 2;
	//TIPO_PESSOA
	const PESSOA_FISICA 	= 1;
	const PESSOA_JURIDICA 	= 2;
	//STATUS
	const ATIVO = 1;
	const INATIVO = 2;
    const EXCLUIDO = 3;

    //relacionamento UM para Muitos Inverso
    //public $appends = ['enderecos','pedidos'];
    public function enderecos(): HasMany
    {
        return $this->hasMany(EnderecoCliente::class, 'idCliente')->where('status',1);
    }

    public function pedidos(): HasManyThrough
    {
        return $this->hasManyThrough(Pedido::class, EnderecoCliente::class, 'idCliente', 'idEndereco')->orderBy("pedido.id", "DESC")->limit(50)->with('distribuidor');
    }
    public function getEnderecosAttribute(){
        return $this->enderecos()->get();
     }

     public function getPedidosAttribute(){
        return $this->pedidos()->get();
     }

     public function reminders(): HasMany
{
    return $this->hasMany(Reminder::class, 'id_cliente');
}
}
