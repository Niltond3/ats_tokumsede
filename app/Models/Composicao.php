<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Composicao extends Model
{
    use HasFactory;
    protected $table = 'composicao';
    protected $fillable = [
        'id',
        'idComposicao',
        'idComponente',
        'quantidade'
    ];
    public $timestamps = false;
}
