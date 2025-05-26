<?php

namespace App\Http\Controllers;

use Barryvdh\Debugbar\Facades\Debugbar;

use App\Actions\Enderecos\{
    StoreEnderecoAction,
    ShowEnderecoAction,
    UpdateEnderecoAction,
    UpdateCoordinatesAction
};
use Illuminate\Http\Request;

class EnderecoClienteController extends Controller
{
    private $actions;

    public function __construct(
        StoreEnderecoAction $storeEndereco,
        ShowEnderecoAction $showEndereco,
        UpdateEnderecoAction $updateEndereco,
        UpdateCoordinatesAction $updateCoordinates
    ) {
        $this->actions = compact(
            'storeEndereco',
            'showEndereco',
            'updateEndereco',
            'updateCoordinates'
        );
    }

    public function store(Request $request)
    {
                Debugbar::info($request);

        return $this->actions['storeEndereco']->execute($request);
    }

    public function show($id)
    {
        return $this->actions['showEndereco']->execute($id);
    }

    public function update(Request $request, $id)
    {
        return $this->actions['updateEndereco']->execute($request, $id);
    }
    public function updateCoordinates(Request $request, $id)
    {
    return $this->actions['updateCoordinates']->execute($request, $id);
    }
}
