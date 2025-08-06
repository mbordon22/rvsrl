<?php

namespace App\DataTables\Inventarios;

use App\Models\Almacen;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AlmacenDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('estado', function ($row) {
                return view('admin.inc.action', [
                    'toggle' => $row,
                    'name' => 'estado',
                    'route' => 'admin.inventarios.almacenes.status',
                    'value' => $row->estado,
                    'permission' => auth()->user()->can('listado_almacenes.edit')
                ]);
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];
                if (auth()->user()->can('listado_almacenes.edit')) {
                    $acciones['edit'] = 'admin.inventarios.almacenes.edit';
                }
                if (auth()->user()->can('listado_almacenes.trash')) {
                    $acciones['delete'] = 'admin.inventarios.almacenes.destroy';
                }
                return view('admin.inc.action', $acciones);
            })
            ->addColumn('action', 'almacen.action')
            ->setRowId('id')
            ->rawColumns(['created_at', 'action','estado']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Almacen $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('almacen-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)->parameters([
                        'language' => [
                            'emptyTable' =>'No se encontraron registros',
                            'infoEmpty' => '',
                            'zeroRecords' => 'No hay registros para mostrar',
                            'info' => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                            'infoFiltered' => '(filtrado de _MAX_ total registros)',
                            'lengthMenu' => 'Mostrar _MENU_ registros',
                            'search' => 'Buscar:',
                            'paginate' => [
                                'next' => 'Siguiente',
                                'previous' => 'Anterior',
                                'first' => 'Primero',
                                'last' => 'Último'
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
                        'createdRow' => 'function(row, data, dataIndex) {
                            if (parseInt(data.stock) <= parseInt(data.min_stock)) {
                                $(row).css("background-color", "#FE6A49");
                            }
                        }',
                    ])
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('nombre')
                ->title('Nombre')
                ->data('nombre')
                ->orderable(false)
                ->searchable(true),
            Column::make('descripcion')
                ->title('Descripción')
                ->data('descripcion')
                ->orderable(false)
                ->searchable(true),
            Column::make('ubicacion')
                ->title('Ubicación')
                ->data('ubicacion')
                ->orderable(false)
                ->searchable(true),
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

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Almacenes_' . date('YmdHis');
    }
}
