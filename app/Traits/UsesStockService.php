<?php

namespace App\Traits;

use App\Services\StockService;

trait UsesStockService
{
    protected StockService $stockService;

    protected function initializeStockService()
    {
        $this->stockService = app(StockService::class);
    }

    protected function atualizaEstoque($distributorId, $produto, $quantidade, $adicionar)
    {
        return $this->stockService->atualizaEstoque($distributorId, $produto, $quantidade, $adicionar);
    }

    protected function atualizaComposicoes($distributorId)
    {
        return $this->stockService->atualizaComposicoes($distributorId);
    }
}
