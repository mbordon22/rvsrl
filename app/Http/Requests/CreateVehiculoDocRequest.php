<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateVehiculoDocRequest extends FormRequest
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
            'tipo_documento' => 'required|string|max:255',
            'archivo' => 'nullable|array', // Ensure the 'archivo' field is an array and required
            'archivo.*' => 'file|mimes:jpg,jpeg,png,pdf', // Validate each file in the array
            'fecha_vencimiento' => [
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
        ];
    }

    public function messages()
    {
        return [
            'tipo_documento.required' => 'El campo tipo de documento es obligatorio.',
            'archivo.*.file' => 'Cada archivo debe ser un archivo válido.',
            'archivo.*.mimes' => 'Los archivos deben ser de tipo: jpg, jpeg, png, pdf.',
            'fecha_vencimiento.string' => 'La fecha de vencimiento debe ser una cadena de texto.',
        ];
    }
}
