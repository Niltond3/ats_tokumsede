<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;
use App\Models\Distribuidor;

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
        return $this->belongsTo(Produto::class, 'idProduto');
    }
    public function distribuidor()
    {
        return $this->belongsTo(Distribuidor::class, 'idDistribuidor');
    }
    //
}
