<?php

namespace App\Actions\Enderecos;

use App\Models\EnderecoCliente;
use App\Traits\AddressProcessing;
use App\Traits\AddressManagement;
use Illuminate\Http\Request;

class StoreEnderecoAction
{
    use AddressProcessing, AddressManagement;

    public function execute(Request $request)
    {
        $enderecoCliente = new EnderecoCliente($request->all());
        $enderecoCliente = $this->handleAddressData($enderecoCliente, $request);
        $enderecoCliente->status = EnderecoCliente::ATIVO;

        if ($enderecoCliente->save()) {
            return $enderecoCliente->id;
        }

        return response("Erro ao cadastrar o endereço. Tente novamente ou contate o suporte.", 400);
    }
}
