<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribuidor extends Model
{
    use HasFactory;

    protected $table = 'distribuidor';
    protected $fillable = [
        'id',
        'nome',
        'cnpj',
        'email',
        'dddTelefone',
        'telefonePrincipal',
        'outrosContatos',
        'status',
        'idEnderecoDistribuidor',
        'idHorarioFuncionamento',
        'idTaxaEntrega',
        'idNovoHorarioFuncionamento',
    ];
    public $timestamps = false;
    const ATIVO = 1;
	const INATIVO = 2;
    const EXCLUIDO = 3;
    //relacionamento UM para Muitos Inverso
    public function enderecoDistribuidor()
    {
        return $this->belongsTo('App\EnderecoDistribuidor', 'idEnderecoDistribuidor');
    }
    public function horarioFuncionamento()
    {
        return $this->belongsTo('App\HorarioFuncionamento', 'idHorarioFuncionamento');
    }
    public function taxaEntrega()
    {
        return $this->belongsTo('App\TaxaEntrega', 'idTaxaEntrega');
    }
    public function novoHorarioFuncionamento()
    {
        return $this->belongsTo('App\NovoHorarioFuncionamento', 'idNovoHorarioFuncionamento');
    }
}
