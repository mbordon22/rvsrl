<?php

namespace App\DataTables\EPP;

use Carbon\Carbon;
use App\Models\EppListadoEntrega;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class EPPEntregaDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('fecha', function ($row) {
                if($row->fecha != null){
                    return $row->fecha ? Carbon::parse($row->fecha)->format('d/m/Y') : '';
                }
                return '';
            })
            ->editColumn('empleado', function ($row) {
                return $row->empleado ? $row->empleado->first_name . ' ' . $row->empleado->last_name : '';
            })
            ->filterColumn('empleado', function($query, $keyword) {
                $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) like ?", ["%{$keyword}%"]);
            })
            ->editColumn('detalle', function ($row) {
                if($row->filas->count() > 0){
                    return '<a href="'.route('admin.epp.entregas.show', $row->id).'" class="btn btn-primary btn-sm">Ver Detalle</a>';
                }
                else {
                    return '<span class="badge badge-danger">Sin Elementos</span>';
                }
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];
                if (auth()->user()->can('epp.edit')) {
                    $acciones['edit'] = 'admin.epp.entregas.edit';
                }
                if (auth()->user()->can('epp.trash')) {
                    $acciones['delete'] = 'admin.epp.entregas.destroy';
                }
                return view('admin.inc.action', $acciones);
            })
            ->addColumn('action', 'epp.action')
            ->setRowId('id')
            ->rawColumns(['created_at', 'action','estado', 'detalle']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EppListadoEntrega $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['filas', 'empleado'])
            ->leftJoin('users', 'epp_listado_entregas.user_id', '=', 'users.id')
            ->select('epp_listado_entregas.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('epp-entrega-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(0)->parameters([
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
                        }'
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
            Column::make('fecha')
                ->title('Fecha Entrega')
                ->searchable(false)
                ->orderable(true),
            Column::make('empleado')
                ->title('Empleado')
                ->orderable(false)
                ->searchable(true),
            Column::make('detalle')
                ->title('Detalle')
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
        return 'Entregas_Elementos_de_Proteccion_Personal_' . date('YmdHis');
    }
}
