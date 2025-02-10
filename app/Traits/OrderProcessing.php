<?php

namespace App\Traits;
use App\Models\Pedido;
use App\Models\Administrador;
use App\Models\EnderecoCliente;
trait OrderProcessing
{
    protected function loadOrderProducts(&$queries)
    {
        foreach ($queries as $query) {
            $query->get()->map(function ($pedido) {
                $pedido = $this->formatPedidoDates($pedido);
                $pedido->produtos = $this->formatProductsOutput(
                    $pedido->itens->map(function ($item) {
                        return (object) [
                            'id' => $item->id,
                            'idProd' => $item->produto->id,
                            'nome' => $item->produto->nome,
                            'img' => $item->produto->img,
                            'qtdMin' => $item->qtd,
                            'valor' => $item->preco
                        ];
                    })->toArray()
                );
                unset($pedido->itens);
                return $pedido;
            });
        }
    }

    protected function formatProductsOutput($produtos)
    {
        if (empty($produtos[0]->idProd)) {
            return [];
        }

        $out = [];
        $currentProduct = null;
        $indexProduto = -1;

        foreach ($produtos as $prod) {
            if ($currentProduct !== $prod->idProd) {
                $indexProduto++;
                $currentProduct = $prod->idProd;
                $out[$indexProduto] = $this->createProductStructure($prod);
            }

            $out[$indexProduto]['preco'][] = $this->createPriceStructure($prod);
            $out[$indexProduto]['preco'] = $this->sortPriceArrayByPrecoId($out[$indexProduto]['preco']);

            if (!empty($out[$indexProduto]['precoEspecial'])) {
                $out[$indexProduto]['precoEspecial'] = $this->sortPriceArrayByPrecoId($out[$indexProduto]['precoEspecial']);
            } else {
                unset($out[$indexProduto]['precoEspecial']);
            }
        }

        return $out;
    }
    protected function createProductStructure($prod)
    {
        return [
            "nome" => $prod->nome,
            "id" => $prod->idProd,
            "img" => $prod->img,
            "preco" => [],
            "precoEspecial" => [],
        ];
    }

    protected function createPriceStructure($prod)
    {
        return [
            'precoId' => $prod->id,
            'qtd' => $prod->qtdMin,
            'val' => $prod->valor
        ];
    }
        /**
 * Formats all dates in order object
 * @param Pedido $pedido Order object
 * @return Pedido Order with formatted dates
 */
protected function formatPedidoDates($pedido) {
    $pedido->horarioPedido = $this->formatDateTime($pedido->horarioPedido);
    $pedido->horarioAceito = $this->formatDateTime($pedido->horarioAceito);
    $pedido->horarioDespache = $this->formatDateTime($pedido->horarioDespache);
    $pedido->horarioEntrega = $this->formatDateTime($pedido->horarioEntrega);
    $pedido->dataAgendada = $this->formatDateTime($pedido->dataAgendada);
    return $pedido;
}
    /**
 * Sorts price array by price ID
 * @param array $prices Price data
 * @return array Sorted prices
 */
private function sortPriceArrayByPrecoId(array $prices): array
{
    usort($prices, function ($a, $b) {
        return $a['precoId'] - $b['precoId'];
    });
    return $prices;
}
protected function notifyDistribuidorChange($pedido, $request, $cliente)
    {
        $administradores = Administrador::where('idDistribuidor', $pedido->idDistribuidor)
            ->orWhere('idDistribuidor', $request->idDistribuidor)
            ->orWhere('tipoAdministrador', 'Administrador');

        $endereco = EnderecoCliente::find($pedido->idEndereco);

        $msg = [
            'title' => 'Distribuidor Alterado: ' . $pedido->id . ' - ' . $cliente->nome,
            'body' => '[Distribuição ' . $pedido->idDistribuidor . ' para ' . $request->idDistribuidor . '] '
                . $endereco->logradouro . ' ' . $endereco->numero . ', '
                . $endereco->bairro . ' - ' . $endereco->cidade . '/' . $endereco->estado,
            'icon' => '/images/logo-icon.png',
            'click_action' => 'https://adm.tokumsede.com'
        ];

        $this->fcmService->sendOrderNotification($administradores, $msg);
    }
}
