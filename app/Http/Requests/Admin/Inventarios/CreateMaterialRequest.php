<?php

namespace App\Http\Requests\Admin\Inventarios;

use Illuminate\Foundation\Http\FormRequest;

class CreateMaterialRequest extends FormRequest
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
            'codigo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'descripcion_larga' => 'nullable|string|max:1000',
            'unidad_medida' => 'required|in:' . implode(',', array_map(fn($case) => $case->name, \App\Enums\UnidadMedidaMaterial::cases()))
            // Add other validation rules as necessary
        ];
    }
}
