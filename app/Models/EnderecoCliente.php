<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Cliente;
class EnderecoCliente extends Model
{
    use HasFactory;

    protected $table = 'enderecoCliente';
    protected $fillable = [
        'id',
        'logradouro',
        'numero',
        'bairro',
        'complemento',
        'cep',
        'cidade',
        'estado',
        'referencia',
        'apelido',
        'atual',
        'latitude',
        'longitude',
        'status',
        'idCliente',
        'observacao'
    ];
    public $timestamps = false;
    const ATIVO = 1;
	const INATIVO = 2;
	const EXCLUIDO = 3;
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'idCliente');
    }

}
