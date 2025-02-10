<?php

namespace App\Actions\Enderecos;

use App\Models\EnderecoCliente;
use App\Traits\AddressProcessing;
use App\Traits\AddressManagement;
use Illuminate\Http\Request;

class UpdateEnderecoAction
{
    use AddressProcessing, AddressManagement;

    public function execute(Request $request, $id)
    {
        $enderecoCliente = EnderecoCliente::find($id);
        $enderecoCliente = $this->handleAddressData($enderecoCliente, $request);
        $enderecoCliente->update(array_filter($request->all()));

        return response('O endereço ' . $enderecoCliente->id, 200);
    }
}
