<?php

namespace App\DataTables\EPP;

use App\Models\EppElemento;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EPPElementoDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('certificacion', function ($row) {
                return $row->certificacion ? 'Si' : 'No';
            })
            ->editColumn('estado', function ($row) {
                return view('admin.inc.action', [
                    'toggle' => $row,
                    'name' => 'estado',
                    'route' => 'admin.epp.elementos.status',
                    'value' => $row->estado,
                    'permission' => auth()->user()->can('epp.edit')
                ]);
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];
                if (auth()->user()->can('epp.edit')) {
                    $acciones['edit'] = 'admin.epp.elementos.edit';
                }
                if (auth()->user()->can('epp.trash')) {
                    $acciones['delete'] = 'admin.epp.elementos.destroy';
                }
                return view('admin.inc.action', $acciones);
            })
            ->addColumn('action', 'epp.action')
            ->setRowId('id')
            ->rawColumns(['created_at', 'action','estado']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EppElemento $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('epp-elemento-table')
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
            Column::make('producto')
                ->title('Producto')
                ->data('producto')
                ->orderable(false)
                ->searchable(true),
            Column::make('tipo')
                ->title('Tipo')
                ->data('tipo')
                ->orderable(false)
                ->searchable(true),
            Column::make('marca')
                ->title('Marca')
                ->data('marca')
                ->orderable(false)
                ->searchable(true),
            Column::make('certificacion')
                ->title('Certificación')
                ->data('certificacion')
                ->orderable(false)
                ->searchable(true),
            Column::make('stock')
                ->title('Stock')
                ->data('stock')
                ->orderable(false)
                ->searchable(false),
            Column::make('min_stock')
                ->title('Min Stock')
                ->data('min_stock')
                ->orderable(false)
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
        return 'Elementos_de_Proteccion_Personal_' . date('YmdHis');
    }
}
