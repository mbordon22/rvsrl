<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class RoleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function ($row) {
                return $row->created_at->diffForHumans();
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->diffForHumans();
            })
            ->editColumn('action', function ($row) {
                $acciones = ['data' => $row];
                if (auth()->user()->can('role.edit')) {
                    $acciones['edit'] = 'admin.role.edit';
                }
                if (auth()->user()->can('role.destroy')) {
                    $acciones['delete'] = 'admin.role.destroy';
                }
                return view('admin.inc.action', $acciones);
            })
            ->rawColumns(['created_at', 'status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->where('system_reserve',0);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('role-table')
            ->addColumn(['data' => 'name', 'title' => __('Nombre'), 'orderable' => false, 'searchable' => true])
            ->addColumn(['data' => 'created_at', 'title' => __('Creado'), 'orderable' => false, 'searchable' => false])
            ->addColumn(['data' => 'updated_at', 'title' => __('Última Actualización'), 'orderable' => false, 'searchable' => false])
            ->addColumn(['data' => 'action', 'title' => __('Acción'), 'orderable' => false, 'searchable' => false])
            ->minifiedAjax()
            ->parameters([
                'language' => [
                    'emptyTable' =>'No se encontraron registros',
                    'infoEmpty' => '',
                    'zeroRecords' => 'No hay registros para mostrar',
                    'info' => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'infoFiltered' => '(filtrado de _MAX_ registros totales)',
                    'lengthMenu' => 'Mostrar _MENU_ registros',
                    'search' => 'Buscar:',
                    'paginate' => [
                        'next' => 'Siguiente',
                        'previous' => 'Anterior',
                        'first' => 'Primero',
                        'last' => 'Último',
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
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Role_' . date('YmdHis');
    }
}
