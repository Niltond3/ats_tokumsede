<?php

namespace App\Models;

use App\Enums\ReminderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_cliente',
        'descricao',
        'data_criacao',
        'data_limite',
        'status'
    ];

    protected $casts = [
        'status' => ReminderStatus::class,
        'data_criacao' => 'datetime',
        'data_limite' => 'date'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}
