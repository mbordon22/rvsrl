<?php

namespace App\Enums;

enum ElementoRed: string
{
    case CDO           = 'cdo';
    case CAJA_TERMINAL = 'caja_terminal';
    case NAP           = 'nap';

    public function label(): string
    {
        return match ($this) {
            self::CDO           => 'CDO',
            self::CAJA_TERMINAL => 'Caja Terminal',
            self::NAP           => 'NAP',
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
