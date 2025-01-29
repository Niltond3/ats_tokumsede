<?php

namespace App\Http\Controllers;

use App\Models\DistributorStockUnion;
use App\Models\Distribuidor;
use App\Models\Estoque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockUnionController extends Controller
{
    /**
     * Cria uma nova união de estoques
     */
    public function createUnion(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'main_distributor_id' => 'required|exists:distribuidor,id',
                'secondary_distributor_ids' => 'required|array',
                'secondary_distributor_ids.*' => 'exists:distribuidor,id'
            ]);

            // Verifica se algum distribuidor já está em uma união
            $existingUnions = DistributorStockUnion::whereIn('secondary_distributor_id', $validated['secondary_distributor_ids'])
                ->orWhere('main_distributor_id', $validated['main_distributor_id'])
                ->exists();

            if ($existingUnions) {
                return response()->json([
                    'message' => 'Um ou mais distribuidores já fazem parte de uma união'
                ], 422);
            }

            // Cria as uniões e transfere os estoques
            foreach ($validated['secondary_distributor_ids'] as $secondaryId) {
                // Cria a união
                DistributorStockUnion::create([
                    'main_distributor_id' => $validated['main_distributor_id'],
                    'secondary_distributor_id' => $secondaryId
                ]);

                // Transfere e zera os estoques
                $this->transferStocks($secondaryId, $validated['main_distributor_id']);
            }

            DB::commit();
            return response()->json(['message' => 'União de estoques criada com sucesso']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao criar união: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Transfere os estoques do distribuidor secundário para o principal
     */
    private function transferStocks($fromDistributorId, $toDistributorId)
    {
        $secondaryStocks = Estoque::where('idDistribuidor', $fromDistributorId)->get();

        foreach ($secondaryStocks as $stock) {
            $mainStock = Estoque::firstOrCreate(
                [
                    'idDistribuidor' => $toDistributorId,
                    'idProduto' => $stock->idProduto
                ],
                ['quantidade' => 0]
            );

            // Soma o estoque
            $mainStock->quantidade += $stock->quantidade;
            $mainStock->save();

            // Zera o estoque secundário
            $stock->quantidade = 0;
            $stock->save();
        }
    }
}
