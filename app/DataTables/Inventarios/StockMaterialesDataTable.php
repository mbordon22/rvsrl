<?php

namespace App\DataTables\Inventarios;

use App\Models\Material;
use App\Models\StockMaterial;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class StockMaterialesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('descripcion', function ($row) {
                return $row->material ? $row->material->codigo . ' - ' . $row->material->descripcion : '';
            })
            ->addColumn('ubicacion', function ($row) {
                $ubicacion = '';
                if($row->cuadrilla_id){
                    $ubicacion = $row->cuadrilla->nombre;
                } elseif($row->almacen_id) {
                    $ubicacion = $row->almacen->nombre;
                }
                return $ubicacion;
            })
            ->addColumn('tipo', function ($row) {
                $tipo = '';
                if($row->cuadrilla_id){
                    $tipo = 'Cuadrilla';
                } elseif($row->almacen_id) {
                    $tipo = 'Almacén';
                }
                return $tipo;
            })
            ->addColumn('estado', function ($row) {
                if($row->cantidad < $row->cantidad_minima) {
                    return '<span class="badge badge-danger">En Alerta</span>';
                }

                if($row->cantidad == $row->cantidad_minima) {
                    return '<span class="badge badge-warning">Mínimo</span>';
                }

                if($row->cantidad > $row->cantidad_minima) {
                    return '<span class="badge badge-success">Suficiente</span>';
                }

                if($row->cantidad == 0) {
                    return '<span class="badge badge-secondary">Sin Stock</span>';
                }
            })
            ->addColumn('fecha_ult_actualizacion', function ($row) {
                if($row->fecha_ult_actualizacion != null){
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m-d', $row->fecha_ult_actualizacion)->format('d/m/Y');
                    return $fecha;
                }
                return '';
            })
            ->editColumn('action', function ($row){
                $historial = '
                    <a href="' . route('admin.inventarios.stock.historial', $row->id) . '" class="btn btn-sm btn-info">Ver Historial</a>
                ';

                return $historial;
            })
            ->addColumn('action', 'stockmateriales.action')
            ->setRowId('id')
            ->rawColumns(['created_at', 'action', 'descripcion', 'ubicacion', 'tipo', 'estado']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(StockMaterial $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['material', 'almacen', 'cuadrilla']);

        if (request()->has('almacen_id') && request()->almacen_id) {
            $query->where('almacen_id', request()->almacen_id);
        }

        if (request()->has('cuadrilla_id') && request()->cuadrilla_id) {
            $query->where('cuadrilla_id', request()->cuadrilla_id);
        }

        if (request()->has('material_id') && request()->material_id) {
            $query->where('material_id', request()->material_id);
        }

        if (request()->has('tipo') && request()->tipo) {
            $tipo = request()->tipo;
            if ($tipo == 'almacen') {
                $query->whereNotNull('almacen_id')->whereNull('cuadrilla_id');
            } elseif ($tipo == 'cuadrilla') {
                $query->whereNotNull('cuadrilla_id')->whereNull('almacen_id');
            }
        }

        if (request()->has('stock_bajo') && request()->stock_bajo == '1') {
            $query->where(DB::raw('cantidad'), '<=', DB::raw('cantidad_minima'));
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('stock-material-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
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
                        'searching' => false,
                        'drawCallback' => 'function(settings) {
                            if (settings._iRecordsDisplay === 0) {
                                $(settings.nTableWrapper).find(".dataTables_paginate").hide();
                            } else {
                                $(settings.nTableWrapper).find(".dataTables_paginate").show();
                            }
                            feather.replace();
                        }',
                        'createdRow' => 'function(row, data, dataIndex) {
                            /*if (parseInt(data.cantidad) <= parseInt(data.cantidad_minima)) {
                                $(row).css("background-color", "#FE6A49");
                            }*/
                        }',
                    ])
                    ->processing(true)
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
            Column::make('descripcion')
                ->title('Material')
                ->data('descripcion')
                ->orderable(false)
                ->searchable(true),
            Column::make('ubicacion')
                ->title('Ubicación')
                ->data('ubicacion')
                ->orderable(true)
                ->searchable(true),
            Column::make('tipo')
                ->title('Tipo')
                ->data('tipo')
                ->orderable(true)
                ->searchable(false),
            Column::make('cantidad')
                ->title('Cantidad')
                ->data('cantidad')
                ->orderable(true)
                ->searchable(false),
            Column::make('cantidad_minima')
                ->title('Cant. Min')
                ->data('cantidad_minima')
                ->orderable(true)
                ->searchable(false),
            Column::make('estado')
                ->title('Estado')
                ->data('estado')
                ->orderable(true)
                ->searchable(false),
            Column::make('fecha_ult_actualizacion')
                ->title('Ult. Act.')
                ->data('fecha_ult_actualizacion')
                ->orderable(true)
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
        return 'Stock_Materiales_' . date('YmdHis');
    }
}
