<?php

namespace App\DataTables;

use App\Models\VehiculoDoc;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VehiculoDocDataTable extends DataTable
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
            ->editColumn('archivo', function ($row) {
                if ($row->tipo_documento) {
                    $media = $row->getFirstMedia('archivo');
                    if ($media) {
                        $fileTag = '<button type="button" class="btn btn-sm view-file-btn btn-info" 
                                        data-id="' . $row->id . '">
                                        Ver Archivos
                                    </button>';
                    } else {
                        $fileTag = '';
                    }
                    return $fileTag;
                } else {
                    return '';
                }
            })
            ->editColumn('fecha_vencimiento', function ($row) {
                if($row->fecha_vencimiento != null){
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m-d', $row->fecha_vencimiento)->format('d/m/Y');
                    return $fecha;
                }
                return '--/--/----';
            })
            ->editColumn('estado', function ($row) {
                return view('admin.inc.action', [
                    'toggle' => $row,
                    'name' => 'estado',
                    'route' => 'admin.vehiculo.documento.status',
                    'value' => $row->estado,
                    'permission' => auth()->user()->can('vehiculo.edit')
                ]);
            })
            ->editColumn('action', function ($row) {
                $acciones = '<div class="btn-group" role="group" aria-label="Basic example">';
                if(auth()->user()->can('vehiculo.edit')){
                    $acciones .= '<button type="button" class="btn btn-sm btn-primary edit-document-btn" 
                                    data-id="' . $row->id . '">
                                    Editar
                                </button>';
                }
                if(auth()->user()->can('vehiculo.trash')){
                    $acciones .= '<button type="button" class="btn btn-sm btn-danger btn-delete" 
                                    data-id="' . $row->id . '">
                                    Eliminar
                                </button>';
                }
                $acciones .= '</div>';
                return $acciones;
            })
            ->addColumn('action', 'vehiculodoc.action')
            ->setRowId('id')
            ->rawColumns(['action','archivo','estado']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(VehiculoDoc $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('vehiculo_id', $this->vehiculoId) // Filter by vehiculoId
            ->with('vehiculo');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('vehiculodoc-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
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
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('tipo_documento')
                ->title('Tipo de Documento')
                ->data('tipo_documento')
                ->orderable(false)
                ->searchable(true),
            Column::make('archivo')
                ->title('Archivo')
                ->data('archivo')
                ->orderable(false)
                ->searchable(false),
            Column::make('fecha_vencimiento')
                ->title('Fecha de Vencimiento')
                ->data('fecha_vencimiento')
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
        return 'VehiculoDoc_' . date('YmdHis');
    }
}
