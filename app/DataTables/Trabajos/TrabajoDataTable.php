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
                $acciones = ['data' => $row];
                if (auth()->user()->can('trabajos_ordenes.show')) {
                    $acciones['show'] = 'admin.trabajos.ordenes.show';
                }
                if (auth()->user()->can('trabajos_ordenes.edit')) {
                    $acciones['edit'] = 'admin.trabajos.ordenes.edit';
                }
                if (auth()->user()->can('trabajos_ordenes.trash')) {
                    $acciones['delete'] = 'admin.trabajos.ordenes.destroy';
                }
                return view('admin.inc.action', $acciones);
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
                ->width(90)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Trabajos_' . date('YmdHis');
    }
}
