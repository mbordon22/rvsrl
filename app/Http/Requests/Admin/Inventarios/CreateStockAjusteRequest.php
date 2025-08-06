<?php

namespace App\Http\Requests\Admin\Inventarios;

use Illuminate\Foundation\Http\FormRequest;

class CreateStockAjusteRequest extends FormRequest
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
            'fecha' => 'required|string',
            'materiales_ajuste' => 'required|string',
            'origen' => 'required|in:almacen,cuadrilla',
            'almacen_ajuste_id' => 'nullable|exists:almacenes,id',
            'cuadrilla_ajuste_id' => 'nullable|exists:cuadrillas,id',
            'observaciones' => 'nullable|string|max:255',
        ];
    }
}
