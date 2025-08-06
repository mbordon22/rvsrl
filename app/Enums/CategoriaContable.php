<?php

namespace App\Enums;

enum CategoriaContable
{
    case Preimpreso;
    case Manual;
    case Electronico;

    public function label(): string
    {
        return match ($this) {
            self::Preimpreso => 'Preimpreso',
            self::Manual => 'Manual',
            self::Electronico => 'Electrónico',
        };
    }
}
