<?php

namespace App\DataTables\GestionContable;

use App\Models\CentroCosto;
use App\Models\MedioPago;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MediosPagoDataTable extends DataTable
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
                    'route' => 'admin.gestion-contable.medio-pago.status',
                    'value' => $row->estado,
                    'permission' => auth()->user()->can('gestion_contable.edit')
                ]);
            })
            ->editColumn('action', function ($row) {
                $acciones = '<div class="btn-group" role="group" aria-label="Basic example">';
                if(auth()->user()->can('gestion_contable.edit')) {
                    $acciones .= '
                        <button type="button" class="btn btn-sm btn-primary edit-btn" 
                            data-id="' . $row->id . '">
                            Editar
                        </button>
                    ';
                }
                if(auth()->user()->can('gestion_contable.trash')) {
                    $acciones .= '
                        <button type="button" class="btn btn-sm btn-danger btn-delete" 
                            data-id="' . $row->id . '">
                            Eliminar
                        </button>
                    ';
                }
                $acciones .= '</div>';
                return $acciones;
            })
            ->addColumn('action', 'gestion_contable.action')
            ->setRowId('id')
            ->rawColumns(['created_at', 'action', 'estado']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MedioPago $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('mediopago-table')
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
        return 'MediosPago_' . date('YmdHis');
    }
}
