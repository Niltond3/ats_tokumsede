<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnderecoDistribuidor extends Model
{
    use HasFactory;
    protected $table = 'enderecoDistribuidor';
    protected $fillable = [
        'logradouro',
        'numero',
        'bairro',
        'complemento',
        'cep',
        'cidade',
        'estado',
        'referencia',
        'latitude',
        'longitude',
        'distanciaMaxima'
    ];
    public $timestamps = false;
    //
}
