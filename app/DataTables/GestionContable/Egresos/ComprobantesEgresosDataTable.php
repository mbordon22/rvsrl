<?php

namespace App\DataTables\GestionContable\Egresos;

use App\Models\ComprobanteEgreso;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ComprobantesEgresosDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $totalImporteBruto = (clone $query)->sum('importe_bruto');
        $totalImpuestos = (clone $query)->sum('impuestos');
        $total = (clone $query)->sum('total');

        return (new EloquentDataTable($query))
            ->with([
                'totalImporteBruto' => $totalImporteBruto,
                'totalImpuestos' => $totalImpuestos,
                'total' => $total,
            ])
            ->editColumn('user_id', function ($row) {
                return $row->user->first_name . " " . $row->user->last_name;
            })
            ->editColumn('proveedor_id', function ($row) {
                return $row->proveedor->nombre ?? '';
            })
            ->editColumn('proveedor_cuit', function ($row) {
                return $row->proveedor->numero_documento ?? '';
            })
            ->editColumn('importe_bruto', function ($row) {
                return "$" . number_format($row->importe_bruto, 2, ',', '.');
            })
            ->editColumn('descuento', function ($row) {
                return "$" . number_format($row->descuento, 2, ',', '.');
            })
            ->editColumn('impuestos', function ($row) {
                return "$" . number_format($row->impuestos, 2, ',', '.');
            })
            ->editColumn('total', function ($row) {
                return "$" . number_format($row->total, 2, ',', '.');
            })
            ->editColumn('fecha_comprobante', function ($row) {
                return $row->fecha_comprobante ? $row->fecha_comprobante->format('d/m/Y') : '';
            })
            ->editColumn('fecha_vencimiento', function ($row) {
                return $row->fecha_vencimiento ? $row->fecha_vencimiento->format('d/m/Y') : '';
            })
            ->editColumn('comprobante_tipo', function ($row) {
                return $row->comprobante_tipo ? $row->comprobante_tipo->label() : '';
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];

                if(auth()->user()->can('egresos.edit')){
                    $acciones['edit'] = 'admin.gestion-contable.egresos.comprobantes.edit';
                }
                if(auth()->user()->can('egresos.trash')){
                    $acciones['delete'] = 'admin.gestion-contable.egresos.comprobantes.destroy';
                }

                return view('admin.inc.action', $acciones);
            })
            ->addColumn('action', 'comprobantes.action')
            ->setRowId('id')
            ->rawColumns(['action','estado']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ComprobanteEgreso $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('comprobantes-egresos-table')
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
            Column::make('fecha_comprobante')
                ->title('Fecha Comprobante')
                ->data('fecha_comprobante')
                ->orderable(false)
                ->searchable(false),
            Column::make('fecha_vencimiento')
                ->title('Fecha Vencimiento')
                ->data('fecha_vencimiento')
                ->orderable(false)
                ->searchable(false),
            Column::make('user_id')
                ->title('Último Cambio')
                ->data('user_id')
                ->orderable(false)
                ->searchable(false),
            Column::make('comprobante_tipo')
                ->title('Tipo')
                ->data('comprobante_tipo')
                ->orderable(false)
                ->searchable(false),
            Column::make('numero_comprobante')
                ->title('Comprobante')
                ->data('numero_comprobante')
                ->orderable(false)
                ->searchable(false),
            Column::make('proveedor_id')
                ->title('Proveedor')
                ->data('proveedor_id')
                ->orderable(false)
                ->searchable(false),
            Column::make('proveedor_cuit')
                ->title('N° Documento')
                ->data('proveedor_cuit')
                ->orderable(false)
                ->searchable(false),
            Column::make('importe_bruto')
                ->title('Importe Neto')
                ->data('importe_bruto')
                ->orderable(false)
                ->searchable(false),
            Column::make('descuento')
                ->title('Descuento')
                ->data('descuento')
                ->orderable(false)
                ->searchable(false),
            Column::make('impuestos')
                ->title('Impuestos')
                ->data('impuestos')
                ->orderable(false)
                ->searchable(false),
            Column::make('total')
                ->title('Total')
                ->data('total')
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
        return 'ComprobantesEgresos_' . date('YmdHis');
    }
}
