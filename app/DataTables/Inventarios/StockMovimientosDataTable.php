<?php

namespace App\DataTables\Inventarios;

use App\Models\StockMaterial;
use App\Models\StockMaterialMovimiento;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class StockMovimientosDataTable extends DataTable
{
    protected $stock_id;
    protected $material;
    protected $almacen_id;
    protected $cuadrilla_id;

    public function setStockId($stock_id)
    {
        $this->stock_id = $stock_id;
    }

    public function setMaterial($material)
    {
        $this->material = $material;
    }

    public function setAlmacenId($almacen_id)
    {
        $this->almacen_id = $almacen_id;
    }

    public function setCuadrillaId($cuadrilla_id)
    {
        $this->cuadrilla_id = $cuadrilla_id;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('fecha', function ($row) {
                if($row->fecha != null){
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m-d', $row->fecha)->format('d/m/Y');
                    return $fecha;
                }
                return '';
            })
            ->editColumn('tipo', function ($row) {
                $tipo = '';
                if($row->tipo == StockMaterialMovimiento::TIPO_INGRESO) {
                    $tipo = '<span class="text-success f-w-700 text-capitalize"><i class="icon-import"></i> ' . StockMaterialMovimiento::TIPO_INGRESO . '</span>';
                } else if($row->tipo == StockMaterialMovimiento::TIPO_EGRESO) {
                    $tipo = '<span class="text-danger f-w-700 text-capitalize"><i class="icon-export"></i> ' . StockMaterialMovimiento::TIPO_EGRESO . '</span>';
                } else if($row->tipo == StockMaterialMovimiento::TIPO_TRASLADO) {
                    if($row->origen_almacen_id == $this->almacen_id) {
                        $tipo = '<span class="text-primary f-w-700 text-capitalize"><i class="icon-exchange-vertical"></i> Transferencia (Salida)</span>';
                    }
                    if($row->destino_almacen_id == $this->almacen_id) {
                        $tipo = '<span class="text-primary f-w-700 text-capitalize"><i class="icon-exchange-vertical"></i> Transferencia (Entrada)</span>';
                    }
                } else if($row->tipo == StockMaterialMovimiento::TIPO_AJUSTE) {
                    $tipo = '<span class="text-secondary f-w-700 text-capitalize"><i class="icon-reload"></i> ' . StockMaterialMovimiento::TIPO_AJUSTE . '</span>';
                }
                return $tipo;
            })
            ->addColumn('cantidad', function ($row) {
                if($row->tipo == StockMaterialMovimiento::TIPO_TRASLADO) {
                    if($row->origen_almacen_id && $row->origen_almacen_id == $this->almacen_id) {
                        return (int) $row->cantidad;
                    }
                    else if($row->origen_cuadrilla_id && $row->origen_cuadrilla_id == $this->cuadrilla_id){
                        return (int) $row->cantidad;
                    }
                    else {
                        return '+' . (int) abs($row->cantidad);
                    }
                } else {
                    if((int) $row->cantidad > 0) {
                        return '+' . (int) abs($row->cantidad);
                    } else {
                        return (int) $row->cantidad;
                    }
                }
            })
            ->editColumn('origen_destino', function ($row) {
                if($row->tipo == StockMaterialMovimiento::TIPO_INGRESO) {
                    return $row->tercero_tipo . ' ' . $row->tercero_nombre;
                }

                if($row->tipo == StockMaterialMovimiento::TIPO_EGRESO) {
                    return $row->tercero_tipo . ' ' . $row->tercero_nombre;
                }
                
                if($row->tipo == StockMaterialMovimiento::TIPO_TRASLADO) {
                    if($row->origen_almacen_id == $this->almacen_id) {
                        if($row->destino_almacen_id) {
                            return 'Hacia Almacén ' . $row->destinoAlmacen->nombre;
                        } else if($row->destino_cuadrilla_id) {
                            return 'Hacia Cuadrilla ' . $row->destinoCuadrilla->nombre;
                        }
                    }

                    if($row->destino_almacen_id == $this->almacen_id) {
                        if($row->origen_almacen_id) {
                            return 'Desde Almacén ' . $row->origenAlmacen->nombre;
                        } else if($row->origen_cuadrilla_id) {
                            return 'Desde Cuadrilla ' . $row->origenCuadrilla->nombre;
                        }
                    }

                    if($row->origen_cuadrilla_id == $this->cuadrilla_id) {
                        if($row->destino_almacen_id) {
                            return 'Hacia Almacén ' . $row->destinoAlmacen->nombre;
                        } else if($row->destino_cuadrilla_id) {
                            return 'Hacia Cuadrilla ' . $row->destinoCuadrilla->nombre;
                        }
                    }

                    if($row->destino_cuadrilla_id == $this->cuadrilla_id) {
                        if($row->origen_almacen_id) {
                            return 'Desde Almacén ' . $row->origenAlmacen->nombre;
                        } else if($row->origen_cuadrilla_id) {
                            return 'Desde Cuadrilla ' . $row->origenCuadrilla->nombre;
                        }
                    }
                }

                if($row->tipo == StockMaterialMovimiento::TIPO_AJUSTE) {
                    return $row->observaciones;
                }
            })
            ->addColumn('user_id', function ($row) {
                return $row->usuario ? $row->usuario->first_name . ' ' . $row->usuario->last_name : '';
            })
            ->addColumn('origen_destino', 'user_id', 'cantidad', 'tipo')
            ->setRowId('id')
            ->rawColumns(['fecha', 'user_id', 'origen_destino', 'tipo', 'cantidad']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(StockMaterialMovimiento $model): QueryBuilder
    {
        $stockMaterial = StockMaterial::with(['material', 'almacen', 'cuadrilla'])
                ->where('id', $this->stock_id)
                ->first();

        if(!$stockMaterial) {
            return $model->newQuery()->where('id', 0);
        }

        $movimientos = $model->newQuery()->with(['material', 'origenAlmacen', 'destinoAlmacen', 'origenCuadrilla', 'destinoCuadrilla', 'usuario'])
                ->where('material_id', $stockMaterial->material_id)
                ->where(function($query) use ($stockMaterial) {

                    if($stockMaterial->almacen_id) {
                        $query->where('origen_almacen_id', $stockMaterial->almacen_id)
                              ->orWhere('destino_almacen_id', $stockMaterial->almacen_id);
                    }
                    if($stockMaterial->cuadrilla_id) {
                        $query->where('origen_cuadrilla_id', $stockMaterial->cuadrilla_id)
                              ->orWhere('destino_cuadrilla_id', $stockMaterial->cuadrilla_id);
                    }                          
                })
                ->orderBy('id', 'desc');

        return $movimientos;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('stock-movimientos-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0, 'desc')
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
            Column::make('fecha')
                ->title('Fecha')
                ->data('fecha')
                ->orderable(true)
                ->searchable(false),
            Column::make('tipo')
                ->title('Tipo')
                ->data('tipo')
                ->orderable(true)
                ->searchable(false),
            Column::make('cantidad')
                ->title('Cantidad')
                ->data('cantidad')
                ->orderable(false)
                ->searchable(false),
            Column::computed('origen_destino')
                ->title('Origen/Destino')
                ->orderable(false)
                ->searchable(false),
            Column::make('user_id')
                ->title('Usuario')
                ->data('user_id')
                ->orderable(false)
                ->searchable(false)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Movimientos_Stock_' . $this->material . '_' . date('YmdHis');
    }
}
