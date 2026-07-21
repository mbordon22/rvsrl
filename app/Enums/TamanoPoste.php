<?php

namespace App\Enums;

enum TamanoPoste: string
{
    case T_7_5M = '7.5m';
    case T_9M   = '9m';
    case T_10M  = '10m';
    case T_11M  = '11m';
    case T_14M  = '14m';
    case OTROS  = 'otros';

    public function label(): string
    {
        return match ($this) {
            self::T_7_5M => '7,5 m',
            self::T_9M   => '9 m',
            self::T_10M  => '10 m',
            self::T_11M  => '11 m',
            self::T_14M  => '14 m',
            self::OTROS  => 'Otros',
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
