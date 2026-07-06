<?php

namespace App\Enums;

enum CentralTrabajo: string
{
    case CYO = 'CYO';
    case VLJ = 'VLJ';
    case KEN = 'KEN';
    case ALD = 'ALD';

    public function label(): string
    {
        return $this->value;
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
