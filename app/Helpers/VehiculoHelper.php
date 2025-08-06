<?php

namespace App\Helpers;

class VehiculoHelper
{
    public static function getTiposDocumentos()
    {
        return [
            'poliza_seguro' => 'Poliza de Seguro',
            'vtv' => 'VTV',
            'oblea' => 'Oblea',
            'titulo' => 'Titulo',
            'cedula' => 'Cedula',
            'permiso_circular' => 'Permiso Circular',
        ];
    }
}
