<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'appVersion'
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
    // //relacionamento UM para Muitos Inverso
    // public function enderecos()
    // {
    //     return $this->hasMany('App\EnderecoCliente', 'idCliente')->where('status',1);
    // }
    // public function pedidos()
    // {
    //     return $this->hasManyThrough('App\Pedido', 'App\EnderecoCliente', 'idCliente', 'idEndereco')->orderBy("pedido.id", "DESC")->limit(50)->with('distribuidor');
    // }

}
