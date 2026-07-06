<?php

namespace App\Http\Requests\Admin\Inventarios;

use Illuminate\Foundation\Http\FormRequest;

class ImportMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo' => 'required|file|mimes:xlsx,xls|max:51200',
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Seleccioná un archivo Excel.',
            'archivo.mimes'    => 'El archivo debe ser formato Excel (.xlsx o .xls).',
            'archivo.max'      => 'El archivo no puede superar los 50MB.',
        ];
    }
}
