<?php

namespace App\Enums;

enum ClaseContable
{
    case Recibo;
    case NotaDebito;
    case NotaCredito;
    case Factura;

    public function label(): string
    {
        return match ($this) {
            self::Recibo => 'Recibo',
            self::NotaDebito => 'Nota de Débito',
            self::NotaCredito => 'Nota de Crédito',
            self::Factura => 'Factura',
        };
    }
}
