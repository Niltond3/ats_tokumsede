<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovoHorarioFuncionamento extends Model
{
    use HasFactory;

    protected $table = 'novoHorarioFuncionamento';
    protected $fillable = [
        'domingo',
        'inicioDomingo',
        'fimDomingo',
        'segunda',
        'inicioSegunda',
        'fimSegunda',
        'terca',
        'inicioTerca',
        'fimTerca',
        'quarta',
        'inicioQuarta',
        'fimQuarta',
        'quinta',
        'inicioQuinta',
        'fimQuinta',
        'sexta',
        'inicioSexta',
        'fimSexta',
        'sabado',
        'inicioSabado',
        'fimSabado',
        'pausaAlmoco',
        'inicioAlmoco',
        'fimAlmoco'
    ];
    public $timestamps = false;
    //
}
