<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', Order::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_number' => 'required|unique:orders',
            'status' => 'required|in:pending,completed,shipped,canceled',
            'total_amount' => 'required|numeric|min:0',
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'order_date' => 'required|date',
            'shipping_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => 'El campo usuario es obligatorio.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'order_number.required' => 'El número de orden es obligatorio.',
            'order_number.unique' => 'El número de orden ya ha sido registrado.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser uno de los valores permitidos: pending, completed, shipped, canceled.',
            'total_amount.required' => 'El monto total es obligatorio.',
            'total_amount.numeric' => 'El monto total debe ser un número.',
            'total_amount.min' => 'El monto total debe ser mayor o igual a 0.',
            'shipping_address.required' => 'La dirección de envío es obligatoria.',
            'billing_address.required' => 'La dirección de facturación es obligatoria.',
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_status.required' => 'El estado del pago es obligatorio.',
            'order_date.required' => 'La fecha del pedido es obligatoria.',
            'order_date.date' => 'La fecha del pedido debe ser una fecha válida.',
            'shipping_date.date' => 'La fecha de envío debe ser una fecha válida.',
            'products.required' => 'Los productos son obligatorios.',
            'products.array' => 'Los productos deben ser un arreglo.',
            'products.*.product_id.required' => 'El ID del producto es obligatorio.',
            'products.*.product_id.exists' => 'El producto especificado no existe.',
            'products.*.quantity.required' => 'La cantidad del producto es obligatoria.',
            'products.*.quantity.integer' => 'La cantidad del producto debe ser un número entero.',
            'products.*.quantity.min' => 'La cantidad del producto debe ser al menos 1.',
            'products.*.price.required' => 'El precio del producto es obligatorio.',
            'products.*.price.numeric' => 'El precio del producto debe ser un número.',
            'products.*.price.min' => 'El precio del producto debe ser mayor o igual a 0.',
        ];
    }
}
