<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\EnderecoDistribuidor;
use App\Models\HorarioFuncionamento;
use App\Models\TaxaEntrega;
use App\Models\NovoHorarioFuncionamento;

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
    'pix_key',
    ];
    public $timestamps = false;
    const ATIVO = 1;
    const INATIVO = 2;
    const EXCLUIDO = 3;
    public function stockUnionsAsMain()
{
    return $this->hasMany(DistributorStockUnion::class, 'main_distributor_id');
}

public function stockUnionAsSecondary()
{
    return $this->hasOne(DistributorStockUnion::class, 'secondary_distributor_id');
}

public function getMainDistributorIdAttribute()
{
    $union = $this->stockUnionAsSecondary()->first();
    return $union ? $union->main_distributor_id : null;
}

public function isPartOfUnion()
{
    return $this->stockUnionsAsMain()->exists() || $this->stockUnionAsSecondary()->exists();
}
    //relacionamento UM para Muitos Inverso
    public function enderecoDistribuidor(): BelongsTo
    {
        return $this->belongsTo(EnderecoDistribuidor::class, 'idEnderecoDistribuidor');
    }
    public function horarioFuncionamento()
    {
        return $this->belongsTo(HorarioFuncionamento::class, 'idHorarioFuncionamento');
    }
    public function taxaEntrega()
    {
        return $this->belongsTo(TaxaEntrega::class, 'idTaxaEntrega');
    }
    public function novoHorarioFuncionamento()
    {
        return $this->belongsTo(NovoHorarioFuncionamento::class, 'idNovoHorarioFuncionamento');
    }
}
