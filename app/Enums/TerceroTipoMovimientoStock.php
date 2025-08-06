<?php

namespace App\Enums;

enum TerceroTipoMovimientoStock
{
    case cliente;
    case contratista;
    case proveedor;

    public function label(): string
    {
        return match ($this) {
            self::cliente => 'Cliente',
            self::contratista => 'Contratista',
            self::proveedor => 'Proveedor'
        };
    }
}
