<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Aquí puedes definir la lógica para autorizar el acceso a esta solicitud.
        // En este caso, retornamos true para permitir el acceso.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id', // Opcional, pero si se proporciona, debe ser válido
            'total_amount' => 'sometimes|required|numeric|min:0', // Opcional, pero si se proporciona, debe ser un número positivo
            'status' => 'sometimes|required|in:pending,completed,shipped,canceled', // Opcional, pero si se proporciona, debe ser uno de los valores permitidos
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
            'total_amount.required' => 'El monto total es obligatorio.',
            'total_amount.numeric' => 'El monto total debe ser un número.',
            'total_amount.min' => 'El monto total debe ser mayor o igual a 0.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser uno de los valores permitidos: pending, completed, shipped, canceled.',
        ];
    }
}
