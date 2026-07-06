<?php

namespace App\Http\Requests\Admin\Trabajos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLpuTipoTrabajoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'codigo_lpu'           => 'required|string|max:30|unique:lpu_tipos_trabajo,codigo_lpu,' . $id,
            'codigo_telecom'       => 'nullable|string|max:30',
            'descripcion'          => 'required|string|max:255',
            'unidad'               => 'required|string|max:20',
            'precio_mantenimiento' => 'required|numeric|min:0',
            'precio_obras'         => 'required|numeric|min:0',
            'vigencia_desde'       => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_lpu.unique' => 'El código LPU ya existe en el sistema.',
        ];
    }
}
