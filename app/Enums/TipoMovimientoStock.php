<?php

namespace App\Enums;

enum TipoMovimientoStock
{
    case ingreso;
    case egreso;
    case traslado;
    case ajuste;

    public function label(): string
    {
        return match ($this) {
            self::ingreso => 'Ingreso',
            self::egreso => 'Egreso',
            self::traslado => 'Traslado',
            self::ajuste => 'Ajuste',
        };
    }
}