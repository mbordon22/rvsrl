<?php

namespace App\Http\Requests\Admin\GestionContable\Egresos;

use App\Enums\ComprobanteTipoContable;
use App\Enums\CondicionPagoContable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComprobanteEgresoRequest extends FormRequest
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
            'proveedor_id' => 'required|exists:proveedores,id',
            'condicion_pago' => ['required', Rule::in(array_map(fn($e) => $e->name, CondicionPagoContable::cases()))],
            'comprobante_tipo' => ['required', Rule::in(array_map(fn($e) => $e->name, ComprobanteTipoContable::cases()))],
            'numero_comprobante' => 'required|string|max:16',
            'fecha_comprobante' => 'required|string',
            'fecha_vencimiento' => 'nullable|string',
            'fecha_fiscal' => 'nullable|string',
            'medio_pago_id' => 'required|exists:medios_pago,id',
            'descripcion' => 'nullable|string|max:255',
            'productos_compra' => 'required|string'
        ];
    }
}
