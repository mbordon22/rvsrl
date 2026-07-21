@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/photoswipe.css') }}">
<style>
    .sw-gallery { margin:0; }
    .sw-gallery .sw-fig { margin:0; cursor:pointer; }
    .sw-gallery .sw-fig img { display:block; width:120px; height:120px; object-fit:cover; border-radius:6px; transition:opacity .15s; }
    .sw-gallery .sw-fig:hover img { opacity:.85; }
    .sw-gallery .sw-figcap { display:none; }

    /* ===== Rediseño MOBILE (Ver Trabajo) — solo <768px ===== */
    .vt-mob .vt-grid { display:grid; grid-template-columns:1fr; gap:10px; }
    .vt-mob .vt-field { background:#f7f9fb; border:1px solid #eceff3; border-radius:12px; padding:13px 16px; }
    .vt-mob .vt-lbl { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#98a3b3; margin-bottom:5px; }
    .vt-mob .vt-val { font-size:16px; font-weight:500; color:#5f8b83; line-height:1.4; word-break:break-word; }
    .vt-mob .vt-mat { background:#f7f9fb; border:1px solid #eceff3; border-radius:12px; padding:14px 16px; }
    .vt-mob .vt-mat-top { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:7px; }
    .vt-mob .vt-codigo { font-size:13px; font-weight:600; color:#98a3b3; }
    .vt-mob .vt-origen { display:inline-flex; align-items:center; padding:4px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#fff; }
    .vt-mob .vt-desc { font-size:16px; font-weight:500; color:#5f8b83; line-height:1.4; margin-bottom:8px; }
    .vt-mob .vt-cant { font-size:14px; color:#7a8698; }
    .vt-mob .vt-cant strong { color:#2b3648; font-weight:600; }
    /* Encabezado mobile de la card: título/badge apilados + botones full width */
    .vt-head-mob .vt-actions { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .vt-head-mob .vt-actions .btn { width:100%; }
</style>
@endsection

@section('main_content')
@php
    $fmt = fn ($v) => $v instanceof \BackedEnum ? $v->label() : ($v ?: '—');
    $si  = fn ($b) => $b ? 'Sí' : 'No';

    // Dimensiones reales de la imagen (para PhotoSwipe data-size). Fallback si no se puede leer.
    $dim = function ($media) {
        try {
            $info = @getimagesize($media->getPath());
            return $info ? ($info[0] . 'x' . $info[1]) : '1600x1200';
        } catch (\Throwable $e) {
            return '1600x1200';
        }
    };

    // Valores derivados del bloque "Trabajo realizado" (reusados en desktop y mobile).
    $colocoTxt = $trabajo->coloco_poste
        ? ' — ' . $fmt($trabajo->poste_material) . ($trabajo->poste_reutilizado_material ? ' (reutilizado: ' . $fmt($trabajo->poste_reutilizado_material) . ')' : '')
        : '';
    $elementoTxt = collect([
        $trabajo->cdo_cantidad ? 'CDO: ' . $trabajo->cdo_cantidad : null,
        $trabajo->caja_terminal_cantidad ? 'Caja Terminal: ' . $trabajo->caja_terminal_cantidad : null,
        $trabajo->nap_cantidad ? 'NAP: ' . $trabajo->nap_cantidad : null,
    ])->filter()->implode(' · ') ?: '—';
    $sifonTxt = $trabajo->sifon
        ? 'Sí — ' . ($trabajo->sifon_cables ?? 0) . ' cables · ' . ($trabajo->protecciones_cantidad ?? 0) . ' protecciones'
        : 'No';
    $riendaTxt = collect([
        $trabajo->rienda_pique_cantidad ? 'Pique: ' . $trabajo->rienda_pique_cantidad : null,
        $trabajo->rienda_tierra_cantidad ? 'Tierra: ' . $trabajo->rienda_tierra_cantidad : null,
        $trabajo->rienda_pluma_cantidad ? 'Pluma: ' . $trabajo->rienda_pluma_cantidad : null,
    ])->filter()->implode(' · ') ?: '—';
    $sueloTxt = $fmt($trabajo->tipo_suelo) . ($trabajo->rep_vereda ? ' · Rep. vereda' : '');
    $otros = collect([
        $trabajo->poda ? 'Poda' : null,
        $trabajo->retensado ? 'Retensó cable/suspensor (' . ($trabajo->retensado_cantidad ?? 0) . ')' : null,
        $trabajo->bajadas ? 'Bajadas (' . $trabajo->bajadas_cantidad . ')' : null,
    ])->filter()->implode(' · ') ?: '—';

    $empleadosTxt = $trabajo->empleados->map(fn($e)=>$e->first_name.' '.$e->last_name)->implode(', ') ?: '—';
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
        {{-- Encabezado DESKTOP (≥768px): igual que antes --}}
        <div class="card-header d-none d-md-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                {{ $trabajo->fecha?->format('d/m/Y') }} — Poste {{ $fmt($trabajo->tipo_poste) }}
                <span class="badge {{ $trabajo->estado?->badge() }}">{{ $trabajo->estado?->label() }}</span>
            </h5>
            <div>
                @can('update', $trabajo)
                <a href="{{ route('admin.trabajos.ordenes.edit', $trabajo->id) }}" class="btn btn-primary btn-sm">Editar</a>
                @endcan
                <a href="{{ route('admin.trabajos.ordenes.index') }}" class="btn btn-light btn-sm">Volver</a>
            </div>
        </div>

        {{-- Encabezado MOBILE (<768px): título/badge apilados + botones full width --}}
        <div class="card-header d-md-none vt-head-mob">
            <h5 class="mb-2">{{ $trabajo->fecha?->format('d/m/Y') }} — Poste {{ $fmt($trabajo->tipo_poste) }}</h5>
            <div class="mb-3">
                <span class="badge {{ $trabajo->estado?->badge() }}">{{ $trabajo->estado?->label() }}</span>
            </div>
            <div class="vt-actions">
                @can('update', $trabajo)
                <a href="{{ route('admin.trabajos.ordenes.edit', $trabajo->id) }}" class="btn btn-primary btn-sm">Editar</a>
                @endcan
                <a href="{{ route('admin.trabajos.ordenes.index') }}" class="btn btn-light btn-sm">Volver</a>
            </div>
        </div>

        <div class="card-body">
            {{-- El LPU y su precio SOLO para quien aprueba/certifica. Los empleados
                 no deben ver precios ni importes en esta vista. --}}
            @can('trabajos_ordenes.approve')
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
            @endcan

            {{-- ===== DETALLE — DESKTOP (dos columnas, tablas) ===== --}}
            <div class="row d-none d-md-flex">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr><th>Cuadrilla</th><td>{{ $trabajo->cuadrilla?->nombre }}</td></tr>
                        <tr><th>Vehículo</th><td>{{ $trabajo->vehiculo?->patente ?? '—' }}</td></tr>
                        <tr><th>Domicilio</th><td>{{ $trabajo->domicilio ?: '—' }}</td></tr>
                        <tr><th>Ubicación</th><td>
                            @if($trabajo->latitud && $trabajo->longitud)
                                <a href="https://www.google.com/maps?q={{ $trabajo->latitud }},{{ $trabajo->longitud }}" target="_blank">
                                    {{ $trabajo->latitud }}, {{ $trabajo->longitud }} — Ver en Maps
                                </a>
                            @else — @endif
                        </td></tr>
                        <tr><th>Central</th><td>{{ $fmt($trabajo->central) }} {{ $trabajo->central_aclarar }}</td></tr>
                        <tr><th>Tipo de trabajo</th><td>{{ $fmt($trabajo->categoria) }}</td></tr>
                        <tr><th>Empleados</th><td>{{ $empleadosTxt }}</td></tr>
                        <tr><th>OT</th><td>{{ $trabajo->ot ?: '—' }}</td></tr>
                        @if($trabajo->aprobado_at)
                        <tr><th>Aprobado</th><td>
                            {{ $trabajo->aprobado_at->format('d/m/Y H:i') }}
                            @if($trabajo->aprobadoPor) · {{ $trabajo->aprobadoPor->first_name }} {{ $trabajo->aprobadoPor->last_name }} @endif
                        </td></tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr><th>Desmontó poste</th><td>{{ $si($trabajo->desmonto_poste) }}</td></tr>
                        <tr><th>Colocó poste</th><td>{{ $si($trabajo->coloco_poste) }}{{ $colocoTxt }}</td></tr>
                        <tr><th>Tamaño poste</th><td>{{ $fmt($trabajo->tamano_poste) }}</td></tr>
                        <tr><th>CDO / Caja Term. / NAP</th><td>{{ $elementoTxt }}</td></tr>
                        <tr><th>Sifón</th><td>{{ $sifonTxt }}</td></tr>
                        <tr><th>Rienda</th><td>{{ $si($trabajo->rienda) }}{{ $trabajo->rienda ? ' — ' . $riendaTxt : '' }}</td></tr>
                        <tr><th>Tipo de suelo</th><td>{{ $sueloTxt }}</td></tr>
                        <tr><th>Otros</th><td>{{ $otros }}</td></tr>
                    </table>
                </div>
            </div>

            {{-- ===== DETALLE — MOBILE (tarjetas apiladas) ===== --}}
            <div class="vt-mob d-md-none">
                <div class="vt-grid">
                    <div class="vt-field"><div class="vt-lbl">Cuadrilla</div><div class="vt-val">{{ $trabajo->cuadrilla?->nombre ?: '—' }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Vehículo</div><div class="vt-val">{{ $trabajo->vehiculo?->patente ?? '—' }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Domicilio</div><div class="vt-val">{{ $trabajo->domicilio ?: '—' }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Ubicación</div><div class="vt-val">
                        @if($trabajo->latitud && $trabajo->longitud)
                            <a href="https://www.google.com/maps?q={{ $trabajo->latitud }},{{ $trabajo->longitud }}" target="_blank">{{ $trabajo->latitud }}, {{ $trabajo->longitud }} — Ver en Maps</a>
                        @else — @endif
                    </div></div>
                    <div class="vt-field"><div class="vt-lbl">Central</div><div class="vt-val">{{ $fmt($trabajo->central) }} {{ $trabajo->central_aclarar }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Tipo de trabajo</div><div class="vt-val">{{ $fmt($trabajo->categoria) }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Empleados</div><div class="vt-val">{{ $empleadosTxt }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">OT</div><div class="vt-val">{{ $trabajo->ot ?: '—' }}</div></div>
                    @if($trabajo->aprobado_at)
                    <div class="vt-field"><div class="vt-lbl">Aprobado</div><div class="vt-val">
                        {{ $trabajo->aprobado_at->format('d/m/Y H:i') }}@if($trabajo->aprobadoPor) · {{ $trabajo->aprobadoPor->first_name }} {{ $trabajo->aprobadoPor->last_name }} @endif
                    </div></div>
                    @endif
                    <div class="vt-field"><div class="vt-lbl">Desmontó poste</div><div class="vt-val">{{ $si($trabajo->desmonto_poste) }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Colocó poste</div><div class="vt-val">{{ $si($trabajo->coloco_poste) }}{{ $colocoTxt }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Tamaño poste</div><div class="vt-val">{{ $fmt($trabajo->tamano_poste) }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">CDO / Caja Term. / NAP</div><div class="vt-val">{{ $elementoTxt }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Sifón</div><div class="vt-val">{{ $sifonTxt }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Rienda</div><div class="vt-val">{{ $si($trabajo->rienda) }}{{ $trabajo->rienda ? ' — ' . $riendaTxt : '' }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Tipo de suelo</div><div class="vt-val">{{ $sueloTxt }}</div></div>
                    <div class="vt-field"><div class="vt-lbl">Otros</div><div class="vt-val">{{ $otros }}</div></div>
                </div>
            </div>

            {{-- ===== OBSERVACIONES (compartido) ===== --}}
            @if($trabajo->observaciones)
            <div class="mt-3"><strong>Observaciones:</strong> {{ $trabajo->observaciones }}</div>
            @endif
            @if($trabajo->getMedia('fotos_observaciones')->isNotEmpty())
            <div class="mt-2">
                <strong>Imágenes de observaciones:</strong>
                <div class="my-gallery sw-gallery d-flex flex-wrap gap-2 mt-1" itemscope>
                    @foreach($trabajo->getMedia('fotos_observaciones') as $i => $m)
                        <figure class="sw-fig" itemprop="associatedMedia" itemscope>
                            <a href="{{ $m->getUrl() }}" itemprop="contentUrl" data-size="{{ $dim($m) }}">
                                <img src="{{ $m->getUrl() }}" itemprop="thumbnail" alt="Observación {{ $i + 1 }}">
                            </a>
                            <figcaption class="sw-figcap">Observación {{ $i + 1 }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ===== MATERIALES ===== --}}
            <div class="mt-3">
                <h6>Materiales Utilizados</h6>

                {{-- Tabla DESKTOP --}}
                <table class="table table-sm table-bordered d-none d-md-table">
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

                {{-- Tarjetas MOBILE --}}
                <div class="vt-mob d-md-none">
                    @forelse($trabajo->materiales as $tm)
                        <div class="vt-mat {{ !$loop->first ? 'mt-2' : '' }}">
                            <div class="vt-mat-top">
                                <span class="vt-codigo">{{ $tm->material?->codigo }}</span>
                                <span class="vt-origen" style="background:{{ $tm->origen === 'manual' ? '#e79f5f' : '#9aa6b8' }}">{{ ucfirst($tm->origen) }}</span>
                            </div>
                            <div class="vt-desc">{{ $tm->material?->descripcion }}</div>
                            <div class="vt-cant">Cantidad: <strong>{{ rtrim(rtrim(number_format($tm->cantidad, 2, ',', '.'), '0'), ',') }}</strong></div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-2">Sin materiales (ninguna regla coincidió)</div>
                    @endforelse
                </div>
            </div>

            {{-- ===== FOTOS (responsive, compartido) ===== --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Fotos ANTES</h6>
                    @if($trabajo->getMedia('fotos_antes')->isNotEmpty())
                        <div class="my-gallery sw-gallery d-flex flex-wrap gap-2" itemscope>
                            @foreach($trabajo->getMedia('fotos_antes') as $i => $m)
                                <figure class="sw-fig" itemprop="associatedMedia" itemscope>
                                    <a href="{{ $m->getUrl() }}" itemprop="contentUrl" data-size="{{ $dim($m) }}">
                                        <img src="{{ $m->getUrl() }}" itemprop="thumbnail" alt="Foto antes {{ $i + 1 }}">
                                    </a>
                                    <figcaption class="sw-figcap">Antes {{ $i + 1 }}</figcaption>
                                </figure>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">Sin fotos</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>Fotos DESPUÉS</h6>
                    @if($trabajo->getMedia('fotos_despues')->isNotEmpty())
                        <div class="my-gallery sw-gallery d-flex flex-wrap gap-2" itemscope>
                            @foreach($trabajo->getMedia('fotos_despues') as $i => $m)
                                <figure class="sw-fig" itemprop="associatedMedia" itemscope>
                                    <a href="{{ $m->getUrl() }}" itemprop="contentUrl" data-size="{{ $dim($m) }}">
                                        <img src="{{ $m->getUrl() }}" itemprop="thumbnail" alt="Foto después {{ $i + 1 }}">
                                    </a>
                                    <figcaption class="sw-figcap">Después {{ $i + 1 }}</figcaption>
                                </figure>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">Sin fotos</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Root de PhotoSwipe (requerido, uno por página) --}}
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Cerrar (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Compartir"></button>
                <button class="pswp__button pswp__button--fs" title="Pantalla completa"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Anterior"></button>
            <button class="pswp__button pswp__button--arrow--right" title="Siguiente"></button>
            <div class="pswp__caption"><div class="pswp__caption__center"></div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/photoswipe/photoswipe.min.js') }}"></script>
<script src="{{ asset('assets/js/photoswipe/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('assets/js/photoswipe/photoswipe.js') }}"></script>
@endsection
