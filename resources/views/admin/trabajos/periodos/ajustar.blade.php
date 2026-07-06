@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-8"><h4>Ajustar trabajo #{{ $trabajo->id }}</h4></div>
            <div class="col-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.periodos.show', $periodo->id) }}">{{ $periodo->nombre }}</a></li>
                    <li class="breadcrumb-item active">Ajustar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="alert alert-secondary py-2 small mb-0 flex-grow-1">
            {{ $trabajo->fecha->format('d/m/Y') }} · {{ $trabajo->cuadrilla?->nombre }} · {{ $trabajo->domicilio }}
            · Poste: {{ $trabajo->tipo_poste?->label() ?? '—' }}
            @if($trabajo->coloco_poste) · Colocó {{ $trabajo->poste_material?->label() }} {{ $trabajo->tamano_poste?->label() }} @endif
        </div>
        {{-- Form independiente para regenerar (fuera del form principal) --}}
        <form action="{{ route('admin.trabajos.periodos.regenerarMateriales', [$periodo->id, $trabajo->id]) }}" method="POST"
            onsubmit="return confirm('Esto reemplaza los materiales por los de las reglas. ¿Continuar?')">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">Regenerar materiales desde reglas</button>
        </form>
    </div>

    <form action="{{ route('admin.trabajos.periodos.guardarAjuste', [$periodo->id, $trabajo->id]) }}" method="POST">
        @csrf @method('PUT')

        {{-- LPU override --}}
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Código LPU</h5></div>
            <div class="card-body">
                <select name="lpu_id" id="lpu_select" class="form-select select2-lpu">
                    <option value="">— Sin LPU —</option>
                    @foreach($lpus as $lpu)
                        <option value="{{ $lpu->id }}" {{ $trabajo->lpu_id == $lpu->id ? 'selected' : '' }}>
                            {{ $lpu->codigo_lpu }} — {{ \Illuminate\Support\Str::limit($lpu->descripcion, 60) }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Podés sobrescribir manualmente el LPU asignado por las reglas.</small>
            </div>
        </div>

        {{-- Materiales --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Materiales</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr><th>Código</th><th>Material</th><th style="width:140px;">Cantidad</th></tr>
                    </thead>
                    <tbody>
                        @forelse($trabajo->materiales as $tm)
                            <tr>
                                <td>{{ $tm->material?->codigo }}<input type="hidden" name="material_id[]" value="{{ $tm->material_id }}"></td>
                                <td class="small">{{ $tm->material?->descripcion }}</td>
                                <td><input type="number" step="0.01" min="0" name="cantidad[]" class="form-control form-control-sm" value="{{ rtrim(rtrim(number_format($tm->cantidad,2,'.',''),'0'),'.') }}"></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center">Sin materiales</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <p class="text-muted small mb-2">Poné cantidad 0 para quitar un material. Para agregar uno nuevo, indicá su código:</p>
                <div class="row g-2">
                    <div class="col-sm-4"><input type="text" name="nuevo_codigo" class="form-control form-control-sm" placeholder="Código de material"></div>
                    <div class="col-sm-3"><input type="number" step="0.01" min="0" name="nuevo_cantidad" class="form-control form-control-sm" placeholder="Cantidad"></div>
                </div>
            </div>
        </div>

        <div class="text-end pb-4">
            <a href="{{ route('admin.trabajos.periodos.show', $periodo->id) }}" class="btn btn-light">Cancelar</a>
            <button type="submit" class="btn btn-success">Guardar ajuste</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#lpu_select').select2({
            width: '100%',
            placeholder: 'Buscá por código o descripción…',
            allowClear: true
        });
    });
</script>
@endsection
