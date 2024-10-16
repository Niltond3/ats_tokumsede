<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxaEntrega extends Model
{
    use HasFactory;
    protected $table = 'taxaEntrega';
    protected $fillable = [
        "taxaUnica",
        "valorTaxaUnica",
        "taxaDomingo",
        "valorTaxaDomingo",
        "taxaCompraMinima",
        "valorCompraMinima",
        "taxaEntregaDistante",
        "distanciaMaxima",
        "valorKmAdicional"
    ];
    public $timestamps = false;
    //
}
