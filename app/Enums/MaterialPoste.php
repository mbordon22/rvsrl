<?php

namespace App\Enums;

enum MaterialPoste: string
{
    case MADERA      = 'madera';
    case HORMIGON    = 'hormigon';
    case PRFV        = 'prfv';
    case REUTILIZADO = 'reutilizado';

    public function label(): string
    {
        return match ($this) {
            self::MADERA      => 'Madera',
            self::HORMIGON    => 'Hormigón',
            self::PRFV        => 'PRFV',
            self::REUTILIZADO => 'Reutilizado',
        };
    }

    /** @return array<string,string> value => label */
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            fn (array $acc, self $c) => $acc + [$c->value => $c->label()],
            []
        );
    }
}
