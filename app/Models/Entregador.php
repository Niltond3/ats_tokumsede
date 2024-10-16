<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entregador extends Model
{
    use HasFactory;
    protected $table = 'entregador';
    protected $fillable = [
        'nome',
        'idDistribuidor',
        'placaVeiculo',
        'telefone',
        'status',
    ];
    public $timestamps = false;
    //STATUS
    const ATIVO = 1;
    const INATIVO = 2;
    const EXCLUIDO = 3;
    //
    public function distribuidor()
    {
        return $this->belongsTo('App\Distribuidor', 'idDistribuidor');
    }
}
