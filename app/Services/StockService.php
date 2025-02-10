<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Composicao;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function atualizaEstoque($distributorId, $produto, $quantidade, $adicionar)
    {
        $estoque = Estoque::firstOrCreate(
            ['idDistribuidor' => $distributorId, 'idProduto' => $produto->id],
            ['quantidade' => 0]
        );

        $estoque->quantidade += $adicionar ? $quantidade : -$quantidade;
        $estoque->save();

        if ($produto->componente) {
            $this->atualizaComponentes($distributorId, $produto, $quantidade, $adicionar);
        }
    }

    public function atualizaComposicoes($distributorId)
    {
        $composicoes = Composicao::query()
            ->join('produto', 'composicao.idComposicao', '=', 'produto.id')
            ->where('produto.status', '!=', 3)
            ->select('composicao.*')
            ->get();

        foreach ($composicoes as $composicao) {
            $this->updateComposicaoEstoque($distributorId, $composicao);
        }
    }

    private function atualizaComponentes($distributorId, $produto, $quantidade, $adicionar)
    {
        $componentes = Composicao::where('idComposicao', $produto->id)->get();

        foreach ($componentes as $componente) {
            $produtoComponente = Produto::find($componente->idComponente);
            $this->atualizaEstoque(
                $distributorId,
                $produtoComponente,
                $quantidade * $componente->quantidade,
                $adicionar
            );
        }
    }

    private function updateComposicaoEstoque($distributorId, $composicao)
    {
        $estoqueComponente = Estoque::where([
            'idDistribuidor' => $distributorId,
            'idProduto' => $composicao->idComponente
        ])->first();

        if ($estoqueComponente) {
            $quantidadeComposicao = floor($estoqueComponente->quantidade / $composicao->quantidade);

            Estoque::updateOrCreate(
                ['idDistribuidor' => $distributorId, 'idProduto' => $composicao->idComposicao],
                ['quantidade' => $quantidadeComposicao]
            );
        }
    }
}
