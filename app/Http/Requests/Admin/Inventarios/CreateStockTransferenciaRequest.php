<?php

namespace App\Http\Requests\Admin\Inventarios;

use Illuminate\Foundation\Http\FormRequest;

class CreateStockTransferenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'almacen_origen_id' => 'nullable|exists:almacenes,id',
            'cuadrilla_origen_id' => 'nullable|exists:cuadrillas,id',
            'almacen_destino_id' => 'nullable|exists:almacenes,id',
            'cuadrilla_destino_id' => 'nullable|exists:cuadrillas,id',
            'materiales_transferencia' => 'required|string',
            'fecha' => 'required|string',
        ];
    }
}
