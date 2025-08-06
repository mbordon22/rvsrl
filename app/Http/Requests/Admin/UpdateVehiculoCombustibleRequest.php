<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehiculoCombustibleRequest extends FormRequest
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
            'tipo_combustible' => 'required|string|max:50',
            'litros' => 'required|decimal:0,2',
            'monto' => 'required|decimal:0,2',
            'archivo' => 'nullable|array', // Ensure the 'archivo' field is an array and required
            'archivo.*' => 'file|mimes:jpg,jpeg,png,pdf', // Validate each file in the array
            'fecha_carga' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $format = 'd/m/Y';
                    $parsedDate = \DateTime::createFromFormat($format, $value);
                    if (!$parsedDate || $parsedDate->format($format) !== $value) {
                        $fail("The $attribute must be a valid date in the format dd/mm/yyyy.");
                    }
                },
            ],
            'observaciones' => 'nullable|string|max:550',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_combustible.required' => 'El tipo de combustible es obligatorio.',
            'litros.required' => 'Los litros son obligatorios.',
            'monto.required' => 'El monto es obligatorio.',
            'km.required' => 'El km es obligatorio.',
            'archivo.*.file' => 'Cada archivo debe ser un archivo válido.',
            'archivo.*.mimes' => 'Cada archivo debe ser de tipo jpg, jpeg, png o pdf.',
            'fecha_carga.string' => 'La fecha de carga debe ser una cadena de texto.',
            'observaciones.string' => 'Las observaciones deben ser una cadena de texto.',
        ];
    }
}
