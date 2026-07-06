<?php

namespace App\DataTables;

use App\Models\Importacion;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ImportacionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-';
            })
            ->editColumn('tipo', function ($row) {
                $badge = $row->tipo === 'lpu' ? 'bg-primary' : 'bg-info';
                return '<span class="badge ' . $badge . '">' . e($row->tipoLabel()) . '</span>';
            })
            ->editColumn('vigencia', function ($row) {
                return $row->vigencia ? $row->vigencia->format('d/m/Y') : '-';
            })
            ->addColumn('usuario', function ($row) {
                return $row->user ? trim($row->user->first_name . ' ' . $row->user->last_name) : '-';
            })
            ->addColumn('archivo_txt', function ($row) {
                return $row->archivo ?: '-';
            })
            ->setRowId('id')
            ->rawColumns(['tipo']);
    }

    public function query(Importacion $model): QueryBuilder
    {
        $query = $model->newQuery()->with('user');

        if (in_array(request('tipo'), ['lpu', 'materiales'])) {
            $query->where('tipo', request('tipo'));
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('importaciones-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->parameters([
                'language' => [
                    'emptyTable'   => 'Aún no se registraron importaciones',
                    'infoEmpty'    => '',
                    'zeroRecords'  => 'No hay registros para mostrar',
                    'info'         => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'infoFiltered' => '(filtrado de _MAX_ total registros)',
                    'lengthMenu'   => 'Mostrar _MENU_ registros',
                    'search'       => 'Buscar:',
                    'paginate'     => [
                        'next'     => 'Siguiente',
                        'previous' => 'Anterior',
                        'first'    => 'Primero',
                        'last'     => 'Último',
                    ],
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('created_at')
                ->title('Fecha')
                ->data('created_at')
                ->orderable(true)
                ->searchable(false),
            Column::make('tipo')
                ->title('Tipo')
                ->data('tipo')
                ->orderable(true)
                ->searchable(true),
            Column::make('archivo')
                ->title('Archivo')
                ->data('archivo')
                ->orderable(false)
                ->searchable(true),
            Column::make('vigencia')
                ->title('Vigencia')
                ->data('vigencia')
                ->orderable(true)
                ->searchable(false),
            Column::make('registros_procesados')
                ->title('Procesados')
                ->data('registros_procesados')
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-center'),
            Column::make('registros_nuevos')
                ->title('Nuevos')
                ->data('registros_nuevos')
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-center'),
            Column::make('registros_actualizados')
                ->title('Actualizados')
                ->data('registros_actualizados')
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-center'),
            Column::computed('usuario')
                ->title('Usuario')
                ->data('usuario')
                ->orderable(false)
                ->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Importaciones_' . date('YmdHis');
    }
}
