<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preco extends Model
{
    use HasFactory;
    protected $table = 'preco';
    protected $fillable = [
        'idProduto',//chave estrangeira
        'idDistribuidor',//chave estrangeira
        'idEstoque',//chave estrangeira
        'idCliente',//chave estrangeira
        'valor',
        'qtdMin',
        'inicioValidade',
        'fimValidade',
        'inicioHora',
        'fimHora',
        'status'
    ];
    public $timestamps = false;
    //STATUS
    const ATIVO = 1;
    const INATIVO = 2;
    const EXCLUIDO = 3;

    //RELACIONAMENTO
    public function distribuidor()
    {
        return $this->belongsTo('App\Distribuidor', 'idDistribuidor');
    }
    public function produto()
    {
        return $this->belongsTo('App\Produto', 'idProduto');
    }
    public function estoque()
    {
        return $this->belongsTo('App\Estoque', 'idEstoque');
    }
}
