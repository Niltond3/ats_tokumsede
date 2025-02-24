<?php

namespace App\Actions\Products;

use App\Models\Produto;
use Illuminate\Http\Request;

class UpdateProductAction
{
    public function execute(Request $request, Produto $produto): Produto
    {
        $produto->fill($request->validated());

        if ($request->has('img')) {
            $produto->img = $request->img;
        }

        $produto->save();

        if ($request->has('itensComposicao')) {
            $this->updateComposition($produto, $request->itensComposicao);
        }

        return $produto;
    }

    private function handleImageUpload($image): string
    {
        $path = $image->store('produtos', 'public');
        return $path;
    }

    private function updateComposition(Produto $produto, array $items): void
    {
        $produto->composicoes()->delete();

        foreach ($items as $item) {
            $parts = explode('-', $item);
            $produto->composicoes()->create([
                'idComponente' => $parts[0],
                'quantidade' => $parts[1]
            ]);
        }
    }
}
