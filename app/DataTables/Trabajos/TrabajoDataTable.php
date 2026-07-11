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
            ->addColumn('cuadrilla_nombre', function ($row) {
                return $row->cuadrilla?->nombre ?? '-';
            })
            ->addColumn('tipo_poste_label', function ($row) {
                if (!$row->tipo_poste) {
                    return '<span class="text-muted">—</span>';
                }
                $badge = $row->tipo_poste->value === 'terminal' ? 'bg-primary' : 'bg-info';
                return '<span class="badge ' . $badge . '">' . e($row->tipo_poste->label()) . '</span>';
            })
            ->addColumn('lpu_label', function ($row) {
                return $row->lpu ? e($row->lpu->codigo_lpu) : '<span class="text-muted">—</span>';
            })
            ->editColumn('domicilio', function ($row) {
                return $row->domicilio ?: '-';
            })
            ->addColumn('estado_badge', function ($row) {
                if (!$row->estado) {
                    return '-';
                }
                return '<span class="badge ' . $row->estado->badge() . '">' . e($row->estado->label()) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $user      = auth()->user();
                $aprobado  = $row->estado?->value === \App\Enums\EstadoTrabajo::APROBADO->value;
                $pendiente = $row->estado?->value === \App\Enums\EstadoTrabajo::PENDIENTE->value;
                $btns      = [];

                // Autorizar: solo con permiso y si está pendiente de revisión
                if ($pendiente && $user->can('trabajos_ordenes.approve')) {
                    $btns[] = '<a href="' . route('admin.trabajos.ordenes.edit', $row->id) . '" '
                        . 'class="btn btn-sm btn-success"><i class="fa fa-check-circle me-1"></i>Autorizar</a>';
                }

                if ($user->can('trabajos_ordenes.show')) {
                    $btns[] = '<a href="' . route('admin.trabajos.ordenes.show', $row->id) . '" '
                        . 'class="btn btn-sm btn-info"><i class="fa fa-eye me-1"></i>Ver</a>';
                }

                // Un trabajo aprobado solo lo puede editar quien tenga permiso de aprobación
                if ($user->can('trabajos_ordenes.edit') && (!$aprobado || $user->can('trabajos_ordenes.approve'))) {
                    $btns[] = '<a href="' . route('admin.trabajos.ordenes.edit', $row->id) . '" '
                        . 'class="btn btn-sm btn-primary"><i class="fa fa-edit me-1"></i>Editar</a>';
                }

                if ($user->can('trabajos_ordenes.trash')) {
                    $btns[] = '<form action="' . route('admin.trabajos.ordenes.destroy', $row->id) . '" method="POST" '
                        . 'class="d-inline" onsubmit="return confirm(\'¿Eliminar este trabajo? Esta acción no se puede deshacer.\');">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash me-1"></i>Eliminar</button></form>';
                }

                return '<div class="d-flex gap-1 justify-content-center flex-wrap">' . implode('', $btns) . '</div>';
            })
            ->setRowId('id')
            ->rawColumns(['estado_badge', 'tipo_poste_label', 'lpu_label', 'action']);
    }

    public function query(Trabajo $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['cuadrilla', 'lpu']);

        // Si no es admin, solo ve los trabajos de sus cuadrillas
        if (!auth()->user()->hasRole('admin')) {
            $cuadrillaIds = auth()->user()->cuadrillas()->pluck('cuadrillas.id');
            $query->whereIn('cuadrilla_id', $cuadrillaIds);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('trabajos-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
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
            Column::computed('lpu_label')
                ->title('LPU')
                ->data('lpu_label')
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
