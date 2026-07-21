<?php

namespace App\Models;

use App\Support\ReglaMaterialCampos;
use Illuminate\Database\Eloquent\Model;

class MaterialRegla extends Model
{
    protected $table = 'material_reglas';

    protected $fillable = [
        'descripcion', 'condicion_campo', 'condicion_valor',
        'material_id', 'cantidad_base', 'cantidad_campo', 'activo',
    ];

    protected $casts = [
        'activo'        => 'boolean',
        'cantidad_base' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    /** Etiqueta del grupo (el "disparador") para el listado agrupado. */
    public function grupoLabel(): string
    {
        return ReglaMaterialCampos::labelCampo($this->condicion_campo);
    }

    /** Texto legible de la condición: "Colocó poste" / "Tipo de poste: Terminal". */
    public function condText(): string
    {
        return ReglaMaterialCampos::textoCondicion($this->condicion_campo, $this->condicion_valor);
    }

    /** Texto legible de la cantidad: "2 fijas" / "1 × por cada NAP". */
    public function qtyText(): string
    {
        $base = $this->cantidadBaseLegible();

        if ($this->cantidad_campo) {
            return $base . ' × por cada ' . (ReglaMaterialCampos::labelNumerico($this->cantidad_campo) ?? $this->cantidad_campo);
        }

        return $base . ($base === '1' ? ' fija' : ' fijas');
    }

    /** Cantidad base sin ceros decimales sobrantes ("1", "2,5"). */
    public function cantidadBaseLegible(): string
    {
        $n = (float) $this->cantidad_base;
        if ($n == (int) $n) {
            return (string) (int) $n;
        }
        return rtrim(rtrim(number_format($n, 2, ',', ''), '0'), ',');
    }
}
