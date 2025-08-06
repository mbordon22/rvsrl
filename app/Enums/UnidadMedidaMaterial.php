<?php

namespace App\Enums;

enum UnidadMedidaMaterial
{
    case UNIDAD;
    case KILOGRAMO;
    case METRO;
    case LITRO;
    case PIEZA;
    case ROLLO;
    case BOBINA;
    case KIT;
    case OTRO;

    public function label(): string
    {
        return match ($this) {
            self::UNIDAD => 'Unidad',
            self::KILOGRAMO => 'Kilogramo',
            self::METRO => 'Metro',
            self::LITRO => 'Litro',
            self::PIEZA => 'Pieza',
            self::ROLLO => 'Rollo',
            self::BOBINA => 'Bobina',
            self::KIT => 'Kit',
            self::OTRO => 'Otro',
        };
    }
}
