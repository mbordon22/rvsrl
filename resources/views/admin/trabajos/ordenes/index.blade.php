@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
        /* ===== Listado de Trabajos — estilos del diseño "Listado de Trabajos.dc.html" ===== */
        .trabajos-listado { border:none; border-radius:12px; box-shadow:0 1px 3px rgba(30,40,70,.06),0 8px 24px rgba(30,40,70,.04); overflow:visible; }

        /* Barra de filtros */
        .trabajos-listado .filtros-bar { padding:20px 22px; border-bottom:1px solid #eef1f6; background:#fafbfd; border-radius:12px 12px 0 0; }
        .trabajos-listado .filtros-titulo { display:flex; align-items:center; gap:9px; margin-bottom:14px; color:#5a6b82; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; }
        .trabajos-listado .filtros-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; align-items:end; }
        .trabajos-listado .filtros-grid label { display:flex; flex-direction:column; gap:6px; font-size:.78rem; font-weight:500; color:#7a8699; margin-bottom:0; }
        .trabajos-listado .filtros-grid select,
        .trabajos-listado .filtros-grid input { border:1.5px solid #d5dce6; border-radius:8px; background:#fff; padding:9px 10px; font-size:.9rem; color:#2b3247; }
        .trabajos-listado .filtros-grid select:focus,
        .trabajos-listado .filtros-grid input:focus { outline:none; border-color:#4f5fbf; box-shadow:0 0 0 .18rem rgba(79,95,191,.14); }
        .trabajos-listado .btn-limpiar { border:1.5px solid #d5dce6; border-radius:8px; background:#fff; padding:9px 14px; font-size:.85rem; font-weight:500; color:#7a8699; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; height:40px; }
        .trabajos-listado .btn-limpiar:hover { border-color:#b7c0cd; color:#4f5fbf; }

        /* Tabla */
        #trabajos-table { width:100% !important; border-collapse:separate; border-spacing:0; }
        #trabajos-table thead th {
            background:#eef1f6; color:#3a4658; font-weight:600; text-transform:uppercase;
            font-size:.72rem; letter-spacing:.03em; border-bottom:2px solid #d7dde6 !important;
            padding:.8rem .9rem; white-space:nowrap; vertical-align:middle;
        }
        #trabajos-table tbody td { padding:.9rem; vertical-align:middle; border-bottom:1px solid #eef0f3; font-size:.92rem; color:#2b3247; }
        #trabajos-table tbody tr { transition:background .12s; }
        #trabajos-table tbody tr:hover { background:#f6f8ff; }

        /* Chips / pills */
        .chip-cuadrilla { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:26px; padding:0 9px; background:#eef1f6; border-radius:6px; font-weight:600; font-size:.8rem; color:#4a5568; }
        .pill-poste { display:inline-flex; align-items:center; padding:5px 11px; border-radius:6px; font-size:.78rem; font-weight:600; color:#fff; }
        .pill-estado { display:inline-flex; align-items:center; padding:5px 12px; border-radius:20px; font-size:.78rem; font-weight:600; color:#fff; }
        .lpu-code { font-variant-numeric:tabular-nums; color:#5a6b82; }
        #trabajos-table .fw-500 { font-weight:500; color:#2b3247; }

        /* Acciones (prefijo #trabajos-table + !important para ganarle a los estilos de <button> del theme) */
        #trabajos-table .act-cell { display:flex; align-items:center; justify-content:flex-end; gap:6px; }
        #trabajos-table .act-cell form { margin:0; }
        #trabajos-table .act-btn,
        #trabajos-table .act-icon {
            display:inline-flex !important; align-items:center; justify-content:center; cursor:pointer;
            line-height:1 !important; box-shadow:none !important; text-decoration:none;
        }
        #trabajos-table .act-btn { gap:6px; height:34px; padding:0 12px !important; border:none !important; border-radius:7px !important; font-size:.82rem; font-weight:500; }
        #trabajos-table .act-autorizar { background:#2ba95f !important; color:#fff !important; }
        #trabajos-table .act-autorizar:hover { background:#238a4e !important; color:#fff !important; }
        #trabajos-table .act-icon {
            width:34px !important; height:34px !important; min-width:34px; padding:0 !important;
            border:1.5px solid #e2e6ee; border-radius:7px !important; background:#fff !important;
        }
        #trabajos-table .act-icon svg { display:block; }
        #trabajos-table .act-ver { color:#1b2a63 !important; } #trabajos-table .act-ver:hover { background:#eef1fb !important; border-color:#c9d2e6; }
        #trabajos-table .act-editar { color:#4f5fbf !important; } #trabajos-table .act-editar:hover { background:#eef0fb !important; border-color:#c9c9ef; }
        #trabajos-table .act-eliminar { color:#e05a45 !important; border-color:#f3d4cf; } #trabajos-table .act-eliminar:hover { background:#fdeeeb !important; border-color:#efbcb2; }

        /* Controles DataTables (buscar / mostrar N / paginación) */
        .trabajos-listado .dataTables_wrapper { padding:6px 22px 18px; }
        .trabajos-listado .dataTables_filter input { border:1.5px solid #d5dce6; border-radius:8px; background:#f7f9fc; padding:.45rem .7rem; margin-left:.4rem; }
        .trabajos-listado .dataTables_length select { border:1.5px solid #d5dce6; border-radius:8px; background:#f7f9fc; padding:.3rem 1.5rem .3rem .5rem; }
        .trabajos-listado .dataTables_paginate .paginate_button.current,
        .trabajos-listado .dataTables_paginate .paginate_button.current:hover { background:#4f5fbf !important; border-color:#4f5fbf !important; color:#fff !important; border-radius:7px; }
        .trabajos-listado .dataTables_paginate .paginate_button { border-radius:7px; }
        .trabajos-listado .dataTables_info { color:#8a97ab; }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid basic_table">
        <div class="page-title">
            <div class="row">
                <div class="col-12">
                    <h4>Carga de Trabajos</h4>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item text-dark">Trabajos</li>
                        <li class="breadcrumb-item active">Carga de Trabajos</li>
                    </ol>
                </div>
                @can('trabajos_ordenes.create')
                    <div class="mt-3 col-12 col-md-4 text-md-end">
                        <a href="{{ route('admin.trabajos.ordenes.create') }}" class="btn btn-primary">
                            Nuevo Trabajo
                        </a>
                    </div>
                @endcan
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card trabajos-listado">

                    {{-- Barra de filtros --}}
                    <div class="filtros-bar">
                        <div class="filtros-titulo">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8a97ab" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                            Filtros
                        </div>
                        <div class="filtros-grid">
                            <label>Cuadrilla
                                <select id="f_cuadrilla">
                                    <option value="">Todas las cuadrillas</option>
                                    @foreach($cuadrillasFiltro as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Estado
                                <select id="f_estado">
                                    <option value="">Todos los estados</option>
                                    @foreach($estadosFiltro as $val => $lab)
                                        <option value="{{ $val }}">{{ $lab }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Domicilio
                                <input type="text" id="f_domicilio" placeholder="Buscar dirección…">
                            </label>
                            <label>Fecha desde
                                <input type="date" id="f_desde">
                            </label>
                            <label>Fecha hasta
                                <input type="date" id="f_hasta">
                            </label>
                            <button type="button" class="btn-limpiar" id="f_limpiar">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Limpiar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive px-2">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
{!! $dataTable->scripts() !!}
<script>
(function () {
    function tabla() {
        return window.LaravelDataTables && window.LaravelDataTables['trabajos-table'];
    }
    function recargar() {
        var t = tabla();
        if (t) t.ajax.reload(null, false); // false = mantiene la página actual
    }

    var debounce;
    ['f_cuadrilla', 'f_estado', 'f_desde', 'f_hasta'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', recargar);
    });
    var dom = document.getElementById('f_domicilio');
    if (dom) dom.addEventListener('input', function () {
        clearTimeout(debounce);
        debounce = setTimeout(recargar, 400);
    });

    var limpiar = document.getElementById('f_limpiar');
    if (limpiar) limpiar.addEventListener('click', function () {
        ['f_cuadrilla', 'f_estado', 'f_domicilio', 'f_desde', 'f_hasta'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
        recargar();
    });
})();
</script>
@endsection
