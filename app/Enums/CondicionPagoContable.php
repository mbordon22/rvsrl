<?php

namespace App\Enums;

enum CondicionPagoContable
{
    case Contado;
    case CuentaCorriente;

    public function label(): string
    {
        return match ($this) {
            self::Contado => 'Contado',
            self::CuentaCorriente => 'Cuenta Corriente',
        };
    }
}
