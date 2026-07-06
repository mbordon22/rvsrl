<?php

namespace App\DataTables\Trabajos;

use App\Models\LpuTipoTrabajo;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LpuTipoTrabajoDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('precio_mantenimiento', function ($row) {
                return '$ ' . number_format($row->precio_mantenimiento, 2, ',', '.');
            })
            ->editColumn('precio_obras', function ($row) {
                return '$ ' . number_format($row->precio_obras, 2, ',', '.');
            })
            ->editColumn('estado', function ($row) {
                return view('admin.inc.action', [
                    'toggle'     => $row,
                    'name'       => 'estado',
                    'route'      => 'admin.trabajos.lpu.status',
                    'value'      => $row->estado,
                    'permission' => auth()->user()->can('listado_lpu.edit'),
                ]);
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];
                if (auth()->user()->can('listado_lpu.edit')) {
                    $acciones['edit'] = 'admin.trabajos.lpu.edit';
                }
                if (auth()->user()->can('listado_lpu.trash')) {
                    $acciones['delete'] = 'admin.trabajos.lpu.destroy';
                }
                return view('admin.inc.action', $acciones);
            })
            ->addColumn('action', 'lpu.action')
            ->setRowId('id')
            ->rawColumns(['action', 'estado']);
    }

    public function query(LpuTipoTrabajo $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('lpu-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->parameters([
                'language' => [
                    'emptyTable'   => 'No se encontraron registros',
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
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('codigo_lpu')
                ->title('Código (S4)')
                ->data('codigo_lpu')
                ->orderable(true)
                ->searchable(true),
            Column::make('descripcion')
                ->title('Descripción')
                ->data('descripcion')
                ->orderable(false)
                ->searchable(true),
            Column::make('unidad')
                ->title('Unidad')
                ->data('unidad')
                ->orderable(true)
                ->searchable(false),
            Column::make('precio_mantenimiento')
                ->title('Mantenimiento')
                ->data('precio_mantenimiento')
                ->orderable(true)
                ->searchable(false),
            Column::make('precio_obras')
                ->title('Obras')
                ->data('precio_obras')
                ->orderable(true)
                ->searchable(false),
            Column::make('estado')
                ->title('Estado')
                ->data('estado')
                ->orderable(false)
                ->searchable(false),
            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'LPU_' . date('YmdHis');
    }
}
