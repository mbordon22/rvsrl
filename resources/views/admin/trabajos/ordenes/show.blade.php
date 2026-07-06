@extends('layouts.simple.master')

@section('main_content')
@php
    $fmt = fn ($v) => $v instanceof \BackedEnum ? $v->label() : ($v ?: '—');
    $si  = fn ($b) => $b ? 'Sí' : 'No';
@endphp
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6"><h4>Trabajo #{{ $trabajo->id }}</h4></div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.ordenes.index') }}">Trabajos</a></li>
                    <li class="breadcrumb-item active">Ver</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                {{ $trabajo->fecha?->format('d/m/Y') }} — Poste {{ $fmt($trabajo->tipo_poste) }}
                <span class="badge {{ $trabajo->estado?->badge() }}">{{ $trabajo->estado?->label() }}</span>
            </h5>
            <div>
                @can('trabajos_ordenes.edit')
                <a href="{{ route('admin.trabajos.ordenes.edit', $trabajo->id) }}" class="btn btn-primary btn-sm">Editar</a>
                @endcan
                <a href="{{ route('admin.trabajos.ordenes.index') }}" class="btn btn-light btn-sm">Volver</a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert {{ $trabajo->lpu ? 'alert-primary' : 'alert-warning' }} d-flex justify-content-between align-items-center">
                <div>
                    <strong>LPU asignado (automático):</strong>
                    @if($trabajo->lpu)
                        {{ $trabajo->lpu->codigo_lpu }} — {{ $trabajo->lpu->descripcion }}
                    @else
                        Sin asignar (ninguna regla coincidió — se puede definir manualmente al certificar)
                    @endif
                </div>
                @if($trabajo->lpu)
                    <span class="badge bg-primary">$ {{ number_format($trabajo->lpu->precio_mantenimiento, 2, ',', '.') }} (mant.)</span>
                @endif
            </div>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr><th>Cuadrilla</th><td>{{ $trabajo->cuadrilla?->nombre }}</td></tr>
                        <tr><th>Vehículo</th><td>{{ $trabajo->vehiculo?->patente ?? '—' }}</td></tr>
                        <tr><th>Domicilio</th><td>{{ $trabajo->domicilio ?: '—' }}</td></tr>
                        <tr><th>Central</th><td>{{ $fmt($trabajo->central) }} {{ $trabajo->central_aclarar }}</td></tr>
                        <tr><th>Armario</th><td>{{ $trabajo->armario ?: '—' }}</td></tr>
                        <tr><th>Red</th><td>{{ $fmt($trabajo->red) }}</td></tr>
                        <tr><th>Empleados</th><td>{{ $trabajo->empleados->map(fn($e)=>$e->first_name.' '.$e->last_name)->implode(', ') ?: '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @php
                        $colocoTxt = $trabajo->coloco_poste
                            ? ' — ' . $fmt($trabajo->poste_material) . ($trabajo->poste_reutilizado_material ? ' (reutilizado: ' . $fmt($trabajo->poste_reutilizado_material) . ')' : '')
                            : '';
                        $elementoTxt = $trabajo->elemento_tipo ? $fmt($trabajo->elemento_tipo) . ': ' . $trabajo->elemento_cantidad : '—';
                        $sifonTxt = $trabajo->sifon ? 'Sí — ' . $trabajo->sifon_cables . ' cables' : 'No — ' . $trabajo->protecciones_cantidad . ' protecciones';
                        $sueloTxt = $fmt($trabajo->tipo_suelo) . ($trabajo->rep_vereda ? ' · Rep. vereda' : '');
                        $otros = collect([
                            $trabajo->poda ? 'Poda' : null,
                            $trabajo->retensado ? 'Retensó cable/suspensor' : null,
                            $trabajo->bajadas ? 'Bajadas (' . $trabajo->bajadas_cantidad . ')' : null,
                        ])->filter()->implode(' · ') ?: '—';
                    @endphp
                    <table class="table table-sm">
                        <tr><th>Desmontó poste</th><td>{{ $si($trabajo->desmonto_poste) }}</td></tr>
                        <tr><th>Colocó poste</th><td>{{ $si($trabajo->coloco_poste) }}{{ $colocoTxt }}</td></tr>
                        <tr><th>Tamaño poste</th><td>{{ $fmt($trabajo->tamano_poste) }}</td></tr>
                        <tr><th>CDO / Caja Term. / NAP</th><td>{{ $elementoTxt }}</td></tr>
                        <tr><th>Sifón</th><td>{{ $sifonTxt }}</td></tr>
                        <tr><th>Rienda</th><td>{{ $si($trabajo->rienda) }} {{ $fmt($trabajo->rienda_tipo) }}</td></tr>
                        <tr><th>Tipo de suelo</th><td>{{ $sueloTxt }}</td></tr>
                        <tr><th>Otros</th><td>{{ $otros }}</td></tr>
                    </table>
                </div>
                @if($trabajo->observaciones)
                <div class="col-12"><strong>Observaciones:</strong> {{ $trabajo->observaciones }}</div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <h6>Materiales (sugeridos por reglas)</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Código</th><th>Material</th><th class="text-center">Cantidad</th><th>Origen</th></tr>
                        </thead>
                        <tbody>
                            @forelse($trabajo->materiales as $tm)
                                <tr>
                                    <td>{{ $tm->material?->codigo }}</td>
                                    <td>{{ $tm->material?->descripcion }}</td>
                                    <td class="text-center">{{ rtrim(rtrim(number_format($tm->cantidad, 2, ',', '.'), '0'), ',') }}</td>
                                    <td><span class="badge {{ $tm->origen === 'manual' ? 'bg-warning' : 'bg-secondary' }}">{{ ucfirst($tm->origen) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">Sin materiales (ninguna regla coincidió)</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Fotos ANTES</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($trabajo->getMedia('fotos_antes') as $m)
                            <a href="{{ $m->getUrl() }}" target="_blank"><img src="{{ $m->getUrl() }}" style="width:120px;height:120px;object-fit:cover;border-radius:6px;"></a>
                        @empty <span class="text-muted">Sin fotos</span> @endforelse
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Fotos DESPUÉS</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($trabajo->getMedia('fotos_despues') as $m)
                            <a href="{{ $m->getUrl() }}" target="_blank"><img src="{{ $m->getUrl() }}" style="width:120px;height:120px;object-fit:cover;border-radius:6px;"></a>
                        @empty <span class="text-muted">Sin fotos</span> @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
