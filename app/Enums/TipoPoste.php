<?php

namespace App\Enums;

enum TipoPoste: string
{
    case TERMINAL = 'terminal';
    case PASANTE  = 'pasante';

    public function label(): string
    {
        return match ($this) {
            self::TERMINAL => 'Terminal',
            self::PASANTE  => 'Pasante',
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
