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
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser uno de los valores permitidos: pending, completed, shipped, canceled.',
        ];
    }
}
