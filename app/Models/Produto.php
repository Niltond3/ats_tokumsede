<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Preco;
use App\Models\Estoque;
use App\Models\Categoria;

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
        return $this->belongsTo(Categoria::class, 'idCategoria');
    }
    public function preco()
    {
        return $this->hasMany(Preco::class, 'idProduto')->where('idDistribuidor', auth()->user()->idDistribuidor)->where('status', '!=', Produto::EXCLUIDO);
    }
    public function precos()
    {
        return $this->hasManyThrough(Preco::class, Estoque::class, 'idProduto', 'idEstoque');
    }
    public function estoque()
    {
        return $this->hasMany(Estoque::class, 'idProduto')->where('idDistribuidor', auth()->user()->idDistribuidor);
    }
    //
}
