<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;
    protected $table = 'produto';
    protected $fillable = [
        'idCategoria',
        'nome',
        'descricao',
        'img',
        'status',
        'composicao',
        'componente'
    ];
    public $timestamps = false;
    const ATIVO = 1;
    const INATIVO = 2;
    const EXCLUIDO = 3;
    public function categoria()
    {
        return $this->belongsTo('App\Categoria', 'idCategoria');
    }
    public function preco()
    {
        return $this->hasMany('App\Preco', 'idProduto')->where('idDistribuidor', auth()->user()->idDistribuidor)->where('status', '!=', Produto::EXCLUIDO);
    }
    public function precos()
    {
        return $this->hasManyThrough('App\Preco', 'App\Estoque', 'idProduto', 'idEstoque');
    }
    public function estoque()
    {
        return $this->hasMany('App\Estoque', 'idProduto')->where('idDistribuidor', auth()->user()->idDistribuidor);
    }
    //
}
