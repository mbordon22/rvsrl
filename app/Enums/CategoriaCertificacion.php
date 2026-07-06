<?php

namespace App\Enums;

enum CategoriaCertificacion: string
{
    case MANTENIMIENTO = 'mantenimiento';
    case OBRAS         = 'obras';

    public function label(): string
    {
        return match ($this) {
            self::MANTENIMIENTO => 'Mantenimiento',
            self::OBRAS         => 'Obras',
        };
    }

    /** Columna de precio del LPU que corresponde a esta categoría */
    public function campoPrecio(): string
    {
        return match ($this) {
            self::MANTENIMIENTO => 'precio_mantenimiento',
            self::OBRAS         => 'precio_obras',
        };
    }

    /** Valor que va en la columna M de la hoja DETALLE */
    public function detalle(): string
    {
        return match ($this) {
            self::MANTENIMIENTO => 'MANT',
            self::OBRAS         => 'OBRA',
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
