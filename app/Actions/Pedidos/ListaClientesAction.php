<?php

namespace App\Actions\Pedidos;

use App\Models\Cliente;

class ListaClientesAction extends BasePedidoAction
{
    public function execute()
    {
        return Cliente::with('enderecos')
            ->where('status', 1)
            ->select([
                'id',
                'nome',
                'tipoPessoa',
                'cpf',
                'cnpj',
                'precoAcertado',
                'dddTelefone',
                'telefone',
                'outrosContatos',
                'status',
                'email',
                'rating'
            ])
            ->get();
    }
}
