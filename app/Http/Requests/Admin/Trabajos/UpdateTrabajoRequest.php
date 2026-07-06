<?php

namespace App\Http\Requests\Admin\Trabajos;

use App\Enums\CentralTrabajo;
use App\Enums\ElementoRed;
use App\Enums\MaterialPoste;
use App\Enums\MaterialReutilizado;
use App\Enums\TamanoPoste;
use App\Enums\TipoPoste;
use App\Enums\TipoRienda;
use App\Enums\TipoSuelo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrabajoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha'                      => 'required|date',
            'cuadrilla_id'               => 'nullable|exists:cuadrillas,id',
            'vehiculo_id'                => 'nullable|exists:vehiculos,id',
            'domicilio'                  => 'nullable|string|max:255',

            'central'                    => ['nullable', Rule::enum(CentralTrabajo::class)],
            'central_aclarar'            => 'nullable|string|max:100',
            'armario'                    => 'nullable|string|max:50',

            'tipo_poste'                 => ['nullable', Rule::enum(TipoPoste::class)],

            'desmonto_poste'             => 'nullable|boolean',

            'coloco_poste'               => 'nullable|boolean',
            'poste_material'             => ['nullable', 'required_if:coloco_poste,1', Rule::enum(MaterialPoste::class)],
            'poste_reutilizado_material' => ['nullable', 'required_if:poste_material,reutilizado', Rule::enum(MaterialReutilizado::class)],
            'tamano_poste'               => ['nullable', Rule::enum(TamanoPoste::class)],

            'elemento_tipo'              => ['nullable', Rule::enum(ElementoRed::class)],
            'elemento_cantidad'          => 'nullable|integer|min:0|max:9999|required_with:elemento_tipo',

            'sifon'                      => 'nullable|boolean',
            'sifon_cables'               => 'nullable|integer|min:0|max:9999',
            'protecciones_cantidad'      => 'nullable|integer|min:0|max:9999',

            'rienda'                     => 'nullable|boolean',
            'rienda_tipo'                => ['nullable', 'required_if:rienda,1', Rule::enum(TipoRienda::class)],

            'tipo_suelo'                 => ['nullable', Rule::enum(TipoSuelo::class)],
            'rep_vereda'                 => 'nullable|boolean',

            'poda'                       => 'nullable|boolean',
            'retensado'                  => 'nullable|boolean',

            'bajadas'                    => 'nullable|boolean',
            'bajadas_cantidad'           => 'nullable|integer|min:0|max:9999',

            'observaciones'              => 'nullable|string',

            'empleados'                  => 'nullable|array',
            'empleados.*'                => 'integer|exists:users,id',

            'fotos_antes'                => 'nullable|array',
            'fotos_antes.*'              => 'image|mimes:jpeg,png,webp|max:8192',
            'fotos_despues'              => 'nullable|array',
            'fotos_despues.*'            => 'image|mimes:jpeg,png,webp|max:8192',
        ];
    }
}
