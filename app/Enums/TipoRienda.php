<?php

namespace App\Enums;

enum TipoRienda: string
{
    case TIERRA = 'tierra';
    case PIQUE  = 'pique';
    case PLUMA  = 'pluma';

    public function label(): string
    {
        return match ($this) {
            self::TIERRA => 'Rienda a Tierra',
            self::PIQUE  => 'Rienda a Pique',
            self::PLUMA  => 'Rienda a Pluma',
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
