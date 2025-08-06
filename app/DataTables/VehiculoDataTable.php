<?php

namespace App\DataTables;

use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VehiculoDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            /* ->editColumn('imagen', function ($row) {
                if ($row->marca) {
                    $media = $row->getFirstMedia('imagen');
                    if ($media) {
                        $imageTag = '<img src="'.$media->getUrl().'" alt="Image" class="img-thumbnail img-fix">';
                    } else {
                        $imageTag = '<div class="initial-letter">'.strtoupper(substr($row->marca, 0, 1)).'</div>';
                    }
                    return '<div class="initial-div">'.$imageTag.' '.'</div>';
                } else {
                    return '<div class="initial-letter">NA</div>';
                }
            }) */
            ->editColumn('marca', function ($row) {
                return $row->marca . ' ' . $row->modelo;
            })
            ->editColumn('documentacion', function ($row) {
            return "<a href='".route('admin.vehiculo.documento.index', $row->id)."' class='btn btn-sm btn-info'>Documentación</a>";
            })
            ->editColumn('combustible', function ($row) {
                return "<a href='".route('admin.vehiculo.combustible.index', $row->id)."' class='btn btn-sm btn-info'>Combustible</a>";
                })
            ->editColumn('estado', function ($row) {
                return view('admin.inc.action', [
                    'toggle' => $row,
                    'name' => 'estado',
                    'route' => 'admin.vehiculo.status',
                    'value' => $row->estado,
                    'permission' => auth()->user()->can('vehiculo.edit')
                ]);
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];

                if(auth()->user()->can('vehiculo.edit')){
                    $acciones['edit'] = 'admin.vehiculo.edit';
                }
                if(auth()->user()->can('vehiculo.destroy')){
                    $acciones['delete'] = 'admin.vehiculo.destroy';
                }

                return view('admin.inc.action', $acciones);
            })
            ->addColumn('action', 'vehiculo.action')
            ->setRowId('id')
            ->rawColumns(['created_at', 'action','estado','documentacion','combustible']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Vehiculo $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('vehiculo-table')
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
            /* Column::make('imagen')
                ->title('Imagen')
                ->data('imagen')
                ->orderable(false)
                ->searchable(false), */
            Column::make('identificador_vehiculo')
                ->title('Identificador')
                ->data('identificador_vehiculo')
                ->orderable(false)
                ->searchable(false),
            Column::make('marca')
                ->title('Vehículo')
                ->data('marca')
                ->orderable(false)
                ->searchable(true),
            Column::make('ano')
                ->title('Año')
                ->data('ano')
                ->orderable(true)
                ->searchable(false),
            Column::make('patente')
                ->title('Patente')
                ->data('patente')
                ->orderable(false)
                ->searchable(true),
            Column::make('documentacion')
                ->title('Documentación')
                ->data('documentacion')
                ->orderable(false)
                ->searchable(false),
            Column::make('combustible')
                ->title('Combustible')
                ->data('combustible')
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
        return 'Vehiculos_' . date('YmdHis');
    }
}
