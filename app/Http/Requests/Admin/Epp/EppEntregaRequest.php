<?php

namespace App\Http\Requests\Admin\Epp;

use Illuminate\Foundation\Http\FormRequest;

class EppEntregaRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'elementos_entrega' => 'required|string'
        ];
    }
}
