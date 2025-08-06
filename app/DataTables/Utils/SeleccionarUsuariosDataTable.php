<?php

namespace App\DataTables\Utils;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SeleccionarUsuariosDataTable extends DataTable
{

    protected $role;
    protected $selectedUsers = [];

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function setSelectedUsers(array $selectedUsers)
    {
        $this->selectedUsers = $selectedUsers; // Setter for selected users
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('first_name', function ($row) {
                $label = "<label class='form-check-label w-100 h-100' for='user-{$row->id}'>";
                $label .= $row->first_name . ' ' . $row->last_name;
                $label .= "</label>";
                return $label;
            })
            ->filterColumn('first_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('action', function ($row) {
                $input = '<input type="checkbox" class="form-check-input" id="user-' . $row->id . '" style="height:20px; width:20px; border-color:black;" name="empleados[]" value="' . $row->id . '"';
                if (isset($this->selectedUsers) && in_array($row->id, $this->selectedUsers)) {
                    $input .= ' checked';
                }
                $input .= '>';
                return $input;
            })
            ->addColumn('action', 'seleccionar_usuario.action')
            ->setRowId('id')
            ->rawColumns(['created_at', 'action', 'first_name']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->role($this->role)
            ->where('status', 1)
            ->where('system_reserve', 0);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('seleccionar-usuario-table')
                    ->setTableAttribute('class', 'hover')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(50)
                    ->orderBy(0)->parameters([
                        'lengthChange' => false,
                        'scrollY' => '30vh',
                        'scrollCollapse' => true,
                        'paging' => false,
                        'language' => [
                            'emptyTable' =>'No se encontraron usuarios',
                            'infoEmpty' => '',
                            'zeroRecords' => 'No hay usuarios para mostrar',
                            'info' => 'Mostrando _START_ a _END_ de _TOTAL_ usuarios',
                            'infoFiltered' => '(filtrado de _MAX_ total usuarios)',
                            'lengthMenu' => 'Mostrar _MENU_ usuarios',
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
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('first_name')
                ->title('Nombre')
                ->data('first_name')
                ->orderable(false)
                ->searchable(true),
            Column::computed('action')
                ->title('Seleccionar')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }
}
