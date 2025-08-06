<?php

namespace App\Enums;

enum Moneda
{
    case ARS;
    case USD;
    case EUR;

    public function label(): string
    {
        return match ($this) {
            self::ARS => 'Peso Argentino',
            self::USD => 'Dólar Estadounidense',
            self::EUR => 'Euro',
        };
    }
}
