<?php

namespace App\Enums;

enum LetraContable
{
    case A;
    case B;
    case C;
    case X;

    public function label(): string
    {
        return match ($this) {
            self::A => 'A',
            self::B => 'B',
            self::C => 'C',
            self::X => 'X',
        };
    }
}
