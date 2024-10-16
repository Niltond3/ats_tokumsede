<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioFuncionamento extends Model
{
    use HasFactory;
    protected $table = 'horarioFuncionamento';
    protected $fillable = [
        'inicioSemana',
        'fimSemana',
        'inicioSabado',
        'fimSabado',
        'domingo',
        'inicioDomingo',
        'fimDomingo'
    ];
    public $timestamps = false;
    //
}
