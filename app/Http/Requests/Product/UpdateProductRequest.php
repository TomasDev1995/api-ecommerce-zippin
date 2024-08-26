<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $this->route('product'),
            'image_url' => 'nullable|url',
            'is_active' => 'sometimes|required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'stock.required' => 'La cantidad en stock es obligatoria.',
            'stock.integer' => 'La cantidad en stock debe ser un número entero.',
            'sku.unique' => 'El SKU debe ser único.',
            'is_active.required' => 'El estado del producto es obligatorio.',
        ];
    }
}
