@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6"><h4>Editar Trabajo</h4></div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.ordenes.index') }}">Trabajos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <form action="{{ route('admin.trabajos.ordenes.update', $trabajo->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.trabajos.ordenes.fields')
            </form>

            @can('trabajos_ordenes.approve')
                <div class="card shadow-none border mb-4">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Aprobación</h6>
                        <span class="badge {{ $trabajo->estado?->badge() }}">{{ $trabajo->estado?->label() }}</span>
                    </div>
                    <div class="card-body">
                        @if($trabajo->estado === \App\Enums\EstadoTrabajo::APROBADO)
                            <p class="mb-1"><strong>Trabajo aprobado.</strong></p>
                            <p class="mb-0 text-muted">
                                OT: <strong>{{ $trabajo->ot ?: '—' }}</strong>
                                @if($trabajo->aprobadoPor)
                                    · Aprobado por {{ $trabajo->aprobadoPor->first_name }} {{ $trabajo->aprobadoPor->last_name }}
                                @endif
                                @if($trabajo->aprobado_at)
                                    · {{ $trabajo->aprobado_at->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        @else
                            <p class="text-muted">Revisá el trabajo (podés corregir lo que haga falta y guardar arriba), cargá la OT y aprobalo.</p>
                            <form action="{{ route('admin.trabajos.ordenes.aprobar', $trabajo->id) }}" method="POST" class="row g-2 align-items-end"
                                onsubmit="return confirm('¿Aprobar este trabajo? Quedará disponible para certificar.');">
                                @csrf
                                <div class="col-12 col-sm-4">
                                    <label class="form-label">N° / código de OT <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="ot" value="{{ old('ot', $trabajo->ot) }}" placeholder="Ej: OT-12345" required>
                                </div>
                                <div class="col-12 col-sm-auto">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fa fa-check me-1"></i> Aprobar trabajo
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.trabajos.ordenes.scripts')
@endsection
