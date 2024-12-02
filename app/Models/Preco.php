<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Distribuidor;
use App\Models\Produto;
use App\Models\Estoque;

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
        return $this->belongsTo(Distribuidor::class, 'idDistribuidor');
    }
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'idProduto');
    }
    public function estoque()
    {
        return $this->belongsTo(Estoque::class, 'idEstoque');
    }
}
