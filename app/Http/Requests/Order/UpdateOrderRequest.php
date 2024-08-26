<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $order = $this->route('order');

        // Asegúrate de que la política de actualización también permita la cancelación
        if ($this->input('status') === 'canceled') {
            return Gate::allows('update', $order);
        }

        // Para otros tipos de actualización
        return Gate::allows('update', $order);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'sometimes|required|in:pending,completed,shipped,canceled',
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
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser uno de los valores permitidos: pending, completed, shipped, canceled.',
        ];
    }
}
