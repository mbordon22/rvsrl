<?php

namespace App\Enums;

enum TamanoPoste: string
{
    case T_7_5M   = '7.5m';
    case T_9_10M  = '9-10m';
    case T_11_14M = '11-14m';
    case OTROS    = 'otros';

    public function label(): string
    {
        return match ($this) {
            self::T_7_5M   => '7,5 m',
            self::T_9_10M  => '9 a 10 m',
            self::T_11_14M => '11 a 14 m',
            self::OTROS    => 'Otros',
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
