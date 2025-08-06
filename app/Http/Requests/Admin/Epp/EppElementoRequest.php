<?php

namespace App\Http\Requests\Admin\Epp;

use Illuminate\Foundation\Http\FormRequest;

class EppElementoRequest extends FormRequest
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
            'producto' => 'required|string|max:500',
            'tipo' => 'required|string|max:500',
            'marca' => 'nullable|string|max:500',
            'certificacion' => 'required|boolean',
            'observacion' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'talle' => 'nullable|string|max:200',
        ];
    }
}
