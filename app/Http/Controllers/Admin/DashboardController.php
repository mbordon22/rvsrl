<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoTrabajo;
use App\Http\Controllers\Controller;
use App\Models\Importacion;
use App\Models\LpuTipoTrabajo;
use App\Models\Trabajo;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Permiso para ver el dashboard. Como es la "home" del panel y está
        // enlazada desde breadcrumbs/logo, en vez de un 403 duro redirigimos con
        // gracia a la home que sí puede ver el usuario (p.ej. un técnico).
        if (!auth()->user()->can('dashboard.index')) {
            return redirect()->route(auth()->user()->homeRoute());
        }

        // ===== Trabajos cargados (por fecha de carga = created_at) =====
        $cargados = [
            'dia'    => Trabajo::whereDate('created_at', Carbon::today())->count(),
            'semana' => Trabajo::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'mes'    => Trabajo::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count(),
        ];

        // ===== Pendientes de revisión / aprobación =====
        $pendientes    = Trabajo::where('estado', EstadoTrabajo::PENDIENTE->value)->count();
        $totalTrabajos = Trabajo::count();

        // ===== Última importación de Materiales y LPU =====
        // Materiales: se loguea en la tabla `importaciones`.
        $ultimaMateriales = $this->resumenImportacion(Importacion::where('tipo', 'materiales')->latest()->first());

        // LPU: si hay registro en `importaciones` se usa; si no (import previo al
        // logueo), se cae a los datos guardados en lpu_tipos_trabajo (misma fuente
        // que la pantalla del Catálogo LPU).
        $impLpu = Importacion::where('tipo', 'lpu')->latest()->first();
        if ($impLpu) {
            $ultimaLpu = $this->resumenImportacion($impLpu);
        } elseif ($fechaLpu = LpuTipoTrabajo::max('ultima_importacion')) {
            $vigenciaLpu = LpuTipoTrabajo::max('vigencia_desde');
            $ultimaLpu = [
                'fecha'        => Carbon::parse($fechaLpu),
                'vigencia'     => $vigenciaLpu ? Carbon::parse($vigenciaLpu) : null,
                'procesados'   => LpuTipoTrabajo::count(),
                'nuevos'       => null,
                'actualizados' => null,
                'usuario'      => null,
            ];
        } else {
            $ultimaLpu = null;
        }

        // ===== Gráfico: cargas por cuadrilla (mes en curso) =====
        $porCuadrilla = Trabajo::selectRaw('cuadrilla_id, COUNT(*) as total')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('cuadrilla_id')
            ->with('cuadrilla')
            ->orderByDesc('total')
            ->get();

        $cuadrillaLabels = $porCuadrilla->map(fn ($r) => $r->cuadrilla?->nombre ?? 'Sin cuadrilla')->values();
        $cuadrillaData   = $porCuadrilla->pluck('total')->map(fn ($n) => (int) $n)->values();

        // ===== Gráfico: trabajos por estado (donut) =====
        $porEstadoRaw = Trabajo::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadoLabels = [];
        $estadoData   = [];
        $estadoColors = [];
        foreach (EstadoTrabajo::cases() as $estado) {
            $estadoLabels[] = $estado->label();
            $estadoData[]   = (int) ($porEstadoRaw[$estado->value] ?? 0);
            $estadoColors[] = $estado->color();
        }

        // ===== Gráfico: cargas de los últimos 30 días (línea) =====
        $desde   = Carbon::today()->subDays(29);
        $porDia  = Trabajo::selectRaw('DATE(created_at) as dia, COUNT(*) as total')
            ->where('created_at', '>=', $desde)
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $trendLabels = [];
        $trendData   = [];
        for ($i = 0; $i < 30; $i++) {
            $d = $desde->copy()->addDays($i);
            $trendLabels[] = $d->format('d/m');
            $trendData[]   = (int) ($porDia[$d->format('Y-m-d')] ?? 0);
        }

        return view('admin.dashboard.index', [
            'cargados'         => $cargados,
            'pendientes'       => $pendientes,
            'totalTrabajos'    => $totalTrabajos,
            'ultimaMateriales' => $ultimaMateriales,
            'ultimaLpu'        => $ultimaLpu,
            'cuadrillaLabels'  => $cuadrillaLabels,
            'cuadrillaData'    => $cuadrillaData,
            'estadoLabels'     => $estadoLabels,
            'estadoData'       => $estadoData,
            'estadoColors'     => $estadoColors,
            'trendLabels'      => $trendLabels,
            'trendData'        => $trendData,
        ]);
    }

    /**
     * Normaliza un registro de importación al shape que consume la vista.
     */
    private function resumenImportacion(?Importacion $imp): ?array
    {
        if (!$imp) {
            return null;
        }
        return [
            'fecha'        => $imp->created_at,
            'vigencia'     => $imp->vigencia,
            'procesados'   => $imp->registros_procesados,
            'nuevos'       => $imp->registros_nuevos,
            'actualizados' => $imp->registros_actualizados,
            'usuario'      => $imp->user ? trim($imp->user->first_name . ' ' . $imp->user->last_name) : null,
        ];
    }
}
