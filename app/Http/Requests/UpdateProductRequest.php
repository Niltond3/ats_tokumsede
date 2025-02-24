<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'idCategoria' => 'required|exists:categoria,id',
            'img' => 'nullable|string|max:255',
            'composicao' => 'boolean',
            'itensComposicao' => 'array|required_if:composicao,1',
            'itensComposicao.*' => 'string|regex:/^\d+-\d+$/'
        ];
    }
}
