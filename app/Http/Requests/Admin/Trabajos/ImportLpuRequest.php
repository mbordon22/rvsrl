<?php

namespace App\Http\Requests\Admin\Trabajos;

use Illuminate\Foundation\Http\FormRequest;

class ImportLpuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo' => 'required|file|mimes:xlsx,xls|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Seleccioná un archivo Excel.',
            'archivo.mimes'    => 'El archivo debe ser formato Excel (.xlsx o .xls).',
            'archivo.max'      => 'El archivo no puede superar los 20MB.',
        ];
    }
}
