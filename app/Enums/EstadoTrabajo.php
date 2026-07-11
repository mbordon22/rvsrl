<?php

namespace App\Enums;

enum EstadoTrabajo: string
{
    case PENDIENTE = 'pendiente';
    case APROBADO  = 'aprobado';
    case CERTIFICADO = 'certificado';

    public function label(): string
    {
        return match ($this) {
            self::CERTIFICADO  => 'Certificado',
            self::PENDIENTE => 'Pendiente de revisión',
            self::APROBADO  => 'Aprobado'
        };
    }

    /** Clase de badge Bootstrap para el DataTable */
    public function badge(): string
    {
        return match ($this) {
            self::CERTIFICADO  => 'bg-secondary',
            self::PENDIENTE => 'bg-warning',
            self::APROBADO  => 'bg-success',
        };
    }

    /** Color hex de la pill de estado (según el diseño del listado) */
    public function color(): string
    {
        return match ($this) {
            self::CERTIFICADO  => '#8794a8',
            self::PENDIENTE => '#f0a020',
            self::APROBADO  => '#2ba95f',
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
