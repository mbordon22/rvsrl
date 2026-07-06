@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>LPU / Tipos de Trabajo</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.lpu.index') }}">LPU</a></li>
                    <li class="breadcrumb-item active">Importar desde Excel</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Importar LPU desde Excel de Telecom</h4>
                    <a href="{{ route('admin.trabajos.lpu.index') }}" class="btn btn-light">
                        Volver al Listado
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <strong>Instrucciones:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Subí el archivo Excel de Telecom (el mismo que recibís para la certificación).</li>
                            <li>El sistema leerá la hoja <strong>"LPU"</strong> automáticamente.</li>
                            <li>La fecha de vigencia se toma de la celda D1 de esa hoja.</li>
                            <li>Si un código LPU ya existe, se actualizan sus datos (precio, vigencia).</li>
                            <li>El archivo puede pesar hasta 20MB.</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.trabajos.lpu.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Archivo Excel de Telecom (.xlsx)</label>
                            <input type="file" class="form-control form-control-lg" name="archivo" accept=".xlsx,.xls" required>
                            @error('archivo')
                                <span class="text-danger"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i data-feather="upload" class="me-1"></i> Importar LPU
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
<script>
    feather.replace();
</script>
@endsection
