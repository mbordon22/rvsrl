<?php

namespace App\DataTables;

use App\Models\VehiculoCombustible;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VehiculoCombustibleDataTable extends DataTable
{
    private $vehiculoId; // Add property to store vehiculoId

    public function setVehiculoId($vehiculoId)
    {
        $this->vehiculoId = $vehiculoId; // Setter for vehiculoId
    }
    
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('user_id', function ($row) {
                return $row->user ? $row->user->first_name . ' ' . $row->user->last_name : 'N/A';
            })
            ->filterColumn('user_id', function($query, $keyword) {
                $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) like ?", ["%{$keyword}%"]);
            })
            ->addColumn('tipo_combustible', function ($row) {
                return $row->tipo_combustible ? $row->tipo_combustible : 'N/A';
            })
            ->editColumn('fecha_carga', function ($row) {
                if($row->fecha_carga != null){
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m-d', $row->fecha_carga)->format('d/m/Y');
                    return $fecha;
                }
                return '';
            })
            ->addColumn('monto', function ($row) {
                return $row->monto ? '$' . number_format($row->monto, 2) : 'N/A';
            })
            ->addColumn('litros', function ($row) {
                return $row->litros ? number_format($row->litros, 2) . ' L' : 'N/A';
            })
            ->editColumn('archivo', function ($row) {
                if ($row->tipo_combustible) {
                    $media = $row->getFirstMedia('archivo');
                    if ($media) {
                        $fileTag = '<button type="button" class="btn btn-sm view-file-btn btn-info" 
                                        data-id="' . $row->id . '">
                                        Ver Facturas
                                    </button>';
                    } else {
                        $fileTag = '';
                    }
                    return $fileTag;
                } else {
                    return '';
                }
            })
            ->editColumn('action', function ($row) {
                $acciones = '<div class="btn-group" role="group" aria-label="Basic example">';
                if(auth()->user()->can('combustible.edit')) {
                    $acciones .= '
                        <button type="button" class="btn btn-sm btn-primary edit-document-btn" 
                            data-id="' . $row->id . '">
                            Editar
                        </button>
                    ';
                }
                if(auth()->user()->can('combustible.trash')) {
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
            ->addColumn('action', 'vehiculocombustible.action')
            ->rawColumns(['action', 'archivo'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(VehiculoCombustible $model): QueryBuilder
    {
        return $model->newQuery()
                ->with(['vehiculo', 'user'])
                ->leftJoin('users', 'vehiculos_combustible.user_id', '=', 'users.id')
                ->select('vehiculos_combustible.*')
                ->where('vehiculo_id', $this->vehiculoId);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('vehiculocombustible-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(2, 'desc')
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
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('tipo_combustible')
                  ->title('Tipo de Combustible')
                  ->searchable(false)
                  ->orderable(false),
            Column::make('user_id')
                ->title('Usuario')
                ->searchable(false)
                ->orderable(false),
            Column::make('fecha_carga')
                ->title('Fecha de Carga')
                ->data('fecha_carga')
                ->searchable(false)
                ->orderable(true),
            Column::make('monto')
                  ->title('Monto')
                  ->searchable(false)
                  ->orderable(false),
            Column::make('litros')
                  ->title('Litros')
                  ->searchable(false)
                  ->orderable(false),
            Column::make('archivo')
                  ->title('Factura')
                  ->searchable(false)
                  ->orderable(false),
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
        return 'VehiculoCombustible_' . date('YmdHis');
    }
}
