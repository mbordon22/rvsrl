<?php

namespace App\Enums;

enum TipoDocumentoContable
{
    case CUIT;
    case CUIL;
    case DNI;
    case Otro;

    public function label(): string
    {
        return match ($this) {
            self::CUIT => 'CUIT',
            self::CUIL => 'CUIL',
            self::DNI => 'DNI',
            self::Otro => 'Otro',
        };
    }
}
