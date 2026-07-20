<?php

namespace App\DataTables\Trabajos;

use App\Models\Trabajo;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TrabajoDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('fecha', function ($row) {
                return $row->fecha ? $row->fecha->format('d/m/Y') : '-';
            })
            ->editColumn('ot', function ($row) {
                // Solo se carga al aprobar; si no está, mostramos "-"
                return $row->ot ?: '-';
            })
            ->addColumn('cuadrilla_nombre', function ($row) {
                if (!$row->cuadrilla) {
                    return '<span class="text-muted">—</span>';
                }
                return '<span class="chip-cuadrilla">' . e($row->cuadrilla->nombre) . '</span>';
            })
            ->addColumn('tipo_poste_label', function ($row) {
                if (!$row->tipo_poste) {
                    return '<span class="text-muted">—</span>';
                }
                $bg = $row->tipo_poste->value === 'terminal' ? '#3d3f8f' : '#2f4b7c';
                return '<span class="pill-poste" style="background:' . $bg . '">' . e($row->tipo_poste->label()) . '</span>';
            })
            ->editColumn('domicilio', function ($row) {
                return $row->domicilio
                    ? '<span class="fw-500">' . e($row->domicilio) . '</span>'
                    : '<span class="text-muted">—</span>';
            })
            ->addColumn('estado_badge', function ($row) {
                if (!$row->estado) {
                    return '-';
                }
                return '<span class="pill-estado" style="background:' . $row->estado->color() . '">'
                    . e($row->estado->label()) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $user      = auth()->user();
                $bloqueado = in_array($row->estado?->value, [
                    \App\Enums\EstadoTrabajo::APROBADO->value,
                    \App\Enums\EstadoTrabajo::CERTIFICADO->value,
                ], true);
                $pendiente = $row->estado?->value === \App\Enums\EstadoTrabajo::PENDIENTE->value;

                // SVGs (del diseño) para render confiable en el HTML que inyecta DataTables
                $svgCheck = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
                $svgEye   = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>';
                $svgEdit  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
                $svgTrash = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';

                $btns = [];

                // Revisar/Autorizar: solo con permiso y si está pendiente de revisión
                if ($pendiente && $user->can('trabajos_ordenes.approve')) {
                    $btns[] = '<a href="' . route('admin.trabajos.ordenes.revisar', $row->id) . '" '
                        . 'class="act-btn act-autorizar" title="Revisar y aprobar">' . $svgCheck . 'Revisar</a>';
                }

                // Trabajo aprobado: acceso a la pantalla de revisión (para revertir / re-revisar)
                if ($row->estado?->value === \App\Enums\EstadoTrabajo::APROBADO->value && $user->can('trabajos_ordenes.approve')) {
                    $btns[] = '<a href="' . route('admin.trabajos.ordenes.revisar', $row->id) . '" '
                        . 'class="act-icon act-ver" title="Revisar">' . $svgCheck . '</a>';
                }

                if ($user->can('trabajos_ordenes.show')) {
                    $btns[] = '<a href="' . route('admin.trabajos.ordenes.show', $row->id) . '" '
                        . 'class="act-icon act-ver" title="Ver detalle">' . $svgEye . '</a>';
                }

                // Un trabajo aprobado solo lo puede editar quien tenga permiso de aprobación
                if ($user->can('trabajos_ordenes.edit') && (!$bloqueado || $user->can('trabajos_ordenes.approve'))) {
                    $btns[] = '<a href="' . route('admin.trabajos.ordenes.edit', $row->id) . '" '
                        . 'class="act-icon act-editar" title="Editar">' . $svgEdit . '</a>';
                }

                if ($user->can('trabajos_ordenes.trash')) {
                    $btns[] = '<form action="' . route('admin.trabajos.ordenes.destroy', $row->id) . '" method="POST" '
                        . 'class="d-inline-flex" onsubmit="return confirm(\'¿Eliminar este trabajo? Esta acción no se puede deshacer.\');">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="act-icon act-eliminar" title="Eliminar">' . $svgTrash . '</button></form>';
                }

                return '<div class="act-cell">' . implode('', $btns) . '</div>';
            })
            ->setRowId('id')
            ->rawColumns(['estado_badge', 'tipo_poste_label', 'cuadrilla_nombre', 'domicilio', 'action']);
    }

    public function query(Trabajo $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['cuadrilla']);

        // Si no es admin, solo ve los trabajos de sus cuadrillas
        if (!auth()->user()->hasRole('admin')) {
            $cuadrillaIds = auth()->user()->cuadrillas()->pluck('cuadrillas.id');
            $query->whereIn('cuadrilla_id', $cuadrillaIds);
        }

        // Filtros de la barra superior (server-side)
        $r = request();
        if ($r->filled('f_cuadrilla')) {
            $query->where('cuadrilla_id', $r->input('f_cuadrilla'));
        }
        if ($r->filled('f_estado')) {
            $query->where('estado', $r->input('f_estado'));
        }
        if ($r->filled('f_domicilio')) {
            $query->where('domicilio', 'like', '%' . $r->input('f_domicilio') . '%');
        }
        if ($r->filled('f_desde')) {
            $query->whereDate('fecha', '>=', $r->input('f_desde'));
        }
        if ($r->filled('f_hasta')) {
            $query->whereDate('fecha', '<=', $r->input('f_hasta'));
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('trabajos-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d){
                    d.f_cuadrilla = document.getElementById("f_cuadrilla")?.value || "";
                    d.f_estado    = document.getElementById("f_estado")?.value || "";
                    d.f_domicilio = document.getElementById("f_domicilio")?.value || "";
                    d.f_desde     = document.getElementById("f_desde")?.value || "";
                    d.f_hasta     = document.getElementById("f_hasta")?.value || "";
                }',
            ])
            ->orderBy(0, 'desc')
            ->parameters([
                'language' => [
                    'emptyTable'   => 'Aún no hay trabajos cargados',
                    'infoEmpty'    => '',
                    'zeroRecords'  => 'No hay registros para mostrar',
                    'info'         => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'infoFiltered' => '(filtrado de _MAX_ total registros)',
                    'lengthMenu'   => 'Mostrar _MENU_ registros',
                    'search'       => 'Buscar:',
                    'paginate'     => [
                        'next'     => 'Siguiente',
                        'previous' => 'Anterior',
                        'first'    => 'Primero',
                        'last'     => 'Último',
                    ],
                ],
                'searching' => false,
                'drawCallback' => 'function(settings) {
                    if (settings._iRecordsDisplay === 0) {
                        $(settings.nTableWrapper).find(".dataTables_paginate").hide();
                    } else {
                        $(settings.nTableWrapper).find(".dataTables_paginate").show();
                    }
                    feather.replace();
                }',
            ])
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('print'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('fecha')
                ->title('Fecha')
                ->data('fecha')
                ->orderable(true)
                ->searchable(false),
            Column::make('ot')
                ->title('OT')
                ->data('ot')
                ->orderable(false)
                ->searchable(true),
            Column::computed('cuadrilla_nombre')
                ->title('Cuadrilla')
                ->data('cuadrilla_nombre')
                ->orderable(false)
                ->searchable(false),
            Column::make('domicilio')
                ->title('Domicilio')
                ->data('domicilio')
                ->orderable(false)
                ->searchable(true),
            Column::computed('tipo_poste_label')
                ->title('Poste')
                ->data('tipo_poste_label')
                ->orderable(false)
                ->searchable(false),
            Column::computed('estado_badge')
                ->title('Estado')
                ->data('estado_badge')
                ->orderable(false)
                ->searchable(false),
            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(260)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Trabajos_' . date('YmdHis');
    }
}
