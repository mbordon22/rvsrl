<?php

namespace App\Enums;

enum CondicionIva
{
    case ResponsableInscripto;
    case Monotributista;
    case Exento;
    case ConsumidorFinal;
    case Otro;

    public function label(): string
    {
        return match ($this) {
            self::ResponsableInscripto => 'Responsable Inscripto',
            self::Monotributista => 'Monotributista',
            self::Exento => 'Exento',
            self::ConsumidorFinal => 'Consumidor Final',
            self::Otro => 'Otro',
        };
    }
}
