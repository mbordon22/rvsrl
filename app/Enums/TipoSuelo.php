<?php

namespace App\Enums;

enum TipoSuelo: string
{
    case TIERRA     = 'tierra';
    case CONTRAPISO = 'contrapiso';
    case RIPIO      = 'ripio';
    case OS         = 'os';

    public function label(): string
    {
        return match ($this) {
            self::TIERRA     => 'Tierra',
            self::CONTRAPISO => 'Contrapiso',
            self::RIPIO      => 'Ripio',
            self::OS         => 'OS',
        };
    }

    /** ¿Este suelo habilita la pregunta de reparación de vereda? */
    public function requiereRepVereda(): bool
    {
        return in_array($this, [self::CONTRAPISO, self::OS], true);
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
