<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use App\Models\User;
use App\Models\VehiculoDoc;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarAlertasVencimientoDocumentos extends Command
{
    protected $signature = 'alertas:vencimiento-documentos';
    protected $description = 'Genera notificaciones para los documentos de vehículos cuya fecha de vencimiento es hoy';

    public function handle()
    {
        $hoy = Carbon::today();

        $documentos = VehiculoDoc::with('vehiculo')
            ->where('genera_alerta', 1)
            ->where('estado', 1)
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', $hoy)
            ->get();

        if ($documentos->isEmpty()) {
            $this->info('No hay documentos que venzan hoy con alerta activa.');
            return self::SUCCESS;
        }

        // Destinatarios: usuarios con permiso de vehículos.
        $destinatarios = User::permission('vehiculo.index')->get();

        if ($destinatarios->isEmpty()) {
            $this->warn('No hay usuarios con permiso vehiculo.index para notificar.');
            return self::SUCCESS;
        }

        $generadas = 0;

        foreach ($documentos as $doc) {
            $vehiculo = $doc->vehiculo;
            $descripcionVehiculo = $vehiculo
                ? trim("{$vehiculo->marca} {$vehiculo->modelo} - Patente: {$vehiculo->patente}")
                : "ID {$doc->vehiculo_id}";

            $titulo = 'Vencimiento de documentación';
            $mensaje = "El documento \"{$doc->tipo_documento}\" del vehículo {$descripcionVehiculo} vence hoy.";
            $url = route('admin.vehiculo.documento.index', $doc->vehiculo_id);

            foreach ($destinatarios as $usuario) {
                $notificacion = Notificacion::firstOrCreate(
                    [
                        'notificable_type' => VehiculoDoc::class,
                        'notificable_id' => $doc->id,
                        'user_id' => $usuario->id,
                        'tipo' => 'vencimiento_documento',
                        'fecha_programada' => $doc->fecha_vencimiento,
                    ],
                    [
                        'titulo' => $titulo,
                        'mensaje' => $mensaje,
                        'url' => $url,
                    ]
                );

                if ($notificacion->wasRecentlyCreated) {
                    $generadas++;
                }
            }
        }

        $this->info("Se generaron {$generadas} notificación(es) de vencimiento.");

        return self::SUCCESS;
    }
}
