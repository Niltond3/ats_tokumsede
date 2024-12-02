<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    protected $fillable = [
        'id', 'nome', 'dataCadatro', 'status',
    ];
    public $timestamps = false;
    //STATUS
	const ATIVO = 1;
	const INATIVO = 2;
    const EXCLUIDO = 3;
    //
}
