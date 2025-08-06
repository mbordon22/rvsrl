<?php

namespace App\Http\Requests\Admin\GestionContable\Ingresos;

use App\Enums\CondicionIva;
use App\Enums\TipoDocumentoContable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateClienteRequest extends FormRequest
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
            'codigo' => 'nullable|string|max:50',
            'tipo_documento' => [
                'required',
                Rule::in(array_map(fn($e) => $e->name, TipoDocumentoContable::cases())),
            ],
            'numero_documento' => 'required|string|max:50',
            'condicion_iva' => [
                'required',
                Rule::in(array_map(fn($e) => $e->name, CondicionIva::cases())),
            ],
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'state_id' => 'required|exists:states,id',
            'localidad' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
        ];
    }
}
