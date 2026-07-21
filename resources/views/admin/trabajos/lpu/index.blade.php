@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid basic_table">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Catálogo LPU / Tipos de Trabajo</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">LPU</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="w-full d-flex justify-content-between gap-2 mb-3">
                @can('listado_lpu.create')
                <div class="col-xxl-2 col-sm-6 box-col-6">
                    <div class="card user-role">
                        <div class="card-body border-b-primary border-2">
                            <div class="upcoming-box">
                                <div class="upcoming-icon bg-primary">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-add') }}"></use>
                                    </svg>
                                </div>
                                <a href="{{ route('admin.trabajos.lpu.create') }}" class="btn btn-primary">Nuevo LPU</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <div class="col-xxl-5 col-sm-12 box-col-12 d-flex align-items-center">
                    <div class="card user-role w-100 mb-0">
                        <div class="card-body border-b-secondary border-2 py-3">
                            <div class="row text-center">
                                <div class="col-4 border-end">
                                    <h6 class="mb-1 text-muted">Vigencia LPU</h6>
                                    <h5 class="mb-0 fw-bold text-primary">
                                        {{ $vigencia ? \Carbon\Carbon::parse($vigencia)->format('d/m/Y') : '—' }}
                                    </h5>
                                </div>
                                <div class="col-4 border-end">
                                    <h6 class="mb-1 text-muted">Última importación</h6>
                                    <h5 class="mb-0 fw-bold text-success">
                                        {{ $ultimaImportacion ? \Carbon\Carbon::parse($ultimaImportacion)->format('d/m/Y H:i') : '—' }}
                                    </h5>
                                </div>
                                <div class="col-4">
                                    <h6 class="mb-1 text-muted">Total códigos</h6>
                                    <h5 class="mb-0 fw-bold">{{ number_format($totalRegistros, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <a href="{{ route('admin.importaciones.index') }}" class="text-decoration-none small">
                                    <i data-feather="clock" style="width:14px;height:14px;"></i> Ver historial de importaciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @can('listado_lpu.import')
                <div class="col-xxl-2 col-sm-6 box-col-6">
                    <div class="card user-role">
                        <div class="card-body border-b-success border-2">
                            <div class="upcoming-box">
                                <div class="upcoming-icon bg-success">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-import') }}"></use>
                                    </svg>
                                </div>
                                <a href="{{ route('admin.trabajos.lpu.showImport') }}" class="btn btn-success">Importar desde Excel</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
            <div class="col-sm-12">
                <div class="card">
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
