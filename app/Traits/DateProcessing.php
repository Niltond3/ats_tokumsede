<?php

namespace App\Traits;

trait DateProcessing
{
    protected function applyDateFilters(&$queries, $request)
    {
        if ($request->dataInicial) {
            $dataInicial = implode("-", array_reverse(explode("/", $request->dataInicial)));
            $whereClause = "horarioPedido >= '$dataInicial 00:00:00'";
            foreach ($queries as $query) {
                $query->whereRaw($whereClause);
            }
        }

        if ($request->dataFinal) {
            $dataFinal = implode("-", array_reverse(explode("/", $request->dataFinal)));
            $whereClause = "horarioPedido <= '$dataFinal 23:59:59'";
            foreach ($queries as $query) {
                $query->whereRaw($whereClause);
            }
        }
    }
    private function formatDateTime($datetime) {
        return date('d/m/Y H:i', strtotime($datetime));
    }
}
