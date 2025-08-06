<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehiculoRequest extends FormRequest
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
        $id = $this->route('vehiculo') ? $this->route('vehiculo')->id : $this->id;
        return [
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'ano' => ['required', 'integer', 'digits:4'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'patente' => ['required', 'string', 'max:255', "unique:vehiculos,patente,{$id},id,deleted_at,NULL"],
            'tipo_vehiculo' => ['required', 'exists:tipos_vehiculos,id'],
            'tipo_combustible' => ['required', 'exists:tipos_combustibles,id'],
            'mas_informacion' => ['nullable', 'string'],
            'identificador_vehiculo' => ['required', 'string', 'max:255', "unique:vehiculos,identificador_vehiculo,{$id},id,deleted_at,NULL"],
        ];
    }
}
