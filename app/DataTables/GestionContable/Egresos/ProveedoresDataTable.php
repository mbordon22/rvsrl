<?php

namespace App\DataTables\GestionContable\Egresos;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProveedoresDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('condicion_iva', function ($row) {
                return $row->condicion_iva->label();
            })
            ->editColumn('numero_documento', function ($row) {
                return $row->tipo_documento->label() . ' ' . $row->numero_documento;
            })
            ->editColumn('estado', function ($row) {
                return view('admin.inc.action', [
                    'toggle' => $row,
                    'name' => 'estado',
                    'route' => 'admin.gestion-contable.egresos.proveedores.status',
                    'value' => $row->estado,
                    'permission' => auth()->user()->can('egresos.edit')
                ]);
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];

                if(auth()->user()->can('egresos.edit')){
                    $acciones['edit'] = 'admin.gestion-contable.egresos.proveedores.edit';
                }
                if(auth()->user()->can('egresos.trash')){
                    $acciones['delete'] = 'admin.gestion-contable.egresos.proveedores.destroy';
                }

                return view('admin.inc.action', $acciones);
            })
            ->addColumn('action', 'proveedores.action')
            ->setRowId('id')
            ->rawColumns(['action','estado']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Proveedor $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('proveedores-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->parameters([
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
                ->orderable(true)
                ->searchable(true),
            Column::make('codigo')
                ->title('Código')
                ->data('codigo')
                ->orderable(true)
                ->searchable(true),
            Column::make('numero_documento')
                ->title('Numero Doc')
                ->data('numero_documento')
                ->orderable(false)
                ->searchable(true),
            Column::make('condicion_iva')
                ->title('Condición IVA')
                ->data('condicion_iva')
                ->orderable(true)
                ->searchable(false),
            Column::make('telefono')
                ->title('Telefono')
                ->data('telefono')
                ->orderable(false)
                ->searchable(true),
            Column::make('localidad')
                ->title('Localidad')
                ->data('localidad')
                ->orderable(false)
                ->searchable(true),
            Column::make('direccion')
                ->title('Dirección')
                ->data('direccion')
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

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Proveedores_' . date('YmdHis');
    }
}
