@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-8"><h4>Generar Excel de certificación</h4></div>
            <div class="col-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.periodos.show', $periodo->id) }}">{{ $periodo->nombre }}</a></li>
                    <li class="breadcrumb-item active">Generar Excel</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Subí la plantilla de Telecom</h5>
                    <a href="{{ route('admin.trabajos.periodos.show', $periodo->id) }}" class="btn btn-light btn-sm">Volver</a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Cómo funciona:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Subí el Excel de Telecom <strong>con los precios de LPU y materiales actualizados</strong>.</li>
                            <li>El sistema llena la hoja <strong>DETALLE</strong> con los {{ $periodo->trabajos_count }} trabajo(s) del período.</li>
                            <li>Las hojas <strong>CONSUMOS</strong> y <strong>CERTIFICACIÓN</strong> se calculan solas al abrir el archivo (usan los precios de la plantilla que subís).</li>
                            <li>Categoría de precio del período: <strong>{{ $periodo->categoria->label() }}</strong>.</li>
                        </ul>
                    </div>

                    @if($periodo->trabajos_count === 0)
                        <div class="alert alert-warning">Este período no tiene trabajos asignados. Asigná trabajos antes de generar el Excel.</div>
                    @endif

                    <form action="{{ route('admin.trabajos.periodos.exportar', $periodo->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Plantilla Excel de Telecom (.xlsx)</label>
                            <input type="file" class="form-control form-control-lg" name="archivo" accept=".xlsx,.xls" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success btn-lg" {{ $periodo->trabajos_count === 0 ? 'disabled' : '' }}>
                                <i data-feather="download" class="me-1"></i> Generar y descargar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script> feather.replace(); </script>
@endsection
