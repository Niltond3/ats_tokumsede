<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;
    protected $table = 'estoque';
    protected $fillable = [
        'idDistribuidor',
        'idProduto',
        'quantidade'
    ];
    public $timestamps = false;
    public function produto()
    {
        return $this->belongsTo('App\Produto', 'idProduto');
    }
    public function distribuidor()
    {
        return $this->belongsTo('App\Distribuidor', 'idDistribuidor');
    }
    //
}
