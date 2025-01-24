<?php

namespace App\Http\Resources;

use \Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    public function toArray($request): array
    {
        Debugbar::info($this);
        return [
            'id' => $this->id,
            'descricao' => $this->descricao,
            'data_criacao' => $this->data_criacao->format('Y-m-d H:i:s'),
            'data_limite' => $this->data_limite ? $this->data_limite->format('Y-m-d') : null,
            'status' => $this->status->value,
            'cliente' => [
                'id' => $this->id_cliente,
                'nome' => $this->cliente->nome
            ]
        ];
    }
}
