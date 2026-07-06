<?php

namespace App\DataTables\Trabajos;

use App\Models\PeriodoCertificacion;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PeriodoCertificacionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('rango', function ($row) {
                return $row->fecha_desde->format('d/m/Y') . ' — ' . $row->fecha_hasta->format('d/m/Y');
            })
            ->addColumn('cuadrilla_nombre', function ($row) {
                return $row->cuadrilla?->nombre ?? 'Todas';
            })
            ->addColumn('categoria_label', function ($row) {
                return $row->categoria?->label() ?? '-';
            })
            ->addColumn('cant_trabajos', function ($row) {
                return $row->trabajos_count;
            })
            ->addColumn('estado_badge', function ($row) {
                $badge = match ($row->estado) {
                    'abierto'   => 'bg-info',
                    'cerrado'   => 'bg-secondary',
                    'exportado' => 'bg-success',
                    default     => 'bg-light',
                };
                return '<span class="badge ' . $badge . '">' . e(ucfirst($row->estado)) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $ver = route('admin.trabajos.periodos.show', $row->id);
                $html = '<a href="' . $ver . '" class="show-icon" title="Ver/Certificar"><i data-feather="eye"></i></a>';
                if (auth()->user()->can('trabajos_periodos.trash')) {
                    $html .= '&nbsp;<a href="#confirmationModal' . $row->id . '" data-bs-toggle="modal" class="delete-svg"><i data-feather="trash-2" class="remove-icon"></i></a>';
                    $html .= view('admin.trabajos.periodos.delete_modal', ['id' => $row->id])->render();
                }
                return '<div class="action-div">' . $html . '</div>';
            })
            ->setRowId('id')
            ->rawColumns(['estado_badge', 'action']);
    }

    public function query(PeriodoCertificacion $model): QueryBuilder
    {
        return $model->newQuery()->with('cuadrilla')->withCount('trabajos');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('periodos-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->parameters([
                'language' => [
                    'emptyTable'   => 'Aún no hay períodos de certificación',
                    'zeroRecords'  => 'No hay registros para mostrar',
                    'info'         => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'infoFiltered' => '(filtrado de _MAX_ total registros)',
                    'lengthMenu'   => 'Mostrar _MENU_ registros',
                    'search'       => 'Buscar:',
                    'paginate'     => ['next' => 'Siguiente', 'previous' => 'Anterior', 'first' => 'Primero', 'last' => 'Último'],
                ],
                'drawCallback' => 'function() { feather.replace(); }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('nombre')->title('Nombre')->orderable(true)->searchable(true),
            Column::computed('rango')->title('Período')->orderable(false)->searchable(false),
            Column::computed('cuadrilla_nombre')->title('Cuadrilla')->orderable(false)->searchable(false),
            Column::computed('categoria_label')->title('Categoría')->orderable(false)->searchable(false),
            Column::computed('cant_trabajos')->title('Trabajos')->orderable(false)->searchable(false)->addClass('text-center'),
            Column::computed('estado_badge')->title('Estado')->orderable(false)->searchable(false),
            Column::computed('action')->title('Acciones')->exportable(false)->printable(false)->width(90)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Periodos_' . date('YmdHis');
    }
}
