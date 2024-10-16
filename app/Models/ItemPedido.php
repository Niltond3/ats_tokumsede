<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    use HasFactory;
    protected $table = 'itemPedido';
    protected $fillable = [
        'id',
        'idProduto',
        'idPedido',
        'qtd',
        'preco',
        'precoAcertado',
        'subtotal'
    ];
    public $timestamps = false;
    public function produto()
    {
        return $this->belongsTo('App\Produto', 'idProduto');
    }
    public function pedido()
    {
        return $this->belongsTo('App\Pedido', 'idPedido');
    }

}
