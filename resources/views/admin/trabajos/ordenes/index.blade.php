@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
        #trabajos-table { width:100% !important; border-collapse:separate; border-spacing:0; }
        #trabajos-table thead th {
            background:#eef1f6; color:#3a4658; font-weight:600; text-transform:uppercase;
            font-size:.72rem; letter-spacing:.03em; border-bottom:2px solid #d7dde6 !important;
            padding:.7rem .75rem; white-space:nowrap; vertical-align:middle;
        }
        #trabajos-table tbody td { padding:.7rem .75rem; vertical-align:middle; border-bottom:1px solid #eef0f3; }
        #trabajos-table tbody tr { transition:background .12s; }
        #trabajos-table tbody tr:hover { background:#f5f8ff; }
        #trabajos-table .badge { font-size:.75rem; padding:.4em .7em; font-weight:600; }
        /* Controles (buscar / mostrar N) */
        .trabajos-wrap .dataTables_filter input {
            border:1.5px solid #97a2b2; border-radius:8px; background:#f7f9fc; padding:.35rem .6rem; margin-left:.4rem;
        }
        .trabajos-wrap .dataTables_length select {
            border:1.5px solid #97a2b2; border-radius:8px; background:#f7f9fc; padding:.25rem 1.5rem .25rem .5rem;
        }
        .trabajos-wrap .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background:#4f5fbf !important; border-color:#4f5fbf !important; color:#fff !important; border-radius:6px;
        }
        /* Acciones (iconos feather) alineados */
        #trabajos-table td .action-div { display:flex; gap:.5rem; justify-content:center; align-items:center; }
        #trabajos-table td .action-div a { color:#5a6b82; }
        #trabajos-table td .action-div a:hover { color:#4f5fbf; }
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
                <div class="card trabajos-wrap">
                    <div class="card-block row">
                        <div class="user-table">
                            <div class="table-responsive p-3">
                                {!! $dataTable->table() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
{!! $dataTable->scripts() !!}
@endsection
