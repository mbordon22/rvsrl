<?php

namespace App\Enums;

enum ComprobanteTipoContable
{
    case Factura;
    case Recibo;
    case NotaDeDebito;
    case NotaDeCredito;
    case Ticket;
    case OtroComprobante;

    public function label(): string
    {
        return match ($this) {
            self::Factura => 'Factura',
            self::Recibo => 'Recibo',
            self::NotaDeDebito => 'Nota de Débito',
            self::NotaDeCredito => 'Nota de Crédito',
            self::Ticket => 'Ticket',
            self::OtroComprobante => 'Otro Comprobante',
        };
    }
}
