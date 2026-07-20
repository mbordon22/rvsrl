@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6"><h4>Nuevo Período de Certificación</h4></div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.periodos.index') }}">Certificación</a></li>
                    <li class="breadcrumb-item active">Nuevo</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Datos del período</h5></div>
                <div class="card-body">
                    <div class="alert alert-info py-2">
                        Al crear el período, en su pantalla vas a poder elegir qué trabajos entran en la certificación.
                        Solo se ofrecen los trabajos <strong>aprobados</strong>, dentro del rango de fechas, de la
                        <strong>misma categoría</strong> (Mantenimiento/Obra) y de la cuadrilla si elegís una.
                    </div>
                    <form action="{{ route('admin.trabajos.periodos.store') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control form-control-lg" value="{{ old('nombre') }}"
                                placeholder="Ej: 1ra Quincena Mayo 2026" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Desde <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_desde" class="form-control form-control-lg" value="{{ old('fecha_desde') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Hasta <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-lg" value="{{ old('fecha_hasta') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Cuadrilla</label>
                            <select name="cuadrilla_id" class="form-select form-select-lg">
                                <option value="">Todas</option>
                                @foreach($cuadrillas as $c)
                                    <option value="{{ $c->id }}" {{ old('cuadrilla_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria" class="form-select form-select-lg" required>
                                @foreach($categorias as $val => $lab)
                                    <option value="{{ $val }}" {{ old('categoria') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Conteo en vivo de trabajos que coincidirían --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-3 rounded" style="background:#eef7f1;border:1px solid #bfe3cd;padding:14px 16px;">
                                <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width:38px;height:38px;border-radius:9px;background:#d6efe0;color:#1f8a4d;">
                                    <i data-feather="search" style="width:19px;"></i>
                                </div>
                                <div style="line-height:1.35">
                                    <div style="color:#1f7a3d;font-weight:500;">
                                        <strong id="cand-count" style="font-size:17px;font-weight:700;">—</strong>
                                        <span id="cand-word">trabajos aprobados</span> coinciden con estos criterios
                                    </div>
                                    <div style="font-size:12.5px;color:#5a9b73;">Se listarán para que los selecciones en la pantalla del período.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <a href="{{ route('admin.trabajos.periodos.index') }}" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-success btn-lg">Crear período</button>
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
    if (window.feather) { feather.replace(); }
(function () {
    var url    = "{{ route('admin.trabajos.periodos.contarCandidatos') }}";
    var desde  = document.querySelector('input[name="fecha_desde"]');
    var hasta  = document.querySelector('input[name="fecha_hasta"]');
    var cuad   = document.querySelector('select[name="cuadrilla_id"]');
    var cat    = document.querySelector('select[name="categoria"]');
    var out    = document.getElementById('cand-count');
    var word   = document.getElementById('cand-word');

    function actualizar() {
        if (!desde.value || !hasta.value || !cat.value) { out.textContent = '—'; return; }
        var qs = new URLSearchParams({
            fecha_desde: desde.value, fecha_hasta: hasta.value,
            categoria: cat.value, cuadrilla_id: cuad.value || ''
        });
        out.textContent = '…';
        fetch(url + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                out.textContent = d.count;
                word.textContent = (d.count === 1 ? 'trabajo aprobado' : 'trabajos aprobados');
            })
            .catch(function () { out.textContent = '—'; });
    }

    [desde, hasta, cuad, cat].forEach(function (el) {
        if (el) { el.addEventListener('change', actualizar); }
    });
    actualizar();
})();
</script>
@endsection
