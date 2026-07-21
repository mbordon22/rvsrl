@extends('layouts.simple.master')

@section('css')
<style>
    /* ===== Reglas de materiales — diseño "Reglas de Materiales (Desktop).dc.html" ===== */
    .rm { --indigo:#4f5fbf; --indigo-d:#3949ab; --navy:#1b2a63; --verde:#2ba95f; }
    .rm .rm-card { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(30,40,70,.06),0 8px 24px rgba(30,40,70,.04); }

    /* Toolbar */
    .rm-toolbar { padding:16px 20px; border-bottom:1px solid #eef1f6; background:#fafbfd; border-radius:12px 12px 0 0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; }
    .rm-pill-activas { display:inline-flex; align-items:center; gap:8px; background:#e5f6ea; color:#1f7a3d; border:1px solid #bfe6cd; border-radius:20px; padding:7px 14px; font-size:13.5px; font-weight:600; }
    .rm-pill-activas .dot { width:8px; height:8px; border-radius:50%; background:#2ba95f; }
    .rm-sel { border:1.5px solid #d5dce6; border-radius:8px; background:#fff; padding:8px 10px; font-size:14px; color:#2b3247; }
    .rm-seg { display:flex; background:#eef1f6; border-radius:8px; padding:3px; }
    .rm-seg button { border:none; background:transparent; border-radius:6px; padding:7px 14px; font-size:13.5px; font-weight:500; color:#5a6b82; cursor:pointer; }
    .rm-seg button.active { background:#fff; color:#2b3247; box-shadow:0 1px 2px rgba(30,40,70,.12); }
    .rm-search { border:1.5px solid #d5dce6; border-radius:8px; background:#fff; padding:9px 12px 9px 34px; font-size:14px; color:#2b3247; width:320px; max-width:100%; }
    .rm-search:focus { outline:none; border-color:var(--indigo); }

    /* Grupos + filas */
    .rm-group { border:1px solid #e9edf3; border-radius:11px; margin:12px 16px; overflow:hidden; }
    .rm-group-head { display:flex; align-items:center; gap:12px; padding:13px 18px; background:#f4f6fb; cursor:pointer; user-select:none; }
    .rm-group-head:hover { background:#eef1f8; }
    .rm-group-head .chev { transition:transform .2s; color:#5a6b82; }
    .rm-group.collapsed .chev { transform:rotate(-90deg); }
    .rm-group.collapsed .rm-group-body { display:none; }
    .rm-group-tag { display:inline-flex; align-items:center; gap:7px; background:#eef0fb; color:var(--indigo-d); border:1px solid #d7ddf6; border-radius:7px; padding:5px 12px; font-size:13.5px; font-weight:600; }
    .rm-group-count { font-size:13px; color:#8a97ab; font-weight:500; }

    .rm-row { display:flex; align-items:center; gap:14px; padding:15px 18px; border-top:1px solid #eef0f3; flex-wrap:wrap; background:#fff; }
    .rm-row.off { opacity:.55; }
    .rm-cond { display:inline-flex; align-items:center; padding:6px 13px; border-radius:8px; font-size:13px; font-weight:600; background:#eef0fb; color:var(--indigo-d); white-space:nowrap; }
    .rm-arrow { color:#b7c1cf; flex:none; }
    .rm-matbox { flex:1; min-width:220px; }
    .rm-matdesc { font-size:15px; font-weight:600; color:#2b3247; }
    .rm-matmeta { font-size:12.5px; color:#8a97ab; font-variant-numeric:tabular-nums; margin-top:2px; }
    .rm-qty { display:inline-flex; align-items:center; gap:7px; background:#fff6e8; color:#b3730a; border:1px solid #f2dcae; border-radius:8px; padding:6px 12px; font-size:13px; font-weight:600; white-space:nowrap; }
    .rm-actions { display:flex; align-items:center; gap:8px; margin-left:auto; }
    .rm-ic { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; background:#fff; cursor:pointer; }
    .rm-ic.edit { border:1.5px solid #e2e6ee; color:var(--indigo); }
    .rm-ic.edit:hover { background:#eef0fb; border-color:#c9c9ef; }
    .rm-ic.del { border:1.5px solid #f3d4cf; color:#e05a45; }
    .rm-ic.del:hover { background:#fdeeeb; border-color:#efbcb2; }

    /* Switch */
    .rm-sw { width:46px; height:25px; border-radius:20px; padding:3px; cursor:pointer; transition:background .2s; flex:none; background:#c9d0dc; }
    .rm-sw.on { background:var(--verde); }
    .rm-sw .knob { width:19px; height:19px; border-radius:50%; background:#fff; transition:transform .2s; }
    .rm-sw.on .knob { transform:translateX(21px); }

    /* Botones cabecera */
    .rm-btn-nav { background:var(--navy); color:#fff; border:none; border-radius:8px; padding:11px 22px; font-size:15px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px; }
    .rm-btn-nav:hover { background:#142050; color:#fff; }
    .rm-btn-out { border:1.5px solid var(--indigo); color:var(--indigo); background:#fff; border-radius:8px; padding:10px 18px; font-size:15px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px; }
    .rm-btn-out:hover { background:#eef0fb; }

    /* Drawer */
    .rm-ov { position:fixed; inset:0; background:rgba(20,26,46,.44); z-index:1040; display:none; }
    .rm-ov.open { display:block; }
    .rm-drawer { position:fixed; top:0; right:0; bottom:0; width:560px; max-width:94vw; background:#f3f5f9; z-index:1041; box-shadow:-8px 0 40px rgba(20,30,60,.22); transform:translateX(100%); transition:transform .26s cubic-bezier(.22,1,.36,1); display:flex; flex-direction:column; }
    .rm-drawer.open { transform:translateX(0); }
    .rm-drawer.wide { width:620px; }
    .rm-dh { padding:18px 24px; background:#fff; border-bottom:1px solid #edf0f5; display:flex; align-items:center; justify-content:space-between; flex:none; }
    .rm-dh.dark { background:var(--navy); color:#fff; }
    .rm-db { flex:1; overflow-y:auto; padding:22px 24px; }
    .rm-df { padding:16px 24px; background:#fff; border-top:1px solid #edf0f5; display:flex; align-items:center; justify-content:flex-end; gap:10px; flex:none; }

    .rm-step { background:#fff; border:1px solid #e6eaf1; border-radius:11px; padding:18px; margin-bottom:18px; }
    .rm-step-t { display:flex; align-items:center; gap:8px; font-size:13.5px; font-weight:600; color:#2b3648; margin-bottom:14px; }
    .rm-step-n { width:22px; height:22px; border-radius:6px; background:#eef0fb; color:var(--indigo-d); display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; }
    .rm-in { width:100%; border:1.6px solid #97a2b2; background:#fff; border-radius:8px; padding:10px 12px; font-size:14.5px; color:#2b3247; outline:none; }
    .rm-in:focus { border-color:var(--indigo); }
    .rm-lbl { display:block; font-size:12.5px; font-weight:500; color:#5a6b82; margin-bottom:6px; }
    .rm-summary { margin-top:14px; background:#eef0fb; border:1px solid #d7ddf6; border-radius:8px; padding:11px 14px; font-size:13.5px; color:var(--indigo-d); font-weight:500; }
    .rm-preview { margin-top:8px; display:flex; align-items:center; gap:12px; background:#fff; border:1px dashed #c4ccdb; border-radius:10px; padding:14px 16px; flex-wrap:wrap; }
    .rm-cap { font-size:11.5px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#7a8699; }

    /* Chips Sí/No del simulador */
    .rm-chip { display:inline-flex; align-items:center; gap:8px; border:1.5px solid #dde3ec; background:#fff; color:#5a6b82; border-radius:20px; padding:8px 14px; font-size:13.5px; font-weight:500; cursor:pointer; }
    .rm-chip .cdot { width:9px; height:9px; border-radius:50%; background:#c9d0dc; }
    .rm-chip.on { border-color:#bfe6cd; background:#e5f6ea; color:#1f7a3d; }
    .rm-chip.on .cdot { background:var(--verde); }

    .rm-empty { padding:44px 16px; text-align:center; color:#8a97ab; font-size:15px; }
    label { color:#000; }
</style>
@endsection

@section('main_content')
@php
    $rulesForJs = $reglas->map(fn ($r) => [
        'id'             => $r->id,
        'descripcion'    => $r->descripcion,
        'condicion_campo'=> $r->condicion_campo,
        'condicion_valor'=> $r->condicion_valor,
        'material_id'    => $r->material_id,
        'material_text'  => $r->material ? ($r->material->codigo . ' — ' . $r->material->descripcion) : '',
        'material_desc'  => $r->material?->descripcion,
        'cantidad_base'  => (float) $r->cantidad_base,
        'cantidad_campo' => $r->cantidad_campo,
        'activo'         => (bool) $r->activo,
    ])->values();
@endphp
<div class="container-fluid rm">

    {{-- Título + acciones --}}
    <div class="page-title">
        <div class="row align-items-end">
            <div class="col-sm-6">
                <h4 class="mb-1 text-dark">Reglas de materiales</h4>
                {{-- <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.ordenes.index') }}">Trabajos</a></li>
                    <li class="breadcrumb-item active text-dark">Reglas de materiales</li>
                </ol> --}}
            </div>
            <div class="col-sm-6 d-flex justify-content-sm-end align-items-center gap-2 mt-2 mt-sm-0">
                <button type="button" class="rm-btn-out" id="rm-open-sim">
                    <i data-feather="play" style="width:16px;"></i> Probar reglas
                </button>
                @can('trabajos_reglas_materiales.create')
                <button type="button" class="rm-btn-nav" id="rm-new">
                    <i data-feather="plus" style="width:16px;"></i> Nueva regla
                </button>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="rm-card">
        {{-- Toolbar --}}
        <div class="rm-toolbar">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="rm-pill-activas"><span class="dot"></span>{{ $activas }} activa{{ $activas === 1 ? '' : 's' }}</span>
                <label class="d-flex align-items-center gap-2 mb-0 text-dark" style="font-size:13.5px;">Condición
                    <select class="rm-sel" id="rm-f-cond">
                        <option value="">Todas</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g['campo'] }}">{{ $g['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="rm-seg" id="rm-f-estado">
                    <button type="button" data-e="all" class="active">Todas</button>
                    <button type="button" data-e="1">Activas</button>
                    <button type="button" data-e="0">Inactivas</button>
                </div>
            </div>
            <div class="position-relative">
                <i data-feather="search" style="width:16px;position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#a9b3c2;"></i>
                <input type="text" class="rm-search" id="rm-search" placeholder="Buscar material, código o condición…">
            </div>
        </div>

        {{-- Listado agrupado --}}
        <div id="rm-list">
            @forelse($grupos as $g)
                <div class="rm-group" data-campo="{{ $g['campo'] }}">
                    <div class="rm-group-head">
                        <i data-feather="chevron-down" class="chev" style="width:16px;"></i>
                        <span class="rm-group-tag"><i data-feather="check-square" style="width:14px;"></i>{{ $g['label'] }}</span>
                        <span class="rm-group-count">{{ $g['reglas']->count() }} regla{{ $g['reglas']->count() === 1 ? '' : 's' }}</span>
                    </div>
                    <div class="rm-group-body">
                        @foreach($g['reglas'] as $r)
                            <div class="rm-row {{ $r->activo ? '' : 'off' }}"
                                data-campo="{{ $r->condicion_campo }}"
                                data-activo="{{ $r->activo ? 1 : 0 }}"
                                data-search="{{ \Illuminate\Support\Str::lower(($r->material?->descripcion).' '.($r->material?->codigo).' '.$r->condText().' '.$r->descripcion) }}">
                                <span class="rm-cond">{{ $r->condText() }}</span>
                                <i data-feather="arrow-right" class="rm-arrow" style="width:20px;"></i>
                                <div class="rm-matbox">
                                    <div class="rm-matdesc text-dark">{{ $r->material?->descripcion ?? '— material no encontrado —' }}</div>
                                    <div class="rm-matmeta">cód {{ $r->material?->codigo ?? '—' }} · {{ $r->descripcion }}</div>
                                </div>
                                <span class="rm-qty"><i data-feather="package" style="width:14px;"></i>{{ $r->qtyText() }}</span>
                                <div class="rm-actions">
                                    @can('trabajos_reglas_materiales.edit')
                                    <div class="rm-sw {{ $r->activo ? 'on' : '' }}" title="Activar / desactivar"
                                        data-url="{{ route('admin.trabajos.reglas-materiales.status', $r->id) }}"><div class="knob"></div></div>
                                    <button type="button" class="rm-ic edit" title="Editar" data-edit="{{ $r->id }}"><i data-feather="edit-2" style="width:16px;"></i></button>
                                    @endcan
                                    @can('trabajos_reglas_materiales.trash')
                                    <form action="{{ route('admin.trabajos.reglas-materiales.destroy', $r->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta regla?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rm-ic del" title="Eliminar"><i data-feather="trash-2" style="width:16px;"></i></button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rm-empty">Todavía no hay reglas cargadas. Creá la primera con “Nueva regla”.</div>
            @endforelse
            <div class="rm-empty" id="rm-noresult" style="display:none;">No hay reglas que coincidan con la búsqueda o los filtros.</div>
        </div>

        <div style="padding:16px 22px 20px;border-top:1px solid #eef1f6;font-size:13px;color:#8a97ab;">
            {{ $reglas->count() }} regla{{ $reglas->count() === 1 ? '' : 's' }} en total
        </div>
    </div>
</div>

{{-- ============ DRAWER: constructor de regla ============ --}}
<div class="rm-ov" id="rm-b-ov"></div>
<form class="rm-drawer" id="rm-drawer" method="POST" action="{{ route('admin.trabajos.reglas-materiales.store') }}">
    @csrf
    <input type="hidden" name="_method" id="rm-method" value="POST">
    <input type="hidden" name="activo" id="rm-activo" value="1">

    <div class="rm-dh">
        <div>
            <h6 class="mb-0 text-dark" style="font-size:18px;font-weight:600;" id="rm-b-title">Nueva regla</h6>
            <span class="text-dark" style="font-size:12.5px;opacity:.6;">Constructor de regla</span>
        </div>
        <button type="button" class="btn btn-light btn-sm" id="rm-b-close"><i data-feather="x" style="width:18px;"></i></button>
    </div>

    <div class="rm-db">
        {{-- 1. Descripción --}}
        <div class="rm-step">
            <span class="rm-step-t text-dark"><span class="rm-step-n">1</span>Descripción de la regla</span>
            <input type="text" name="descripcion" id="rm-desc" class="rm-in text-dark" placeholder="Ej: Poste al colocar">
        </div>

        {{-- 2. Condición --}}
        <div class="rm-step">
            <span class="rm-step-t text-dark"><span class="rm-step-n">2</span>¿Cuándo se aplica?</span>
            <div class="row g-3">
                <div class="col-6">
                    <span class="rm-lbl text-dark">Campo del trabajo</span>
                    <select name="condicion_campo" id="rm-campo" class="rm-in text-dark">
                        <option value="siempre">Siempre (sin condición)</option>
                        <optgroup label="Sí / No">
                            @foreach($booleanos as $col => $label)
                                <option value="{{ $col }}">{{ $label }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Poste">
                            <option value="datos_poste">Datos del poste (material + tamaño)</option>
                        </optgroup>
                        <optgroup label="De opción">
                            @foreach($condicionEnums as $col => $meta)
                                <option value="{{ $col }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="col-6" id="rm-valor-wrap" style="display:none;">
                    <span class="rm-lbl text-dark">Valor esperado</span>
                    <select name="condicion_valor" id="rm-valor" class="rm-in text-dark" disabled></select>
                </div>
                <div class="col-6 d-flex align-items-end" id="rm-novalor" style="display:none;">
                    <span style="font-size:13px;color:#5a6b82;line-height:1.4;padding-bottom:10px;" id="rm-novalor-txt"></span>
                </div>

                {{-- Sub-campos de "Datos del poste" --}}
                <div class="col-12" id="rm-poste-wrap" style="display:none;">
                    <div class="row g-3">
                        <div class="col-6">
                            <span class="rm-lbl text-dark">Material del poste</span>
                            <select id="rm-dp-material" name="dp_material" class="rm-in text-dark" disabled>
                                <option value="">Cualquiera</option>
                                @foreach($datosPoste['material']['options'] as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-6" id="rm-dp-reut-wrap" style="display:none;">
                            <span class="rm-lbl text-dark">Material reutilizado</span>
                            <select id="rm-dp-reut" name="dp_reutilizado" class="rm-in text-dark" disabled>
                                <option value="">Cualquiera</option>
                                @foreach($datosPoste['reutilizado']['options'] as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <span class="rm-lbl text-dark">Tamaño del poste</span>
                            <select id="rm-dp-tamano" name="dp_tamano" class="rm-in text-dark" disabled>
                                <option value="">Cualquiera</option>
                                @foreach($datosPoste['tamano']['options'] as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div style="font-size:12.5px;color:#5a6b82;margin-top:8px;">Podés dejar uno en “Cualquiera”, pero indicá al menos material o tamaño. Se agrega el material cuando el poste coincide con TODO lo que marques.</div>
                </div>
            </div>
            <div class="rm-summary text-dark" id="rm-summary">Se aplica: Siempre</div>
        </div>

        {{-- 3. Material --}}
        <div class="rm-step">
            <span class="rm-step-t text-dark"><span class="rm-step-n">3</span>Material a agregar</span>
            <select name="material_id" id="rm-material" class="form-control" style="width:100%"></select>
        </div>

        {{-- 4. Cantidad --}}
        <div class="rm-step">
            <span class="rm-step-t text-dark"><span class="rm-step-n">4</span>Cantidad</span>
            <div class="rm-seg mb-3" id="rm-qtymode" style="background:#eef1f6;">
                <button type="button" data-m="fija" class="active">Cantidad fija</button>
                <button type="button" data-m="porcada">Por cada…</button>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <input type="number" min="0.01" step="0.01" name="cantidad_base" id="rm-base" class="rm-in text-dark" style="width:100px;" value="1">
                <span id="rm-qty-fijalbl" class="text-dark" style="font-size:14px;">unidad(es) fija(s) por trabajo</span>
                <span id="rm-qty-porlbl" class="text-dark" style="font-size:14px;display:none;">× por cada</span>
                <select name="cantidad_campo" id="rm-campocant" class="rm-in text-dark" style="flex:1;min-width:170px;display:none;" disabled>
                    @foreach($numericos as $col => $label)
                        <option value="{{ $col }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:12px;font-size:13px;" class="text-dark">Resultado: <strong style="color:#b3730a;" id="rm-qty-res">1 fija</strong></div>
        </div>

        {{-- 5. Estado --}}
        <div class="rm-step d-flex align-items-center justify-content-between" style="margin-bottom:18px;">
            <div>
                <span class="rm-step-t mb-0 text-dark"><span class="rm-step-n">5</span>Estado</span>
                <div class="text-dark" style="font-size:13px;margin-top:5px;padding-left:30px;" id="rm-estado-txt">La regla está activa y se aplicará.</div>
            </div>
            <div class="rm-sw on" id="rm-estado-sw"><div class="knob"></div></div>
        </div>

        {{-- Vista previa --}}
        <span class="rm-cap text-dark">Vista previa</span>
        <div class="rm-preview">
            <span class="rm-cond text-dark" id="rm-pv-cond">Siempre</span>
            <i data-feather="arrow-right" style="width:18px;color:#b7c1cf;"></i>
            <div style="flex:1;min-width:160px;"><div class="rm-matdesc text-dark" id="rm-pv-mat">— elegí un material —</div></div>
            <span class="rm-qty" id="rm-pv-qty">1 fija</span>
        </div>
    </div>

    <div class="rm-df">
        <button type="button" class="btn btn-light" id="rm-b-cancel">Cancelar</button>
        <button type="submit" class="btn" style="background:var(--indigo);color:#fff;"><i data-feather="save" style="width:16px;"></i> Guardar</button>
    </div>
</form>

{{-- ============ DRAWER: simulador ============ --}}
<div class="rm-ov" id="rm-s-ov"></div>
<div class="rm-drawer wide" id="rm-sim">
    <div class="rm-dh dark">
        <div class="d-flex align-items-center gap-2">
            <i data-feather="play" style="width:20px;"></i>
            <div><h6 class="mb-0" style="font-size:18px;font-weight:600;">Probar reglas</h6>
                <span style="font-size:12.5px;color:#b7c1e0;">Simulá un trabajo y mirá los materiales sugeridos</span></div>
        </div>
        <button type="button" class="btn btn-sm" style="border:1.5px solid rgba(255,255,255,.25);color:#fff;" id="rm-s-close"><i data-feather="x" style="width:18px;"></i></button>
    </div>

    <div class="rm-db">
        <div class="rm-step">
            <span class="text-dark" style="font-size:13.5px;font-weight:600;">Respuestas del trabajo de ejemplo</span>

            <div class="rm-cap text-dark" style="margin:16px 0 10px;">Sí / No</div>
            <div class="d-flex flex-wrap gap-2" id="rm-sim-bools">
                @foreach($booleanos as $col => $label)
                    <div class="rm-chip" data-col="{{ $col }}"><span class="cdot"></span>{{ $label }}</div>
                @endforeach
            </div>

            <div class="rm-cap text-dark" style="margin:18px 0 10px;">De opción</div>
            <div class="row g-3" id="rm-sim-enums">
                @foreach($enums as $col => $meta)
                    <div class="col-6">
                        <span class="rm-lbl text-dark">{{ $meta['label'] }}</span>
                        <select class="rm-in text-dark rm-sim-enum" data-col="{{ $col }}">
                            <option value="">—</option>
                            @foreach($meta['options'] as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

            <div class="rm-cap text-dark" style="margin:18px 0 10px;">Cantidades</div>
            <div class="row g-2" id="rm-sim-nums">
                @foreach($numericos as $col => $label)
                    <div class="col-4">
                        <span class="rm-lbl text-dark">{{ $label }}</span>
                        <input type="number" min="0" value="0" class="rm-in text-dark rm-sim-num" data-col="{{ $col }}">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rm-card" style="border:1px solid #e6eaf1;overflow:hidden;">
            <div style="padding:14px 18px;background:#e5f6ea;border-bottom:1px solid #cdead7;display:flex;align-items:center;justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:9px;font-size:14.5px;font-weight:600;color:#1f7a3d;"><i data-feather="package" style="width:17px;"></i>Materiales sugeridos</span>
                <span style="font-size:13px;color:#2f8a52;font-weight:500;" id="rm-sim-count">0 ítems</span>
            </div>
            <div id="rm-sim-results"></div>
            <div class="rm-empty" id="rm-sim-empty" style="padding:34px 16px;">Ninguna regla activa coincide con este trabajo. Ajustá las respuestas de arriba.</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    if (window.feather) feather.replace();

    var BOOLS    = @json($booleanos);
    var ENUMS    = @json($enums);          // completos (7): simulador + etiquetas de datos del poste
    var CONDENUMS= @json($condicionEnums); // enums simples usables como condición
    var NUMS     = @json($numericos);
    var RULES = {};
    (@json($rulesForJs)).forEach(function (r) { RULES[r.id] = r; });

    var $j = window.jQuery;
    function el(id) { return document.getElementById(id); }
    function val(id) { var e = el(id); return e ? e.value : ''; }

    // ---------- Helpers de texto ----------
    function labelCampo(campo) {
        if (!campo || campo === 'siempre') return 'Siempre';
        if (campo === 'datos_poste') return 'Datos del poste';
        if (BOOLS[campo]) return BOOLS[campo];
        if (ENUMS[campo]) return ENUMS[campo].label;
        return campo;
    }
    function labelValor(campo, valor) {
        if (!valor) return null;
        if (ENUMS[campo] && ENUMS[campo].options[valor]) return ENUMS[campo].options[valor];
        return valor;
    }
    function textoDatosPoste() {
        var m = val('rm-dp-material'), r = val('rm-dp-reut'), t = val('rm-dp-tamano');
        var bits = [];
        if (m) {
            var ml = ENUMS['poste_material'].options[m] || m;
            if (m === 'reutilizado' && r) ml += ' (' + (ENUMS['poste_reutilizado_material'].options[r] || r) + ')';
            bits.push(ml);
        }
        if (t) bits.push(ENUMS['tamano_poste'].options[t] || t);
        return bits.length ? bits.join(' · ') : '—';
    }
    function textoCond(campo, valor) {
        if (!campo || campo === 'siempre') return 'Siempre';
        if (campo === 'datos_poste') return 'Datos del poste: ' + textoDatosPoste();
        var v = labelValor(campo, valor);
        return v ? labelCampo(campo) + ': ' + v : labelCampo(campo);
    }
    function baseLegible(n) {
        n = Number(n) || 0;
        if (n === Math.trunc(n)) return String(Math.trunc(n));
        return String(n).replace('.', ',');
    }
    function qtyText(base, campo) {
        var b = baseLegible(base);
        if (campo) return b + ' × por cada ' + (NUMS[campo] || campo);
        return b + (b === '1' ? ' fija' : ' fijas');
    }
    function parseDP(v) {
        var p = (v || '').split('|');
        return { material: p[0] || '', reutilizado: p[1] || '', tamano: p[2] || '' };
    }

    // ============ FILTROS ============
    var fCampo = '', fEstado = 'all';
    var search = el('rm-search');

    function applyFilter() {
        var q = (search.value || '').trim().toLowerCase();
        document.querySelectorAll('.rm-group').forEach(function (grp) {
            var visibles = 0;
            grp.querySelectorAll('.rm-row').forEach(function (row) {
                var okCond = !fCampo || row.getAttribute('data-campo') === fCampo;
                var okEst  = fEstado === 'all' || row.getAttribute('data-activo') === fEstado;
                var okSrch = !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
                var show = okCond && okEst && okSrch;
                row.style.display = show ? '' : 'none';
                if (show) visibles++;
            });
            grp.style.display = visibles ? '' : 'none';
        });
        var anyGroup = Array.prototype.some.call(document.querySelectorAll('.rm-group'), function (g) { return g.style.display !== 'none'; });
        var nr = el('rm-noresult');
        if (nr) nr.style.display = anyGroup ? 'none' : '';
    }

    if (search) search.addEventListener('input', applyFilter);
    el('rm-f-cond').addEventListener('change', function () { fCampo = this.value; applyFilter(); });
    el('rm-f-estado').addEventListener('click', function (e) {
        var b = e.target.closest('button'); if (!b) return;
        fEstado = b.getAttribute('data-e');
        this.querySelectorAll('button').forEach(function (x) { x.classList.toggle('active', x === b); });
        applyFilter();
    });

    document.querySelectorAll('.rm-group-head').forEach(function (h) {
        h.addEventListener('click', function () { h.closest('.rm-group').classList.toggle('collapsed'); });
    });

    // ============ TOGGLE ACTIVO (AJAX) ============
    var csrf = document.querySelector('#rm-drawer input[name="_token"]').value;
    document.querySelectorAll('.rm-sw[data-url]').forEach(function (sw) {
        sw.addEventListener('click', function () {
            var fd = new FormData();
            fd.append('_token', csrf);
            fd.append('_method', 'PUT');
            fetch(sw.getAttribute('data-url'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    sw.classList.toggle('on', d.activo);
                    var row = sw.closest('.rm-row');
                    row.classList.toggle('off', !d.activo);
                    row.setAttribute('data-activo', d.activo ? 1 : 0);
                    applyFilter();
                })
                .catch(function () { alert('No se pudo cambiar el estado.'); });
        });
    });

    // ============ DRAWER CONSTRUCTOR ============
    var ovB = el('rm-b-ov'), drawer = el('rm-drawer');
    var STORE_URL = "{{ route('admin.trabajos.reglas-materiales.store') }}";
    var UPDATE_TPL = "{{ route('admin.trabajos.reglas-materiales.update', '__ID__') }}";

    if ($j && $j.fn.select2) {
        $j('#rm-material').select2({
            width: '100%',
            dropdownParent: $j('#rm-drawer'),
            placeholder: 'Buscar por código o descripción…',
            ajax: {
                url: "{{ route('admin.trabajos.reglas-materiales.buscarMateriales') }}",
                dataType: 'json', delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; }
            },
            minimumInputLength: 1
        });
        $j('#rm-material').on('change', updatePreview);
    }

    var qtyMode = 'fija';

    function openBuilder() { ovB.classList.add('open'); drawer.classList.add('open'); }
    function closeBuilder() { ovB.classList.remove('open'); drawer.classList.remove('open'); }

    function setDpDisabled(dis) {
        ['rm-dp-material', 'rm-dp-reut', 'rm-dp-tamano'].forEach(function (id) { var e = el(id); if (e) e.disabled = dis; });
    }
    function updateDpReutVis() {
        var esReut = val('rm-dp-material') === 'reutilizado';
        el('rm-dp-reut-wrap').style.display = esReut ? '' : 'none';
        el('rm-dp-reut').disabled = !esReut; // si no es reutilizado, no se envía
    }

    function setValorField() {
        var campo = val('rm-campo');
        var wrap = el('rm-valor-wrap'), noval = el('rm-novalor'), poste = el('rm-poste-wrap');
        var sel = el('rm-valor');

        // reset
        wrap.style.display = 'none'; sel.disabled = true;
        noval.style.display = 'none';
        poste.style.display = 'none'; setDpDisabled(true);

        if (campo === 'datos_poste') {
            poste.style.display = '';
            setDpDisabled(false);
            updateDpReutVis();
        } else if (CONDENUMS[campo]) {
            wrap.style.display = ''; sel.disabled = false;
            var cur = sel.value;
            sel.innerHTML = '';
            Object.keys(CONDENUMS[campo].options).forEach(function (v) {
                var o = document.createElement('option');
                o.value = v; o.textContent = CONDENUMS[campo].options[v];
                sel.appendChild(o);
            });
            if (cur) sel.value = cur;
        } else if (campo && campo !== 'siempre') {
            noval.style.display = '';
            el('rm-novalor-txt').textContent = 'Se aplica cuando “' + labelCampo(campo) + '” está en Sí.';
        }
    }

    function setQtyMode(m) {
        qtyMode = m;
        document.querySelectorAll('#rm-qtymode button').forEach(function (b) { b.classList.toggle('active', b.getAttribute('data-m') === m); });
        var campoCant = el('rm-campocant');
        var por = m === 'porcada';
        el('rm-qty-fijalbl').style.display = por ? 'none' : '';
        el('rm-qty-porlbl').style.display = por ? '' : 'none';
        campoCant.style.display = por ? '' : 'none';
        campoCant.disabled = !por; // deshabilitado => no se envía cuando es "fija"
        updatePreview();
    }

    function updatePreview() {
        var campo = val('rm-campo');
        var valor = val('rm-valor');
        var base  = val('rm-base');
        var campoCant = qtyMode === 'porcada' ? val('rm-campocant') : '';
        var matText = $j ? ($j('#rm-material option:selected').text() || '') : '';

        el('rm-summary').textContent = 'Se aplica: ' + textoCond(campo, valor);
        var qt = qtyText(base, campoCant);
        el('rm-qty-res').textContent = qt;
        el('rm-pv-cond').textContent = textoCond(campo, valor);
        el('rm-pv-qty').textContent = qt;
        el('rm-pv-mat').textContent = matText || '— elegí un material —';
    }

    function resetBuilder() {
        el('rm-method').value = 'POST';
        drawer.setAttribute('action', STORE_URL);
        el('rm-b-title').textContent = 'Nueva regla';
        el('rm-desc').value = '';
        el('rm-campo').value = 'siempre';
        el('rm-base').value = '1';
        el('rm-activo').value = '1';
        el('rm-dp-material').value = '';
        el('rm-dp-reut').value = '';
        el('rm-dp-tamano').value = '';
        el('rm-estado-sw').classList.add('on');
        el('rm-estado-txt').textContent = 'La regla está activa y se aplicará.';
        if ($j) $j('#rm-material').val(null).trigger('change.select2');
        setValorField();
        setQtyMode('fija');
        updatePreview();
    }

    function fillBuilder(r) {
        el('rm-method').value = 'PUT';
        drawer.setAttribute('action', UPDATE_TPL.replace('__ID__', r.id));
        el('rm-b-title').textContent = 'Editar regla';
        el('rm-desc').value = r.descripcion || '';
        el('rm-campo').value = r.condicion_campo || 'siempre';
        setValorField();

        if (r.condicion_campo === 'datos_poste') {
            var p = parseDP(r.condicion_valor);
            el('rm-dp-material').value = p.material;
            updateDpReutVis();
            if (p.reutilizado) el('rm-dp-reut').value = p.reutilizado;
            el('rm-dp-tamano').value = p.tamano;
        } else if (r.condicion_valor && CONDENUMS[r.condicion_campo]) {
            el('rm-valor').value = r.condicion_valor;
        }

        el('rm-base').value = r.cantidad_base || 1;

        if ($j) {
            var opt = new Option(r.material_text || ('#' + r.material_id), r.material_id, true, true);
            $j('#rm-material').append(opt).trigger('change.select2');
        }

        if (r.cantidad_campo) {
            setQtyMode('porcada');
            el('rm-campocant').value = r.cantidad_campo;
        } else {
            setQtyMode('fija');
        }

        var activo = !!r.activo;
        el('rm-activo').value = activo ? '1' : '0';
        el('rm-estado-sw').classList.toggle('on', activo);
        el('rm-estado-txt').textContent = activo ? 'La regla está activa y se aplicará.' : 'La regla está inactiva (no se aplica).';
        updatePreview();
    }

    var btnNew = el('rm-new');
    if (btnNew) btnNew.addEventListener('click', function () { resetBuilder(); openBuilder(); });
    el('rm-b-close').addEventListener('click', closeBuilder);
    el('rm-b-cancel').addEventListener('click', closeBuilder);
    ovB.addEventListener('click', closeBuilder);

    el('rm-campo').addEventListener('change', function () { setValorField(); updatePreview(); });
    el('rm-valor').addEventListener('change', updatePreview);
    el('rm-dp-material').addEventListener('change', function () { updateDpReutVis(); updatePreview(); });
    el('rm-dp-reut').addEventListener('change', updatePreview);
    el('rm-dp-tamano').addEventListener('change', updatePreview);
    el('rm-base').addEventListener('input', updatePreview);
    el('rm-campocant').addEventListener('change', updatePreview);
    el('rm-qtymode').addEventListener('click', function (e) {
        var b = e.target.closest('button'); if (b) setQtyMode(b.getAttribute('data-m'));
    });
    el('rm-estado-sw').addEventListener('click', function () {
        var on = this.classList.toggle('on');
        el('rm-activo').value = on ? '1' : '0';
        el('rm-estado-txt').textContent = on ? 'La regla está activa y se aplicará.' : 'La regla está inactiva (no se aplica).';
    });

    document.querySelectorAll('[data-edit]').forEach(function (b) {
        b.addEventListener('click', function () {
            var r = RULES[b.getAttribute('data-edit')];
            if (r) { resetBuilder(); fillBuilder(r); openBuilder(); }
        });
    });

    drawer.addEventListener('submit', function (e) {
        if ($j && !$j('#rm-material').val()) { e.preventDefault(); alert('Elegí un material.'); return; }
        if (!el('rm-desc').value.trim()) { e.preventDefault(); alert('Poné una descripción.'); return; }
        if (val('rm-campo') === 'datos_poste' && !val('rm-dp-material') && !val('rm-dp-tamano')) {
            e.preventDefault(); alert('En “Datos del poste” indicá al menos material o tamaño.');
        }
    });

    // ============ SIMULADOR ============
    var ovS = el('rm-s-ov'), sim = el('rm-sim');
    var SIM_URL = "{{ route('admin.trabajos.reglas-materiales.simular') }}";
    var simTimer = null;

    function openSim() { ovS.classList.add('open'); sim.classList.add('open'); runSim(); }
    function closeSim() { ovS.classList.remove('open'); sim.classList.remove('open'); }

    function runSim() {
        var fd = new FormData();
        fd.append('_token', csrf);
        document.querySelectorAll('#rm-sim-bools .rm-chip').forEach(function (c) {
            fd.append('bool[' + c.getAttribute('data-col') + ']', c.classList.contains('on') ? 1 : 0);
        });
        document.querySelectorAll('.rm-sim-enum').forEach(function (s) {
            if (s.value) fd.append('enum[' + s.getAttribute('data-col') + ']', s.value);
        });
        document.querySelectorAll('.rm-sim-num').forEach(function (n) {
            fd.append('num[' + n.getAttribute('data-col') + ']', n.value || 0);
        });
        fetch(SIM_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
            .then(function (r) { return r.json(); })
            .then(function (d) { renderSim(d.results || []); })
            .catch(function () { renderSim([]); });
    }

    function renderSim(items) {
        var box = el('rm-sim-results');
        var empty = el('rm-sim-empty');
        box.innerHTML = '';
        if (!items.length) { empty.style.display = ''; el('rm-sim-count').textContent = '0 ítems'; return; }
        empty.style.display = 'none';
        el('rm-sim-count').textContent = items.length + (items.length === 1 ? ' ítem' : ' ítems');
        items.forEach(function (s) {
            var cant = Math.round(Number(s.cantidad) * 100) / 100;
            var row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:14px;padding:13px 18px;border-bottom:1px solid #f0f2f6;';
            row.innerHTML = '<div style="flex:1;min-width:0;">'
                + '<div class="text-dark" style="font-size:14.5px;font-weight:600;">' + (s.descripcion || '—') + '</div>'
                + '<div style="font-size:12px;color:#8a97ab;">cód ' + (s.codigo || '—') + ' · ' + (s.reglas || []).join(', ') + '</div></div>'
                + '<span style="display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:34px;padding:0 12px;background:#eef0fb;color:#3949ab;border-radius:8px;font-size:15px;font-weight:700;">' + cant + '</span>';
            box.appendChild(row);
        });
    }

    function scheduleSim() { clearTimeout(simTimer); simTimer = setTimeout(runSim, 300); }

    el('rm-open-sim').addEventListener('click', openSim);
    el('rm-s-close').addEventListener('click', closeSim);
    ovS.addEventListener('click', closeSim);
    document.querySelectorAll('#rm-sim-bools .rm-chip').forEach(function (c) {
        c.addEventListener('click', function () { c.classList.toggle('on'); scheduleSim(); });
    });
    document.querySelectorAll('.rm-sim-enum').forEach(function (s) { s.addEventListener('change', scheduleSim); });
    document.querySelectorAll('.rm-sim-num').forEach(function (n) { n.addEventListener('input', scheduleSim); });

    applyFilter();
})();
</script>
@endsection
