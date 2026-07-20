@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/photoswipe.css') }}">
@endsection

@section('main_content')
@php
    $t = $trabajo;

    // Valor actual de un campo (old() para reponer tras validación)
    $v   = function (string $f, $default = '') use ($t) {
        $cur = $t?->{$f};
        if ($cur instanceof \BackedEnum) $cur = $cur->value;
        return old($f, $cur ?? $default);
    };
    $chk = fn (string $f) => old($f, $t?->{$f}) ? 'checked' : '';
    $fmt = fn ($val) => $val instanceof \BackedEnum ? $val->label() : ($val !== null && $val !== '' ? $val : '—');
    $fecha = old('fecha', $t->fecha?->format('Y-m-d'));

    $estado    = $t->estado;
    $pendiente = $estado === \App\Enums\EstadoTrabajo::PENDIENTE;
    $aprobado  = $estado === \App\Enums\EstadoTrabajo::APROBADO;
    $certificado = $estado === \App\Enums\EstadoTrabajo::CERTIFICADO;

    $statusBg = match ($estado) {
        \App\Enums\EstadoTrabajo::APROBADO    => '#e5f6ea',
        \App\Enums\EstadoTrabajo::CERTIFICADO => '#eef1f6',
        default                               => '#fdf1e3',
    };
    $statusColor = $estado?->color() ?? '#c0803a';

    $initials = function ($nombre) {
        $parts = preg_split('/\s+/', trim($nombre)) ?: [];
        return strtoupper(mb_substr($parts[0] ?? '', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
    };

    // Dimensiones reales de la imagen (para PhotoSwipe data-size). Fallback si no se puede leer.
    $dim = function ($media) {
        try {
            $info = @getimagesize($media->getPath());
            return $info ? ($info[0] . 'x' . $info[1]) : '1600x1200';
        } catch (\Throwable $e) {
            return '1600x1200';
        }
    };

    // Lista compacta de preguntas para la vista de lectura de "Trabajo realizado"
    $chipDetalle = 'detalle';
    $preguntas = [
        ['num' => 1, 'label' => '¿Se desmontó poste?', 'on' => (bool) $t->desmonto_poste, 'sub' => []],
        ['num' => 2, 'label' => '¿Se colocó poste?', 'on' => (bool) $t->coloco_poste, 'sub' => $t->coloco_poste ? [
            ['k' => 'Tamaño', 'v' => $fmt($t->tamano_poste)],
            ['k' => 'Material', 'v' => $fmt($t->poste_material) . ($t->poste_reutilizado_material ? ' (reut.: ' . $fmt($t->poste_reutilizado_material) . ')' : '')],
        ] : []],
        ['num' => 3, 'label' => 'CDO / Caja Terminal / NAP', 'detalle' => true, 'sub' => array_values(array_filter([
            $t->cdo_cantidad ? ['k' => 'CDO', 'v' => $t->cdo_cantidad] : null,
            $t->caja_terminal_cantidad ? ['k' => 'Caja Terminal', 'v' => $t->caja_terminal_cantidad] : null,
            $t->nap_cantidad ? ['k' => 'NAP', 'v' => $t->nap_cantidad] : null,
        ]))],
        ['num' => 4, 'label' => '¿Tiene sifón?', 'on' => (bool) $t->sifon, 'sub' => $t->sifon ? [
            ['k' => 'Cables', 'v' => $t->sifon_cables ?? 0],
            ['k' => 'Protecciones', 'v' => $t->protecciones_cantidad ?? 0],
        ] : []],
        ['num' => 5, 'label' => '¿Tiene rienda?', 'on' => (bool) $t->rienda, 'sub' => $t->rienda ? array_values(array_filter([
            $t->rienda_pique_cantidad ? ['k' => 'Pique', 'v' => $t->rienda_pique_cantidad] : null,
            $t->rienda_tierra_cantidad ? ['k' => 'Tierra', 'v' => $t->rienda_tierra_cantidad] : null,
            $t->rienda_pluma_cantidad ? ['k' => 'Pluma', 'v' => $t->rienda_pluma_cantidad] : null,
        ])) : []],
        ['num' => 6, 'label' => 'Tipo de suelo', 'detalle' => true, 'sub' => [
            ['k' => 'Suelo', 'v' => $fmt($t->tipo_suelo)],
            ['k' => 'Rep. vereda', 'v' => $t->rep_vereda ? 'Sí' : 'No'],
        ]],
        ['num' => 7, 'label' => '¿Se realizó poda?', 'on' => (bool) $t->poda, 'sub' => []],
        ['num' => 8, 'label' => '¿Se retensó cable o suspensor?', 'on' => (bool) $t->retensado, 'sub' => $t->retensado && $t->retensado_cantidad ? [
            ['k' => 'Cantidad', 'v' => $t->retensado_cantidad],
        ] : []],
        ['num' => 9, 'label' => '¿Se traspasó cable de bajada?', 'on' => (bool) $t->bajadas, 'sub' => $t->bajadas && $t->bajadas_cantidad ? [
            ['k' => 'Bajadas', 'v' => $t->bajadas_cantidad],
        ] : []],
    ];
@endphp

<style>
    .rv-revisar { color:#2b3247; }
    .rv-revisar a { color:#4f5fbf; text-decoration:none; }
    .rv-revisar .rv-card { background:#fff; border:1px solid #e6eaf1; border-radius:12px; overflow:hidden; margin-bottom:16px; }
    .rv-revisar .rv-card-head { background:#eef1f6; border-bottom:1px solid #dde2ea; padding:11px 20px; display:flex; align-items:center; justify-content:space-between; }
    .rv-revisar .rv-card-head h6 { margin:0; font-size:15px; font-weight:600; color:#2b3648; }
    .rv-revisar .rv-card-body { padding:20px; }

    .rv-revisar .rv-status { display:inline-flex; align-items:center; gap:7px; padding:5px 13px; border-radius:20px; font-size:13px; font-weight:600; }
    .rv-revisar .rv-status .dot { width:7px; height:7px; border-radius:50%; }

    /* Tira de contexto */
    .rv-revisar .rv-context { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
    .rv-revisar .rv-context .lbl { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.07em; color:#9aa6b8; margin-bottom:4px; }
    .rv-revisar .rv-context .val { font-size:15px; font-weight:500; color:#2b3247; }

    /* Toggle lectura/edición */
    .rv-revisar .rv-edit { display:none; }
    .rv-revisar .rv-section.editing .rv-read { display:none; }
    .rv-revisar .rv-section.editing .rv-edit { display:block; animation:rvIn .2s ease both; }
    @keyframes rvIn { from{opacity:0; transform:translateY(-4px)} to{opacity:1; transform:translateY(0)} }

    .rv-revisar .rv-editbtn { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:500; color:#4f5fbf; cursor:pointer; user-select:none; }
    .rv-revisar .rv-section.editing .rv-editbtn { color:#28a745; font-weight:600; }
    .rv-revisar .rv-editbtn .lbl-listo { display:none; }
    .rv-revisar .rv-section.editing .rv-editbtn .lbl-editar { display:none; }
    .rv-revisar .rv-section.editing .rv-editbtn .lbl-listo { display:inline; }

    /* Grillas de lectura */
    .rv-revisar .rv-read-grid { display:grid; gap:18px 24px; }
    .rv-revisar .rv-read-grid .k { font-size:12px; color:#9aa6b8; margin-bottom:3px; }
    .rv-revisar .rv-read-grid .val { font-size:15px; color:#2b3247; }

    /* Inputs */
    .rv-revisar .rv-field { margin-bottom:14px; }
    .rv-revisar .rv-field > label,
    .rv-revisar .rv-field .form-label { display:block; font-size:13px; font-weight:500; color:#3a4658; margin-bottom:6px; }
    .rv-revisar input.form-control, .rv-revisar select.form-select, .rv-revisar textarea.form-control {
        border:1.6px solid #97a2b2 !important; background:#f7f9fc !important; border-radius:8px !important;
    }
    .rv-revisar input.form-control:focus, .rv-revisar select.form-select:focus, .rv-revisar textarea.form-control:focus {
        border-color:#3c82ff !important; background:#fff !important; box-shadow:0 0 0 .18rem rgba(60,130,255,.15);
    }

    /* Chips de integrantes */
    .rv-revisar .rv-chip { display:inline-flex; align-items:center; gap:7px; padding:7px 13px 7px 9px; border-radius:24px; font-size:14px; font-weight:500; background:#eef0fb; color:#3949ab; border:1px solid #d7ddf6; }
    .rv-revisar .rv-chip .ini { width:22px; height:22px; border-radius:50%; background:#4f5fbf; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; }

    /* Preguntas — lectura */
    .rv-revisar .rv-q { border-bottom:1px solid #f0f2f7; padding:12px 0; }
    .rv-revisar .rv-q:last-child { border-bottom:0; }
    .rv-revisar .rv-q-top { display:flex; align-items:center; justify-content:space-between; gap:14px; }
    .rv-revisar .rv-q-label { font-size:14.5px; color:#2b3648; }
    .rv-revisar .rv-q-num { color:#9aa6b8; font-weight:600; margin-right:8px; }
    .rv-revisar .rv-qchip { display:inline-flex; align-items:center; padding:4px 13px; border-radius:20px; font-size:13px; font-weight:600; flex:none; }
    .rv-revisar .rv-qchip.si { background:#e5f6ea; color:#1f7a3d; border:1px solid #a9dcbb; }
    .rv-revisar .rv-qchip.no { background:#eef1f6; color:#8a97ab; border:1px solid #e0e5ee; font-weight:500; }
    .rv-revisar .rv-qchip.det { background:#eef4fd; color:#3f6fd8; border:1px solid #cfe0f5; font-weight:500; }
    .rv-revisar .rv-subtags { margin-top:8px; display:flex; flex-wrap:wrap; gap:8px; }
    .rv-revisar .rv-subtag { display:inline-flex; align-items:center; gap:6px; background:#f4f6fa; border:1px solid #e6eaf1; border-radius:7px; padding:5px 11px; font-size:13px; color:#5a6b82; }
    .rv-revisar .rv-subtag strong { color:#2b3247; font-weight:600; }

    /* Preguntas — edición (bloques como en el form de carga) */
    .rv-revisar .pregunta { background:#f7f9fc; border:1px solid #e9edf3; border-radius:10px; padding:13px 15px; margin-bottom:11px; }
    .rv-revisar .pregunta .pregunta-titulo { font-weight:500; color:#2b3648; margin-bottom:0; }
    .rv-revisar .pregunta .sub { margin-top:12px; }
    .rv-revisar .form-switch .form-check-input { width:2.6em; height:1.35em; cursor:pointer; border-color:#97a2b2; }
    .rv-revisar .form-switch .form-check-input:checked { background-color:#28a745; border-color:#28a745; }
    /* Los .conditional arrancan ocultos vía JS (apply() en load); su display lo
       controla el script para evitar que una regla CSS le gane al style inline. */

    /* Select2 acorde al alto de los inputs */
    .rv-revisar .select2-container .select2-selection--single { height:38px; border:1.6px solid #97a2b2; border-radius:8px; }
    .rv-revisar .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:36px; }
    .rv-revisar .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; }

    /* Materiales */
    .rv-revisar .rv-mat-card { border-color:#cfe0f5; box-shadow:0 0 0 1px #eaf1fb inset; }
    .rv-revisar .rv-mat-card .rv-card-head { background:#eef4fd; border-bottom-color:#d5e3f6; }
    .rv-revisar .rv-mat-card .rv-card-head h6 { color:#264a86; }
    .rv-revisar table.rv-mat { width:100%; border:1px solid #e6eaf1; border-radius:10px; border-collapse:separate; border-spacing:0; overflow:hidden; }
    .rv-revisar table.rv-mat th { background:#f7f9fc; border-bottom:1px solid #e6eaf1; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#8a97ab; padding:10px 12px; text-align:left; }
    .rv-revisar table.rv-mat td { border-bottom:1px solid #f0f2f7; padding:8px 12px; font-size:14px; vertical-align:middle; }
    .rv-revisar table.rv-mat tr:last-child td { border-bottom:0; }
    .rv-revisar .rv-mat .btn-rm { width:30px; height:30px; border:0; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; color:#c05555; background:transparent; }
    .rv-revisar .rv-mat .btn-rm:hover { background:#fdeaea; }
    .rv-revisar .rv-add-dashed { display:inline-flex; align-items:center; gap:8px; margin-top:14px; border:1.5px dashed #b7c8e6; color:#3f6fd8; background:#fff; border-radius:8px; padding:9px 16px; font-size:14px; font-weight:500; cursor:pointer; }

    /* Fotos */
    .rv-revisar .rv-photos { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
    .rv-revisar .rv-photos .cap { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#9aa6b8; margin-bottom:10px; }
    .rv-revisar .rv-photos img { width:88px; height:66px; object-fit:cover; border-radius:8px; }
    .rv-revisar .my-gallery { margin:0; }
    .rv-revisar .rv-fig { margin:0; cursor:pointer; }
    .rv-revisar .rv-fig img { display:block; width:88px; height:66px; object-fit:cover; border-radius:8px; transition:opacity .15s; }
    .rv-revisar .rv-fig:hover img { opacity:.85; }
    .rv-revisar .rv-figcap { display:none; }

    /* Layout 2 columnas + panel sticky */
    .rv-revisar .rv-layout { display:flex; gap:24px; align-items:flex-start; }
    .rv-revisar .rv-main { flex:1; min-width:0; }
    .rv-revisar .rv-aside { position:sticky; top:20px; flex:none; width:322px; align-self:flex-start; }
    .rv-revisar .rv-ot-input { width:100%; border-radius:8px; padding:11px 12px; font-size:15px; }
    .rv-revisar .rv-approve { width:100%; border:none; border-radius:8px; padding:13px; font-size:15px; font-weight:600; display:flex; align-items:center; justify-content:center; gap:9px; background:#28a745; color:#fff; cursor:pointer; }
    .rv-revisar .rv-approve:disabled { background:#c9d4cc; color:#f2f6f3; cursor:not-allowed; }

    @media (max-width: 991px) {
        .rv-revisar .rv-layout { flex-direction:column; }
        .rv-revisar .rv-aside { position:static; width:100%; }
        .rv-revisar .rv-context { grid-template-columns:1fr 1fr; }
        .rv-revisar .rv-photos { grid-template-columns:1fr; }
    }
</style>

<div class="container-fluid rv-revisar">
    <div class="page-title">
        <div class="row">
            <div class="col-6"><h4>Revisar / Aprobar Trabajo</h4></div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                        <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.ordenes.index') }}">Trabajos</a></li>
                    <li class="breadcrumb-item active">Revisar #{{ $t->id }}</li>
                </ol>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
        <h3 class="m-0" style="font-size:24px;font-weight:600;">Trabajo #{{ $t->id }}</h3>
        <span class="rv-status" style="background:{{ $statusBg }};color:{{ $statusColor }}">
            <span class="dot" style="background:{{ $statusColor }}"></span>{{ $estado?->label() }}
        </span>
    </div>

    <form id="form-revision" action="{{ route('admin.trabajos.ordenes.guardarRevision', $t->id) }}" method="POST">
        @csrf
        <input type="hidden" name="accion" id="accion" value="guardar">

        {{-- Integrantes: van como hidden para preservar el sync (se muestran en su tarjeta) --}}
        @foreach($t->empleados as $emp)
            <input type="hidden" name="empleados[]" value="{{ $emp->id }}">
        @endforeach

        <div class="rv-layout">
            <div class="rv-main">

                {{-- ===== TIRA DE CONTEXTO ===== --}}
                <div class="rv-card"><div class="rv-card-body">
                    <div class="rv-context">
                        <div><div class="lbl">Cargado por</div><div class="val">{{ $t->user ? trim($t->user->first_name.' '.$t->user->last_name) : '—' }}</div></div>
                        <div><div class="lbl">Cuadrilla</div><div class="val">{{ $t->cuadrilla?->nombre ?? '—' }}</div></div>
                        <div><div class="lbl">Fecha del trabajo</div><div class="val">{{ $t->fecha?->format('d/m/Y') ?? '—' }}</div></div>
                        <div><div class="lbl">Tipo de trabajo</div><div class="val">{{ $fmt($t->categoria) }}</div></div>
                    </div>
                </div></div>

                {{-- ===== DATOS GENERALES ===== --}}
                <div class="rv-card rv-section">
                    <div class="rv-card-head">
                        <h6>Datos generales</h6>
                        <span class="rv-editbtn" data-edit-toggle><span class="lbl-editar">Editar</span><span class="lbl-listo">Listo</span></span>
                    </div>
                    <div class="rv-card-body">
                        {{-- LECTURA --}}
                        <div class="rv-read rv-read-grid" style="grid-template-columns:repeat(3,1fr)">
                            <div><div class="k">Domicilio</div><div class="val">{{ $t->domicilio ?: '—' }}</div></div>
                            <div><div class="k">Vehículo</div><div class="val">{{ $t->vehiculo ? $t->vehiculo->patente : '—' }}</div></div>
                            <div><div class="k">Ubicación (GPS)</div><div class="val">
                                @if($t->latitud && $t->longitud)
                                    {{ $t->latitud }}, {{ $t->longitud }} ·
                                    <a href="https://www.google.com/maps?q={{ $t->latitud }},{{ $t->longitud }}" target="_blank" style="color:#2ba95f">Ver en Maps ↗</a>
                                @else — @endif
                            </div></div>
                        </div>
                        {{-- EDICIÓN --}}
                        <div class="rv-edit">
                            <div class="row g-3">
                                <div class="col-12 col-sm-4 rv-field">
                                    <label>Fecha</label>
                                    <input type="date" class="form-control" name="fecha" value="{{ $fecha }}">
                                </div>
                                @if($esAdmin)
                                <div class="col-12 col-sm-4 rv-field">
                                    <label>Cuadrilla</label>
                                    <select name="cuadrilla_id" class="form-select">
                                        <option value="">Seleccione…</option>
                                        @foreach($cuadrillas as $c)
                                            <option value="{{ $c->id }}" {{ (string)$v('cuadrilla_id') === (string)$c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <input type="hidden" name="cuadrilla_id" value="{{ $v('cuadrilla_id') }}">
                                @endif
                                <div class="col-12 col-sm-4 rv-field">
                                    <label>Vehículo</label>
                                    <select name="vehiculo_id" class="form-select">
                                        <option value="">Seleccione…</option>
                                        @foreach($vehiculos as $veh)
                                            <option value="{{ $veh->id }}" {{ (string)$v('vehiculo_id') === (string)$veh->id ? 'selected' : '' }}>{{ $veh->patente }} — {{ $veh->marca }} {{ $veh->modelo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="rv-field mt-2">
                                <label>Domicilio</label>
                                <input type="text" class="form-control" name="domicilio" value="{{ $v('domicilio') }}" placeholder="Dirección del poste">
                            </div>
                            <div class="row g-3">
                                <div class="col-6 col-sm-3 rv-field"><label>Latitud</label><input type="text" class="form-control" name="latitud" value="{{ $v('latitud') }}"></div>
                                <div class="col-6 col-sm-3 rv-field"><label>Longitud</label><input type="text" class="form-control" name="longitud" value="{{ $v('longitud') }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== INTEGRANTES + INFRAESTRUCTURA ===== --}}
                <div class="row g-3" style="margin-bottom:0">
                    <div class="col-12 col-lg-6">
                        <div class="rv-card">
                            <div class="rv-card-head"><h6>Integrantes</h6></div>
                            <div class="rv-card-body d-flex flex-wrap gap-2">
                                @forelse($t->empleados as $emp)
                                    @php $nom = trim($emp->first_name.' '.$emp->last_name); @endphp
                                    <span class="rv-chip"><span class="ini">{{ $initials($nom) }}</span>{{ $nom }}</span>
                                @empty
                                    <span class="text-muted">Sin integrantes asignados.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="rv-card rv-section">
                            <div class="rv-card-head">
                                <h6>Infraestructura</h6>
                                <span class="rv-editbtn" data-edit-toggle><span class="lbl-editar">Editar</span><span class="lbl-listo">Listo</span></span>
                            </div>
                            <div class="rv-card-body">
                                {{-- LECTURA --}}
                                <div class="rv-read rv-read-grid" style="grid-template-columns:1fr 1fr">
                                    <div><div class="k">Central</div><div class="val">{{ $fmt($t->central) }} {{ $t->central_aclarar }}</div></div>
                                    <div><div class="k">Tipo de trabajo</div><div class="val">{{ $fmt($t->categoria) }}</div></div>
                                </div>
                                {{-- EDICIÓN --}}
                                <div class="rv-edit">
                                    <div class="rv-field">
                                        <label>Central</label>
                                        <select name="central" id="rv-central" class="form-select">
                                            <option value="">—</option>
                                            @foreach($centrales as $val => $lab)
                                                <option value="{{ $val }}" {{ $v('central') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="rv-field conditional" id="rv-grp-central-aclarar">
                                        <label>Aclarar central</label>
                                        <input type="text" class="form-control" name="central_aclarar" value="{{ $v('central_aclarar') }}">
                                    </div>
                                    <div class="rv-field">
                                        <label>Tipo de trabajo</label>
                                        <select name="categoria" class="form-select">
                                            <option value="">Seleccione…</option>
                                            @foreach($categorias as $val => $lab)
                                                <option value="{{ $val }}" {{ $v('categoria') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== TRABAJO REALIZADO ===== --}}
                <div class="rv-card rv-section">
                    <div class="rv-card-head">
                        <h6>Trabajo realizado</h6>
                        <span class="rv-editbtn" data-edit-toggle><span class="lbl-editar">Editar</span><span class="lbl-listo">Listo</span></span>
                    </div>
                    <div class="rv-card-body">
                        {{-- LECTURA: lista compacta --}}
                        <div class="rv-read">
                            <div class="rv-q">
                                <div class="rv-q-top">
                                    <span class="rv-q-label">Tipo de poste</span>
                                    <span class="rv-qchip det">{{ $fmt($t->tipo_poste) }}</span>
                                </div>
                            </div>
                            @foreach($preguntas as $q)
                                <div class="rv-q">
                                    <div class="rv-q-top">
                                        <span class="rv-q-label"><span class="rv-q-num">{{ $q['num'] }}</span>{{ $q['label'] }}</span>
                                        @if(!empty($q['detalle']))
                                            <span class="rv-qchip det">Detalle</span>
                                        @else
                                            <span class="rv-qchip {{ $q['on'] ? 'si' : 'no' }}">{{ $q['on'] ? 'Sí' : 'No' }}</span>
                                        @endif
                                    </div>
                                    @if(!empty($q['sub']))
                                        <div class="rv-subtags">
                                            @foreach($q['sub'] as $s)
                                                <span class="rv-subtag">{{ $s['k'] }} <strong>{{ $s['v'] }}</strong></span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- EDICIÓN: form con switches y condicionales --}}
                        <div class="rv-edit">
                            {{-- Tipo de poste (define la certificación) --}}
                            <div class="pregunta">
                                <label class="pregunta-titulo d-block mb-2" for="rv-tipo-poste">Tipo de poste</label>
                                <select name="tipo_poste" id="rv-tipo-poste" class="form-select">
                                    <option value="">Seleccione…</option>
                                    @foreach($tiposPoste as $val => $lab)
                                        <option value="{{ $val }}" {{ $v('tipo_poste') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- 1 --}}
                            <div class="pregunta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="pregunta-titulo" for="rv-desmonto">1. ¿Se desmontó poste?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="desmonto_poste" value="1" id="rv-desmonto" {{ $chk('desmonto_poste') }}></div>
                                </div>
                            </div>
                            {{-- 2 --}}
                            <div class="pregunta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="pregunta-titulo" for="rv-coloco">2. ¿Se colocó poste?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="coloco_poste" value="1" id="rv-coloco" {{ $chk('coloco_poste') }}></div>
                                </div>
                                <div class="sub conditional" id="rv-grp-datos-poste">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-4"><label class="form-label">Tamaño</label>
                                            <select name="tamano_poste" class="form-select"><option value="">—</option>
                                                @foreach($tamanosPoste as $val => $lab)<option value="{{ $val }}" {{ $v('tamano_poste') === $val ? 'selected' : '' }}>{{ $lab }}</option>@endforeach
                                            </select></div>
                                        <div class="col-12 col-sm-4"><label class="form-label">Material</label>
                                            <select name="poste_material" id="rv-poste-material" class="form-select"><option value="">—</option>
                                                @foreach($materialesPoste as $val => $lab)<option value="{{ $val }}" {{ $v('poste_material') === $val ? 'selected' : '' }}>{{ $lab }}</option>@endforeach
                                            </select></div>
                                        <div class="col-12 col-sm-4 conditional" id="rv-grp-reutilizado"><label class="form-label">¿Qué se reutilizó?</label>
                                            <select name="poste_reutilizado_material" class="form-select"><option value="">—</option>
                                                @foreach($materialesReutilizado as $val => $lab)<option value="{{ $val }}" {{ $v('poste_reutilizado_material') === $val ? 'selected' : '' }}>{{ $lab }}</option>@endforeach
                                            </select></div>
                                    </div>
                                </div>
                            </div>
                            {{-- 3 --}}
                            <div class="pregunta">
                                <label class="pregunta-titulo d-block mb-2">3. CDO / Caja Terminal / NAP</label>
                                <div class="row g-2">
                                    @foreach($elementosRed as $val => $lab)
                                        <div class="col-12 col-sm-4"><label class="form-label">{{ $lab }}</label>
                                            <input type="number" min="0" class="form-control" name="{{ $val }}_cantidad" value="{{ $v($val.'_cantidad') }}" placeholder="Cantidad"></div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- 4 --}}
                            <div class="pregunta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="pregunta-titulo" for="rv-sifon">4. ¿Tiene sifón?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="sifon" value="1" id="rv-sifon" {{ $chk('sifon') }}></div>
                                </div>
                                <div class="sub conditional" id="rv-grp-sifon">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-6"><label class="form-label">Cantidad de cables</label><input type="number" min="0" class="form-control" name="sifon_cables" value="{{ $v('sifon_cables') }}"></div>
                                        <div class="col-12 col-sm-6"><label class="form-label">Cantidad de protecciones</label><input type="number" min="0" class="form-control" name="protecciones_cantidad" value="{{ $v('protecciones_cantidad') }}"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- 5 --}}
                            <div class="pregunta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="pregunta-titulo" for="rv-rienda">5. ¿Tiene rienda?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="rienda" value="1" id="rv-rienda" data-cond="#rv-grp-rienda" {{ $chk('rienda') }}></div>
                                </div>
                                <div class="sub conditional" id="rv-grp-rienda">
                                    <label class="form-label d-block mb-2">Cantidad por tipo de rienda</label>
                                    <div class="row g-2">
                                        @foreach($tiposRienda as $val => $lab)
                                            <div class="col-12 col-sm-4"><label class="form-label">{{ $lab }}</label>
                                                <input type="number" min="0" class="form-control" name="rienda_{{ $val }}_cantidad" value="{{ $v('rienda_'.$val.'_cantidad') }}" placeholder="Cantidad"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            {{-- 6 --}}
                            <div class="pregunta">
                                <label class="pregunta-titulo d-block mb-2" for="rv-suelo">6. Tipo de suelo</label>
                                <select name="tipo_suelo" id="rv-suelo" class="form-select" style="max-width:340px"><option value="">Seleccione…</option>
                                    @foreach($tiposSuelo as $val => $lab)<option value="{{ $val }}" {{ $v('tipo_suelo') === $val ? 'selected' : '' }}>{{ $lab }}</option>@endforeach
                                </select>
                                <div class="conditional mt-3 pt-3" id="rv-grp-vereda" data-disp="flex" style="border-top:1px solid #e9edf3;display:none;justify-content:space-between;align-items:center">
                                    <label class="form-label mb-0" for="rv-vereda">¿Se realizó reparación de vereda?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="rep_vereda" value="1" id="rv-vereda" {{ $chk('rep_vereda') }}></div>
                                </div>
                            </div>
                            {{-- 7 --}}
                            <div class="pregunta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="pregunta-titulo" for="rv-poda">7. ¿Se realizó poda?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="poda" value="1" id="rv-poda" {{ $chk('poda') }}></div>
                                </div>
                            </div>
                            {{-- 8 --}}
                            <div class="pregunta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="pregunta-titulo" for="rv-retensado">8. ¿Se retensó cable o suspensor?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="retensado" value="1" id="rv-retensado" data-cond="#rv-grp-retensado" {{ $chk('retensado') }}></div>
                                </div>
                                <div class="sub conditional" id="rv-grp-retensado"><label class="form-label">Cantidad</label><input type="number" min="0" class="form-control" name="retensado_cantidad" value="{{ $v('retensado_cantidad') }}"></div>
                            </div>
                            {{-- 9 --}}
                            <div class="pregunta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="pregunta-titulo" for="rv-bajadas">9. ¿Se traspasó cable de bajada?</label>
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="bajadas" value="1" id="rv-bajadas" data-cond="#rv-grp-bajadas" {{ $chk('bajadas') }}></div>
                                </div>
                                <div class="sub conditional" id="rv-grp-bajadas"><label class="form-label">Cantidad de bajadas</label><input type="number" min="0" class="form-control" name="bajadas_cantidad" value="{{ $v('bajadas_cantidad') }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== MATERIALES ===== --}}
                <div class="rv-card rv-mat-card">
                    <div class="rv-card-head">
                        <h6>Materiales utilizados</h6>
                        <button type="submit" class="btn btn-outline-secondary btn-sm"
                            formaction="{{ route('admin.trabajos.ordenes.regenerarMateriales', $t->id) }}"
                            formnovalidate onclick="return confirm('Esto reemplaza los materiales por los de las reglas. ¿Continuar?')">
                            Regenerar desde reglas
                        </button>
                    </div>
                    <div class="rv-card-body">
                        <p class="text-muted small mb-2">Cargados automáticamente según lo realizado. Revisá y ajustá lo que haga falta; se guardan al aprobar o con “Guardar cambios”.</p>
                        <table class="rv-mat">
                            <thead><tr><th>Código</th><th>Material</th><th style="width:130px">Cantidad</th><th style="width:44px"></th></tr></thead>
                            <tbody id="rv-mat-body">
                                @forelse($t->materiales as $tm)
                                    <tr>
                                        <td>{{ $tm->material?->codigo }}<input type="hidden" name="material_id[]" value="{{ $tm->material_id }}"></td>
                                        <td class="small">{{ $tm->material?->descripcion }}</td>
                                        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="cantidad[]" value="{{ rtrim(rtrim(number_format($tm->cantidad,2,'.',''),'0'),'.') }}"></td>
                                        <td class="text-center"><button type="button" class="btn-rm" title="Quitar" data-rm-row>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button></td>
                                    </tr>
                                @empty
                                    <tr id="rv-mat-empty"><td colspan="4" class="text-muted text-center">Sin materiales</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="row g-2 align-items-end mt-3">
                            <div class="col-12 col-sm-8">
                                <label class="form-label small mb-1">Agregar material</label>
                                <select id="rv-mat-search" class="form-select"></select>
                                <small class="text-muted">Buscá por código o descripción. Podés agregar todos los que necesites.</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== FOTOS ===== --}}
                @php
                    $antes  = $t->getMedia('fotos_antes');
                    $despues = $t->getMedia('fotos_despues');
                @endphp
                <div class="rv-card">
                    <div class="rv-card-head"><h6>Fotos</h6></div>
                    <div class="rv-card-body">
                        <div class="rv-photos">
                            <div>
                                <div class="cap">Antes · {{ $antes->count() }}</div>
                                @if($antes->isNotEmpty())
                                    <div class="my-gallery d-flex flex-wrap gap-2" itemscope>
                                        @foreach($antes as $i => $m)
                                            <figure class="rv-fig" itemprop="associatedMedia" itemscope>
                                                <a href="{{ $m->getUrl() }}" itemprop="contentUrl" data-size="{{ $dim($m) }}">
                                                    <img src="{{ $m->getUrl() }}" itemprop="thumbnail" alt="Foto antes {{ $i + 1 }}">
                                                </a>
                                                <figcaption class="rv-figcap">Antes {{ $i + 1 }}</figcaption>
                                            </figure>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">Sin fotos</span>
                                @endif
                            </div>
                            <div>
                                <div class="cap">Después · {{ $despues->count() }}</div>
                                @if($despues->isNotEmpty())
                                    <div class="my-gallery d-flex flex-wrap gap-2" itemscope>
                                        @foreach($despues as $i => $m)
                                            <figure class="rv-fig" itemprop="associatedMedia" itemscope>
                                                <a href="{{ $m->getUrl() }}" itemprop="contentUrl" data-size="{{ $dim($m) }}">
                                                    <img src="{{ $m->getUrl() }}" itemprop="thumbnail" alt="Foto después {{ $i + 1 }}">
                                                </a>
                                                <figcaption class="rv-figcap">Después {{ $i + 1 }}</figcaption>
                                            </figure>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">Sin fotos</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== OBSERVACIONES ===== --}}
                @php $fotosObs = $t->getMedia('fotos_observaciones'); @endphp
                <div class="rv-card rv-section">
                    <div class="rv-card-head">
                        <h6>Observaciones</h6>
                        <span class="rv-editbtn" data-edit-toggle><span class="lbl-editar">Editar</span><span class="lbl-listo">Listo</span></span>
                    </div>
                    <div class="rv-card-body">
                        <div class="rv-read"><p class="m-0" style="font-size:14.5px;line-height:1.6;color:#3a4658">{{ $t->observaciones ?: '—' }}</p></div>
                        <div class="rv-edit"><textarea class="form-control" name="observaciones" rows="3">{{ $v('observaciones') }}</textarea></div>

                        @if($fotosObs->isNotEmpty())
                            <div class="mt-3">
                                <div class="cap" style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#9aa6b8;margin-bottom:10px">Imágenes de observaciones · {{ $fotosObs->count() }}</div>
                                <div class="my-gallery d-flex flex-wrap gap-2" itemscope>
                                    @foreach($fotosObs as $i => $m)
                                        <figure class="rv-fig" itemprop="associatedMedia" itemscope>
                                            <a href="{{ $m->getUrl() }}" itemprop="contentUrl" data-size="{{ $dim($m) }}">
                                                <img src="{{ $m->getUrl() }}" itemprop="thumbnail" alt="Observación {{ $i + 1 }}">
                                            </a>
                                            <figcaption class="rv-figcap">Observación {{ $i + 1 }}</figcaption>
                                        </figure>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>{{-- /.rv-main --}}

            {{-- ===================== PANEL DE ACCIÓN ===================== --}}
            <aside class="rv-aside">
                {{-- OT --}}
                <div class="rv-card">
                    <div class="rv-card-body" style="padding:16px 18px">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span style="font-size:15px;font-weight:600;color:#2b3648">Orden de trabajo</span>
                        </div>
                        <p class="text-muted mb-2" style="font-size:12.5px">Requerida para aprobar el trabajo.</p>
                        <input type="text" class="form-control rv-ot-input" name="ot" id="rv-ot" value="{{ old('ot', $t->ot) }}" placeholder="Ej. OT-2026-00842" {{ ($aprobado || $certificado) ? 'readonly' : '' }}>
                    </div>
                </div>

                {{-- Decisión --}}
                <div class="rv-card"><div class="rv-card-body" style="padding:18px">
                    <span style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#9aa6b8;margin-bottom:14px">Decisión</span>

                    @if($pendiente)
                        @unless($t->categoria)
                            <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:13px">
                                Cargá el <strong>tipo de trabajo</strong> en Infraestructura para poder aprobar.
                            </div>
                        @endunless
                        <button type="submit" class="rv-approve" id="rv-btn-aprobar" data-accion="aprobar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Aprobar trabajo
                        </button>
                        <p id="rv-ot-hint" class="mt-2 mb-0" style="font-size:12px;color:#c0803a;display:none">Cargá la OT para habilitar la aprobación.</p>
                        <button type="submit" class="btn btn-light w-100 mt-2" data-accion="guardar" formnovalidate>Guardar cambios</button>

                    @elseif($aprobado)
                        <div style="background:#e5f6ea;border:1px solid #a9dcbb;border-radius:10px;padding:16px;text-align:center">
                            <div style="width:44px;height:44px;border-radius:50%;background:#28a745;color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div style="font-size:15px;font-weight:600;color:#1f7a3d">Trabajo aprobado</div>
                            <div style="font-size:13px;color:#3f8f5c;margin-top:4px">
                                OT {{ $t->ot ?: '—' }}@if($t->aprobadoPor) · por {{ $t->aprobadoPor->first_name }} {{ $t->aprobadoPor->last_name }} @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary w-100 mt-3"
                            formaction="{{ route('admin.trabajos.ordenes.revertir', $t->id) }}" formnovalidate
                            onclick="return confirm('¿Revertir la aprobación? El trabajo vuelve a Pendiente de revisión.')">
                            Revertir aprobación
                        </button>

                    @else
                        <div class="alert alert-secondary mb-0" style="font-size:13px">
                            Este trabajo está <strong>certificado</strong> y no admite cambios desde acá.
                        </div>
                    @endif
                </div></div>

                <p class="text-center text-muted" style="font-size:12px;line-height:1.5">Los cambios en datos y materiales<br>se guardan al aprobar o con “Guardar cambios”.</p>
            </aside>
        </div>
    </form>

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
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/photoswipe/photoswipe.min.js') }}"></script>
<script src="{{ asset('assets/js/photoswipe/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('assets/js/photoswipe/photoswipe.js') }}"></script>
<script>
(function () {
    // Toggle lectura/edición por sección
    document.querySelectorAll('[data-edit-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var sec = btn.closest('.rv-section');
            if (sec) sec.classList.toggle('editing');
        });
    });

    // Mostrar/ocultar grupos condicionales. Sin regla CSS de por medio:
    // fijamos el display explícito (block salvo data-disp, p.ej. "flex").
    function setVis(el, show) {
        if (!el) return;
        el.style.display = show ? (el.dataset.disp || 'block') : 'none';
    }
    function bindCond(chkId, grpId, extra) {
        var chk = document.getElementById(chkId), grp = document.getElementById(grpId);
        if (!chk) return;
        function apply() { setVis(grp, chk.checked); if (extra) extra(); }
        chk.addEventListener('change', apply); apply();
    }
    bindCond('rv-coloco', 'rv-grp-datos-poste', aplicarReutilizado);
    bindCond('rv-sifon', 'rv-grp-sifon');
    bindCond('rv-rienda', 'rv-grp-rienda');
    bindCond('rv-retensado', 'rv-grp-retensado');
    bindCond('rv-bajadas', 'rv-grp-bajadas');

    // Material = reutilizado -> mostrar "qué se reutilizó"
    function aplicarReutilizado() {
        var mat = document.getElementById('rv-poste-material');
        setVis(document.getElementById('rv-grp-reutilizado'), mat && mat.value === 'reutilizado');
    }
    var posteMat = document.getElementById('rv-poste-material');
    if (posteMat) { posteMat.addEventListener('change', aplicarReutilizado); aplicarReutilizado(); }

    // Central = CYO -> aclarar
    var central = document.getElementById('rv-central');
    function aplicarCentral() { setVis(document.getElementById('rv-grp-central-aclarar'), central && central.value === 'CYO'); }
    if (central) { central.addEventListener('change', aplicarCentral); aplicarCentral(); }

    // Suelo = contrapiso -> reparación de vereda
    var suelo = document.getElementById('rv-suelo');
    function aplicarSuelo() { setVis(document.getElementById('rv-grp-vereda'), suelo && suelo.value === 'contrapiso'); }
    if (suelo) { suelo.addEventListener('change', aplicarSuelo); aplicarSuelo(); }

    // Quitar fila de material (reutilizable para filas nuevas)
    function bindRemove(btn) {
        if (!btn) return;
        btn.addEventListener('click', function () { var tr = btn.closest('tr'); if (tr) tr.remove(); });
    }
    document.querySelectorAll('[data-rm-row]').forEach(bindRemove);

    // Agregar material vía Select2 (búsqueda AJAX por código/descripción)
    function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function agregarFila(id, codigo, desc) {
        var body = document.getElementById('rv-mat-body');
        if (!body) return;
        var vacio = document.getElementById('rv-mat-empty');
        if (vacio) vacio.remove();
        var existe = body.querySelector('input[name="material_id[]"][value="' + id + '"]');
        if (existe) { var q = existe.closest('tr').querySelector('input[name="cantidad[]"]'); if (q) q.focus(); return; }
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td>' + esc(codigo) + '<input type="hidden" name="material_id[]" value="' + esc(id) + '"></td>' +
            '<td class="small">' + esc(desc) + '</td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="cantidad[]" value="1"></td>' +
            '<td class="text-center"><button type="button" class="btn-rm" title="Quitar" data-rm-row><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button></td>';
        body.appendChild(tr);
        bindRemove(tr.querySelector('[data-rm-row]'));
    }
    if (window.jQuery) {
        var jq = window.jQuery;
        var $search = jq('#rv-mat-search');
        if ($search.length && jq.fn.select2) {
            $search.select2({
                width: '100%',
                placeholder: 'Buscá por código o descripción…',
                minimumInputLength: 2,
                language: {
                    inputTooShort: function () { return 'Escribí al menos 2 caracteres…'; },
                    searching: function () { return 'Buscando…'; },
                    noResults: function () { return 'Sin resultados'; }
                },
                ajax: {
                    url: '{{ route('admin.trabajos.ordenes.buscarMateriales') }}',
                    dataType: 'json', delay: 250,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) { return { results: data.results }; },
                    cache: true
                }
            });
            $search.on('select2:select', function (e) {
                var d = e.params.data;
                agregarFila(d.id, d.codigo, d.descripcion);
                $search.val(null).trigger('change');
            });
        }
    }

    // Set de accion + gate de OT para Aprobar
    var form = document.getElementById('form-revision');
    var ot = document.getElementById('rv-ot');
    var btnAprobar = document.getElementById('rv-btn-aprobar');
    var otHint = document.getElementById('rv-ot-hint');

    function otLleno() { return ot && ot.value.trim().length > 0; }
    function refrescarAprobar() {
        if (!btnAprobar) return;
        btnAprobar.disabled = !otLleno();
        if (otHint) otHint.style.display = otLleno() ? 'none' : '';
    }
    if (ot) { ot.addEventListener('input', refrescarAprobar); }
    refrescarAprobar();

    // Cualquier submit con data-accion setea el hidden accion
    var accion = document.getElementById('accion');
    if (form) {
        form.querySelectorAll('button[type="submit"][data-accion]').forEach(function (b) {
            b.addEventListener('click', function () { if (accion) accion.value = b.getAttribute('data-accion'); });
        });
    }
})();
</script>
@endsection
