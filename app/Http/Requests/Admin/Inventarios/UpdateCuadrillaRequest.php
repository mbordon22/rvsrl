<?php

namespace App\Http\Requests\Admin\Inventarios;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCuadrillaRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'empleados' => 'required|array'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la cuadrilla es obligatorio.',
            'empleados.required' => 'Debe seleccionar al menos un empleado para la cuadrilla.'
        ];
    }
}
