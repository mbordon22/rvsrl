<?php

namespace App\Enums;

enum EstadoTrabajo: string
{
    case BORRADOR  = 'borrador';       // legado (registros viejos); los nuevos se crean como PENDIENTE
    case PENDIENTE = 'pendiente';
    case ENVIADO   = 'enviado';
    case APROBADO  = 'aprobado';
    case RECHAZADO = 'rechazado';

    public function label(): string
    {
        return match ($this) {
            self::BORRADOR  => 'Borrador',
            self::PENDIENTE => 'Pendiente de revisión',
            self::ENVIADO   => 'Enviado',
            self::APROBADO  => 'Aprobado',
            self::RECHAZADO => 'Rechazado',
        };
    }

    /** Clase de badge Bootstrap para el DataTable */
    public function badge(): string
    {
        return match ($this) {
            self::BORRADOR  => 'bg-secondary',
            self::PENDIENTE => 'bg-warning',
            self::ENVIADO   => 'bg-info',
            self::APROBADO  => 'bg-success',
            self::RECHAZADO => 'bg-danger',
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
