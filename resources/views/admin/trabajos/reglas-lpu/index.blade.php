@extends('layouts.simple.master')

@section('css')
<style>
    /* ===== Reglas de LPU — diseño "Reglas de LPU (Desktop).dc.html" ===== */
    .rl { --indigo:#4f5fbf; --indigo-d:#3949ab; --navy:#1b2a63; --verde:#2ba95f; --amber:#b3730a; }
    .rl .rl-card { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(30,40,70,.06),0 8px 24px rgba(30,40,70,.04); }

    .rl-banner { background:#eef0fb; border:1px solid #d7ddf6; border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:flex-start; gap:10px; font-size:13.5px; color:var(--indigo-d); font-weight:500; }

    .rl-toolbar { padding:16px 20px; border-bottom:1px solid #eef1f6; background:#fafbfd; border-radius:12px 12px 0 0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; }
    .rl-pill-activas { display:inline-flex; align-items:center; gap:8px; background:#e5f6ea; color:#1f7a3d; border:1px solid #bfe6cd; border-radius:20px; padding:7px 14px; font-size:13.5px; font-weight:600; }
    .rl-pill-activas .dot { width:8px; height:8px; border-radius:50%; background:#2ba95f; }
    .rl-sel { border:1.5px solid #d5dce6; border-radius:8px; background:#fff; padding:8px 10px; font-size:14px; color:#2b3247; }
    .rl-seg { display:flex; background:#eef1f6; border-radius:8px; padding:3px; }
    .rl-seg button { border:none; background:transparent; border-radius:6px; padding:7px 14px; font-size:13.5px; font-weight:500; color:#5a6b82; cursor:pointer; }
    .rl-seg button.active { background:#fff; color:#2b3247; box-shadow:0 1px 2px rgba(30,40,70,.12); }
    .rl-search { border:1.5px solid #d5dce6; border-radius:8px; background:#fff; padding:9px 12px 9px 34px; font-size:14px; color:#2b3247; width:340px; max-width:100%; }
    .rl-search:focus { outline:none; border-color:var(--indigo); }

    .rl-group { border:1px solid #e9edf3; border-radius:11px; margin:12px 16px; overflow:hidden; }
    .rl-group-head { display:flex; align-items:center; gap:12px; padding:13px 18px; background:#f4f6fb; cursor:pointer; user-select:none; }
    .rl-group-head:hover { background:#eef1f8; }
    .rl-group-head .chev { transition:transform .2s; color:#5a6b82; }
    .rl-group.collapsed .chev { transform:rotate(-90deg); }
    .rl-group.collapsed .rl-group-body { display:none; }
    .rl-group-tag { display:inline-flex; align-items:center; border-radius:7px; padding:5px 12px; font-size:13.5px; font-weight:600; color:#fff; }
    .rl-group-count { font-size:13px; color:#8a97ab; font-weight:500; }

    .rl-row { display:flex; align-items:center; gap:16px; padding:15px 18px; border-top:1px solid #eef0f3; flex-wrap:wrap; background:#fff; }
    .rl-row.off { opacity:.55; }
    .rl-badge { width:60px; height:60px; border-radius:11px; display:flex; flex-direction:column; align-items:center; justify-content:center; flex:none; color:#fff; }
    .rl-badge .n { font-size:23px; font-weight:700; line-height:1; font-variant-numeric:tabular-nums; }
    .rl-badge .t { font-size:8px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; opacity:.85; margin-top:3px; }
    .rl-condbox { flex:1; min-width:260px; }
    .rl-desc { font-size:15px; font-weight:600; color:#2b3247; margin-bottom:7px; }
    .rl-chips { display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
    .rl-chip { display:inline-flex; align-items:center; padding:5px 11px; border-radius:7px; font-size:12.5px; font-weight:600; background:#eef0fb; color:var(--indigo-d); white-space:nowrap; }
    .rl-chip.none { background:#f0f2f6; color:#8a97ab; font-weight:500; font-style:italic; }
    .rl-arrow { color:#b7c1cf; flex:none; }
    .rl-lpu { min-width:250px; background:#fff6e8; border:1px solid #f2dcae; border-radius:9px; padding:10px 14px; }
    .rl-lpu .code { font-variant-numeric:tabular-nums; font-size:14px; font-weight:700; color:var(--amber); }
    .rl-lpu .d { font-size:12.5px; color:#7a5a1f; margin-top:2px; }
    .rl-lpu .p { font-size:12px; color:#9a8145; margin-top:4px; font-variant-numeric:tabular-nums; }
    .rl-actions { display:flex; align-items:center; gap:8px; margin-left:auto; }
    .rl-ic { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; background:#fff; cursor:pointer; }
    .rl-ic.edit { border:1.5px solid #e2e6ee; color:var(--indigo); }
    .rl-ic.edit:hover { background:#eef0fb; border-color:#c9c9ef; }
    .rl-ic.del { border:1.5px solid #f3d4cf; color:#e05a45; }
    .rl-ic.del:hover { background:#fdeeeb; border-color:#efbcb2; }

    .rl-sw { width:46px; height:25px; border-radius:20px; padding:3px; cursor:pointer; transition:background .2s; flex:none; background:#c9d0dc; }
    .rl-sw.on { background:var(--verde); }
    .rl-sw .knob { width:19px; height:19px; border-radius:50%; background:#fff; transition:transform .2s; }
    .rl-sw.on .knob { transform:translateX(21px); }

    .rl-btn-nav { background:var(--navy); color:#fff; border:none; border-radius:8px; padding:11px 22px; font-size:15px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px; }
    .rl-btn-nav:hover { background:#142050; color:#fff; }
    .rl-btn-out { border:1.5px solid var(--indigo); color:var(--indigo); background:#fff; border-radius:8px; padding:10px 18px; font-size:15px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px; }
    .rl-btn-out:hover { background:#eef0fb; }

    .rl-ov { position:fixed; inset:0; background:rgba(20,26,46,.44); z-index:1040; display:none; }
    .rl-ov.open { display:block; }
    .rl-drawer { position:fixed; top:0; right:0; bottom:0; width:600px; max-width:94vw; background:#f3f5f9; z-index:1041; box-shadow:-8px 0 40px rgba(20,30,60,.22); transform:translateX(100%); transition:transform .26s cubic-bezier(.22,1,.36,1); display:flex; flex-direction:column; }
    .rl-drawer.open { transform:translateX(0); }
    .rl-dh { padding:18px 24px; background:#fff; border-bottom:1px solid #edf0f5; display:flex; align-items:center; justify-content:space-between; flex:none; }
    .rl-dh.dark { background:var(--navy); color:#fff; }
    .rl-db { flex:1; overflow-y:auto; padding:22px 24px; }
    .rl-df { padding:16px 24px; background:#fff; border-top:1px solid #edf0f5; display:flex; align-items:center; justify-content:flex-end; gap:10px; flex:none; }

    .rl-step { background:#fff; border:1px solid #e6eaf1; border-radius:11px; padding:18px; margin-bottom:18px; }
    .rl-step-t { display:flex; align-items:center; gap:8px; font-size:13.5px; font-weight:600; color:#2b3648; margin-bottom:14px; }
    .rl-step-n { width:22px; height:22px; border-radius:6px; background:#eef0fb; color:var(--indigo-d); display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; }
    .rl-in { width:100%; border:1.6px solid #97a2b2; background:#fff; border-radius:8px; padding:10px 12px; font-size:14.5px; color:#2b3247; outline:none; }
    .rl-in:focus { border-color:var(--indigo); }
    .rl-lbl { display:block; font-size:12.5px; font-weight:500; color:#5a6b82; margin-bottom:6px; }
    .rl-summary { margin-top:14px; background:#eef0fb; border:1px solid #d7ddf6; border-radius:8px; padding:11px 14px; font-size:13.5px; color:var(--indigo-d); font-weight:500; }
    .rl-preview { margin-top:8px; display:flex; align-items:center; gap:13px; background:#fff; border:1px dashed #c4ccdb; border-radius:10px; padding:14px 16px; flex-wrap:wrap; }
    .rl-cap { font-size:11.5px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#7a8699; }
    .rl-empty { padding:44px 16px; text-align:center; color:#8a97ab; font-size:15px; }
    label { color:#000; }
</style>
@endsection

@section('main_content')
@php
    $precio = fn ($lpu) => $lpu
        ? 'Mant. $ ' . number_format((float) $lpu->precio_mantenimiento, 2, ',', '.') . ' · Obra $ ' . number_format((float) $lpu->precio_obras, 2, ',', '.')
        : '';
    $rulesForJs = $reglas->map(fn ($r) => [
        'id'          => $r->id,
        'descripcion' => $r->descripcion,
        'prioridad'   => (int) $r->prioridad,
        'desmonto'    => is_null($r->desmonto) ? '' : (string) (int) $r->desmonto,
        'coloco'      => is_null($r->coloco) ? '' : (string) (int) $r->coloco,
        'tipo_poste'  => $r->tipo_poste ?? '',
        'material'    => $r->material ?? '',
        'tamano'      => $r->tamano ?? '',
        'lpu_id'      => $r->lpu_id,
        'lpu_text'    => $r->lpu ? ($r->lpu->codigo_lpu . ' — ' . $r->lpu->descripcion) : '',
        'lpu_code'    => $r->lpu?->codigo_lpu,
        'activo'      => (bool) $r->activo,
    ])->values();
@endphp
<div class="container-fluid rl">

    <div class="page-title">
        <div class="row align-items-end">
            <div class="col-sm-6">
                <h4 class="mb-1 text-dark">Reglas de LPU</h4>
                {{-- <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.ordenes.index') }}">Trabajos</a></li>
                    <li class="breadcrumb-item active text-dark">Reglas de LPU</li>
                </ol> --}}
            </div>
            <div class="col-sm-6 d-flex justify-content-sm-end align-items-center gap-2 mt-2 mt-sm-0">
                <button type="button" class="rl-btn-out" id="rl-open-sim"><i data-feather="play" style="width:16px;"></i> Probar reglas</button>
                @can('trabajos_reglas_lpu.create')
                <button type="button" class="rl-btn-nav" id="rl-new"><i data-feather="plus" style="width:16px;"></i> Nueva regla</button>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    {{-- <div class="rl-banner">
        <i data-feather="info" style="width:17px;flex:none;margin-top:1px;"></i>
        <span>Las reglas se evalúan de mayor a menor prioridad. Si un trabajo coincide con varias, se asigna el LPU de la regla de <strong>mayor prioridad</strong>.</span>
    </div> --}}

    <div class="rl-card">
        <div class="rl-toolbar">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="rl-pill-activas"><span class="dot"></span>{{ $activas }} activa{{ $activas === 1 ? '' : 's' }}</span>
                <label class="d-flex align-items-center gap-2 mb-0 text-dark" style="font-size:13.5px;">Prioridad
                    <select class="rl-sel" id="rl-f-tier">
                        <option value="">Todos los tramos</option>
                        <option value="alta">Alta (90+)</option>
                        <option value="media">Media (60–89)</option>
                        <option value="baja">Baja (&lt;60)</option>
                    </select>
                </label>
                <div class="rl-seg" id="rl-f-estado">
                    <button type="button" data-e="all" class="active">Todas</button>
                    <button type="button" data-e="1">Activas</button>
                    <button type="button" data-e="0">Inactivas</button>
                </div>
            </div>
            <div class="position-relative">
                <i data-feather="search" style="width:16px;position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#a9b3c2;"></i>
                <input type="text" class="rl-search" id="rl-search" placeholder="Buscar por código LPU, descripción o condición…">
            </div>
        </div>

        <div id="rl-list">
            @forelse($grupos as $g)
                <div class="rl-group" data-tier="{{ $g['tier'] }}">
                    <div class="rl-group-head">
                        <i data-feather="chevron-down" class="chev" style="width:16px;"></i>
                        <span class="rl-group-tag" style="background:{{ $g['color'] }}">{{ $g['label'] }}</span>
                        <span class="rl-group-count">Prioridad {{ $g['range'] }}</span>
                        <span class="rl-group-count" style="margin-left:auto;">{{ $g['reglas']->count() }} regla{{ $g['reglas']->count() === 1 ? '' : 's' }}</span>
                    </div>
                    <div class="rl-group-body">
                        @foreach($g['reglas'] as $r)
                            @php $chips = $r->chips(); @endphp
                            <div class="rl-row {{ $r->activo ? '' : 'off' }}"
                                data-tier="{{ $r->tier() }}" data-activo="{{ $r->activo ? 1 : 0 }}"
                                data-search="{{ \Illuminate\Support\Str::lower(($r->lpu?->codigo_lpu).' '.($r->lpu?->descripcion).' '.$r->descripcion.' '.implode(' ', $chips)) }}">
                                <div class="rl-badge" style="background:{{ $r->tierColor() }}">
                                    <span class="n">{{ $r->prioridad }}</span><span class="t">prioridad</span>
                                </div>
                                <div class="rl-condbox">
                                    <div class="rl-desc text-dark">{{ $r->descripcion ?: 'Regla sin descripción' }}</div>
                                    <div class="rl-chips">
                                        @forelse($chips as $c)
                                            <span class="rl-chip">{{ $c }}</span>
                                        @empty
                                            <span class="rl-chip none">Sin condiciones · siempre</span>
                                        @endforelse
                                    </div>
                                </div>
                                <i data-feather="arrow-right" class="rl-arrow" style="width:20px;"></i>
                                <div class="rl-lpu">
                                    <div class="code">LPU {{ $r->lpu?->codigo_lpu ?? '—' }}</div>
                                    <div class="d text-dark">{{ $r->lpu?->descripcion ?? '— LPU no encontrado —' }}</div>
                                    <div class="p">{{ $precio($r->lpu) }}</div>
                                </div>
                                <div class="rl-actions">
                                    @can('trabajos_reglas_lpu.edit')
                                    <div class="rl-sw {{ $r->activo ? 'on' : '' }}" title="Activar / desactivar"
                                        data-url="{{ route('admin.trabajos.reglas-lpu.status', $r->id) }}"><div class="knob"></div></div>
                                    <button type="button" class="rl-ic edit" title="Editar" data-edit="{{ $r->id }}"><i data-feather="edit-2" style="width:16px;"></i></button>
                                    @endcan
                                    @can('trabajos_reglas_lpu.trash')
                                    <form action="{{ route('admin.trabajos.reglas-lpu.destroy', $r->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta regla?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rl-ic del" title="Eliminar"><i data-feather="trash-2" style="width:16px;"></i></button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rl-empty">Todavía no hay reglas cargadas. Creá la primera con “Nueva regla”.</div>
            @endforelse
            <div class="rl-empty" id="rl-noresult" style="display:none;">No hay reglas que coincidan con la búsqueda o los filtros.</div>
        </div>

        <div style="padding:16px 22px 20px;border-top:1px solid #eef1f6;font-size:13px;color:#8a97ab;">
            {{ $reglas->count() }} regla{{ $reglas->count() === 1 ? '' : 's' }} en total
        </div>
    </div>
</div>

{{-- ============ DRAWER: constructor ============ --}}
<div class="rl-ov" id="rl-b-ov"></div>
<form class="rl-drawer" id="rl-drawer" method="POST" action="{{ route('admin.trabajos.reglas-lpu.store') }}">
    @csrf
    <input type="hidden" name="_method" id="rl-method" value="POST">
    <input type="hidden" name="activo" id="rl-activo" value="1">
    <input type="hidden" name="desmonto" id="rl-desmonto" value="">
    <input type="hidden" name="coloco" id="rl-coloco" value="">

    <div class="rl-dh">
        <div>
            <h6 class="mb-0 text-dark" style="font-size:18px;font-weight:600;" id="rl-b-title">Nueva regla</h6>
            <span class="text-dark" style="font-size:12.5px;opacity:.6;">Constructor de regla de LPU</span>
        </div>
        <button type="button" class="btn btn-light btn-sm" id="rl-b-close"><i data-feather="x" style="width:18px;"></i></button>
    </div>

    <div class="rl-db">
        {{-- 1. Descripción --}}
        <div class="rl-step">
            <span class="rl-step-t text-dark"><span class="rl-step-n">1</span>Descripción de la regla</span>
            <input type="text" name="descripcion" id="rl-desc" class="rl-in text-dark" placeholder="Ej: Desmonte y colocación TERMINAL">
        </div>

        {{-- 2. Prioridad --}}
        <div class="rl-step">
            <span class="rl-step-t text-dark"><span class="rl-step-n">2</span>Prioridad</span>
            <div class="d-flex align-items-center gap-3">
                <input type="number" min="0" max="999" name="prioridad" id="rl-prio" class="rl-in text-dark" style="width:110px;font-weight:600;" value="70">
                <span id="rl-tier-badge" style="border-radius:7px;padding:6px 12px;font-size:13px;font-weight:600;color:#fff;background:#4f5fbf;">Media</span>
            </div>
            <div class="text-dark" style="margin-top:10px;font-size:13px;opacity:.75;">Si varias reglas coinciden, gana la de <strong>mayor prioridad</strong>.</div>
        </div>

        {{-- 3. Condiciones --}}
        <div class="rl-step">
            <span class="rl-step-t text-dark"><span class="rl-step-n">3</span>¿Cuándo aplica?</span>
            <div class="text-dark" style="font-size:12.5px;opacity:.7;margin:-8px 0 16px 30px;">Dejá en «Cualquiera» las condiciones que no importan.</div>

            <div class="mb-3">
                <span class="rl-lbl text-dark">Desmontó poste</span>
                <div class="rl-seg" data-tri="rl-desmonto">
                    <button type="button" data-v="">Cualquiera</button><button type="button" data-v="1">Sí</button><button type="button" data-v="0">No</button>
                </div>
            </div>
            <div class="mb-3">
                <span class="rl-lbl text-dark">Colocó poste</span>
                <div class="rl-seg" data-tri="rl-coloco">
                    <button type="button" data-v="">Cualquiera</button><button type="button" data-v="1">Sí</button><button type="button" data-v="0">No</button>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <span class="rl-lbl text-dark">Tipo de poste</span>
                    <select name="tipo_poste" id="rl-tipo" class="rl-in text-dark">
                        <option value="">Cualquiera</option>
                        @foreach($tipoOptions as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="col-6">
                    <span class="rl-lbl text-dark">Material del poste</span>
                    <select name="material" id="rl-material" class="rl-in text-dark">
                        <option value="">Cualquiera</option>
                        @foreach($materialOptions as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="col-6">
                    <span class="rl-lbl text-dark">Tamaño del poste</span>
                    <select name="tamano" id="rl-tamano" class="rl-in text-dark">
                        <option value="">Cualquiera</option>
                        @foreach($tamanoOptions as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="rl-summary" id="rl-summary">Aplica: siempre (sin condiciones)</div>
        </div>

        {{-- 4. LPU resultante --}}
        <div class="rl-step">
            <span class="rl-step-t text-dark"><span class="rl-step-n">4</span>LPU resultante</span>
            <select name="lpu_id" id="rl-lpu" class="form-control" style="width:100%"></select>
        </div>

        {{-- 5. Estado --}}
        <div class="rl-step d-flex align-items-center justify-content-between" style="margin-bottom:18px;">
            <div>
                <span class="rl-step-t mb-0 text-dark"><span class="rl-step-n">5</span>Estado</span>
                <div class="text-dark" style="font-size:13px;margin-top:5px;padding-left:30px;opacity:.75;" id="rl-estado-txt">La regla está activa y se evalúa.</div>
            </div>
            <div class="rl-sw on" id="rl-estado-sw"><div class="knob"></div></div>
        </div>

        {{-- Vista previa --}}
        <span class="rl-cap text-dark">Vista previa en el listado</span>
        <div class="rl-preview">
            <div class="rl-badge" id="rl-pv-badge" style="width:52px;height:52px;background:#4f5fbf;"><span class="n" id="rl-pv-prio">70</span><span class="t">prioridad</span></div>
            <div class="rl-chips" id="rl-pv-chips" style="flex:1;min-width:150px;"></div>
            <i data-feather="arrow-right" style="width:18px;color:#b7c1cf;"></i>
            <span class="rl-chip" style="background:#fff6e8;color:#b3730a;border:1px solid #f2dcae;" id="rl-pv-lpu">Elegí un LPU</span>
        </div>
    </div>

    <div class="rl-df">
        <button type="button" class="btn btn-light" id="rl-b-cancel">Cancelar</button>
        <button type="submit" class="btn" style="background:var(--indigo);color:#fff;"><i data-feather="save" style="width:16px;"></i> Guardar</button>
    </div>
</form>

{{-- ============ DRAWER: simulador ============ --}}
<div class="rl-ov" id="rl-s-ov"></div>
<div class="rl-drawer" id="rl-sim">
    <div class="rl-dh dark">
        <div class="d-flex align-items-center gap-2">
            <i data-feather="play" style="width:20px;"></i>
            <div><h6 class="mb-0" style="font-size:18px;font-weight:600;">Probar reglas</h6>
                <span style="font-size:12.5px;color:#b7c1e0;">Simulá un trabajo y mirá qué LPU se le asignaría</span></div>
        </div>
        <button type="button" class="btn btn-sm" style="border:1.5px solid rgba(255,255,255,.25);color:#fff;" id="rl-s-close"><i data-feather="x" style="width:18px;"></i></button>
    </div>

    <div class="rl-db">
        <div class="rl-step">
            <span class="text-dark" style="font-size:13.5px;font-weight:600;">Respuestas del trabajo de ejemplo</span>
            <div class="row g-3 mt-1">
                <div class="col-6">
                    <span class="rl-lbl text-dark">Desmontó poste</span>
                    <div class="rl-seg" data-simbool="desmonto">
                        <button type="button" data-v="1">Sí</button><button type="button" data-v="0" class="active">No</button>
                    </div>
                </div>
                <div class="col-6">
                    <span class="rl-lbl text-dark">Colocó poste</span>
                    <div class="rl-seg" data-simbool="coloco">
                        <button type="button" data-v="1" class="active">Sí</button><button type="button" data-v="0">No</button>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-4">
                    <span class="rl-lbl text-dark">Tipo de poste</span>
                    <select class="rl-in text-dark rl-sim" data-k="tipo_poste"><option value="">—</option>
                        @foreach($tipoOptions as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="col-4">
                    <span class="rl-lbl text-dark">Material</span>
                    <select class="rl-in text-dark rl-sim" data-k="material"><option value="">—</option>
                        @foreach($materialOptions as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="col-4">
                    <span class="rl-lbl text-dark">Tamaño</span>
                    <select class="rl-in text-dark rl-sim" data-k="tamano"><option value="">—</option>
                        @foreach($tamanoOptions as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Ganador --}}
        <div id="rl-sim-winner" style="display:none;background:var(--navy);border-radius:12px;padding:20px 22px;color:#fff;margin-bottom:16px;">
            <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#9fb0e0;margin-bottom:8px;">LPU asignado</div>
            <div style="font-size:20px;font-weight:700;font-variant-numeric:tabular-nums;" id="rl-win-code"></div>
            <div style="font-size:13.5px;color:#dbe3f7;margin-top:3px;" id="rl-win-desc"></div>
            <div style="font-size:13px;color:#b7c1e0;margin-top:6px;" id="rl-win-price"></div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.15);font-size:12.5px;color:#b7c1e0;" id="rl-win-rule"></div>
        </div>
        <div id="rl-sim-empty" class="rl-card" style="display:none;border:1px solid #e6eaf1;padding:28px 16px;text-align:center;color:#8a97ab;margin-bottom:16px;">
            Ningún LPU asignado: ninguna regla activa coincide con este trabajo.
        </div>

        {{-- Ranking --}}
        <div class="rl-card" style="border:1px solid #e6eaf1;overflow:hidden;">
            <div style="padding:12px 18px;background:#f4f6fb;border-bottom:1px solid #eef1f6;font-size:13px;font-weight:600;color:#3a4658;display:flex;justify-content:space-between;">
                <span class="text-dark">Reglas que coinciden</span><span id="rl-sim-count" style="color:#8a97ab;font-weight:500;">0</span>
            </div>
            <div id="rl-sim-matches"></div>
            <div id="rl-sim-nomatch" class="rl-empty" style="padding:28px 16px;">Ninguna regla coincide.</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    if (window.feather) feather.replace();

    var TIPO     = @json($tipoOptions);
    var MATERIAL = @json($materialOptions);
    var TAMANO   = @json($tamanoOptions);
    var RULES = {};
    (@json($rulesForJs)).forEach(function (r) { RULES[r.id] = r; });

    var $j = window.jQuery;
    function el(id) { return document.getElementById(id); }
    function val(id) { var e = el(id); return e ? e.value : ''; }

    function tierOf(p) {
        p = Number(p) || 0;
        if (p >= 90) return { label: 'Alta',  color: '#1b2a63' };
        if (p >= 60) return { label: 'Media', color: '#4f5fbf' };
        return { label: 'Baja', color: '#8a97ab' };
    }
    function draftChips() {
        var c = [];
        var d = val('rl-desmonto'); if (d === '1') c.push('Desmontó'); else if (d === '0') c.push('No desmontó');
        var co = val('rl-coloco');  if (co === '1') c.push('Colocó');   else if (co === '0') c.push('No colocó');
        var tp = val('rl-tipo');     if (tp) c.push(TIPO[tp] || tp);
        var ma = val('rl-material'); if (ma) c.push(MATERIAL[ma] || ma);
        var ta = val('rl-tamano');   if (ta) c.push(TAMANO[ta] || ta);
        return c;
    }
    function chipHtml(list) {
        if (!list.length) return '<span class="rl-chip none">Sin condiciones · siempre</span>';
        return list.map(function (x) { return '<span class="rl-chip">' + x + '</span>'; }).join('');
    }

    // ===== FILTROS =====
    var fTier = '', fEstado = 'all';
    var search = el('rl-search');
    function applyFilter() {
        var q = (search.value || '').trim().toLowerCase();
        document.querySelectorAll('.rl-group').forEach(function (grp) {
            var vis = 0;
            grp.querySelectorAll('.rl-row').forEach(function (row) {
                var okT = !fTier || row.getAttribute('data-tier') === fTier;
                var okE = fEstado === 'all' || row.getAttribute('data-activo') === fEstado;
                var okS = !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
                var show = okT && okE && okS;
                row.style.display = show ? '' : 'none';
                if (show) vis++;
            });
            grp.style.display = vis ? '' : 'none';
        });
        var any = Array.prototype.some.call(document.querySelectorAll('.rl-group'), function (g) { return g.style.display !== 'none'; });
        var nr = el('rl-noresult'); if (nr) nr.style.display = any ? 'none' : '';
    }
    if (search) search.addEventListener('input', applyFilter);
    el('rl-f-tier').addEventListener('change', function () { fTier = this.value; applyFilter(); });
    el('rl-f-estado').addEventListener('click', function (e) {
        var b = e.target.closest('button'); if (!b) return;
        fEstado = b.getAttribute('data-e');
        this.querySelectorAll('button').forEach(function (x) { x.classList.toggle('active', x === b); });
        applyFilter();
    });
    document.querySelectorAll('.rl-group-head').forEach(function (h) {
        h.addEventListener('click', function () { h.closest('.rl-group').classList.toggle('collapsed'); });
    });

    // ===== TOGGLE ACTIVO =====
    var csrf = document.querySelector('#rl-drawer input[name="_token"]').value;
    document.querySelectorAll('.rl-sw[data-url]').forEach(function (sw) {
        sw.addEventListener('click', function () {
            var fd = new FormData(); fd.append('_token', csrf); fd.append('_method', 'PUT');
            fetch(sw.getAttribute('data-url'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    sw.classList.toggle('on', d.activo);
                    var row = sw.closest('.rl-row'); row.classList.toggle('off', !d.activo);
                    row.setAttribute('data-activo', d.activo ? 1 : 0); applyFilter();
                })
                .catch(function () { alert('No se pudo cambiar el estado.'); });
        });
    });

    // ===== DRAWER CONSTRUCTOR =====
    var ovB = el('rl-b-ov'), drawer = el('rl-drawer');
    var STORE_URL = "{{ route('admin.trabajos.reglas-lpu.store') }}";
    var UPDATE_TPL = "{{ route('admin.trabajos.reglas-lpu.update', '__ID__') }}";

    if ($j && $j.fn.select2) {
        $j('#rl-lpu').select2({
            width: '100%', dropdownParent: $j('#rl-drawer'),
            placeholder: 'Buscar en el catálogo LPU por código o descripción…',
            ajax: {
                url: "{{ route('admin.trabajos.reglas-lpu.buscarLpu') }}",
                dataType: 'json', delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; }
            },
            minimumInputLength: 1
        });
        $j('#rl-lpu').on('change', updatePreview);
    }

    function openBuilder() { ovB.classList.add('open'); drawer.classList.add('open'); }
    function closeBuilder() { ovB.classList.remove('open'); drawer.classList.remove('open'); }

    function setTri(hiddenId, value) {
        el(hiddenId).value = value;
        var seg = document.querySelector('.rl-seg[data-tri="' + hiddenId + '"]');
        if (seg) seg.querySelectorAll('button').forEach(function (b) { b.classList.toggle('active', b.getAttribute('data-v') === value); });
    }

    function updatePreview() {
        var chips = draftChips();
        el('rl-summary').textContent = chips.length ? 'Aplica cuando: ' + chips.join(' y ') : 'Aplica: siempre (sin condiciones)';
        el('rl-pv-chips').innerHTML = chipHtml(chips);
        var prio = val('rl-prio') || '0';
        var t = tierOf(prio);
        el('rl-pv-prio').textContent = prio;
        el('rl-pv-badge').style.background = t.color;
        el('rl-tier-badge').textContent = t.label;
        el('rl-tier-badge').style.background = t.color;
        var lpuText = $j ? ($j('#rl-lpu option:selected').text() || '') : '';
        el('rl-pv-lpu').textContent = lpuText ? ('LPU ' + lpuText.split(' — ')[0]) : 'Elegí un LPU';
    }

    function resetBuilder() {
        el('rl-method').value = 'POST';
        drawer.setAttribute('action', STORE_URL);
        el('rl-b-title').textContent = 'Nueva regla';
        el('rl-desc').value = '';
        el('rl-prio').value = '70';
        el('rl-tipo').value = ''; el('rl-material').value = ''; el('rl-tamano').value = '';
        setTri('rl-desmonto', ''); setTri('rl-coloco', '');
        el('rl-activo').value = '1'; el('rl-estado-sw').classList.add('on');
        el('rl-estado-txt').textContent = 'La regla está activa y se evalúa.';
        if ($j) $j('#rl-lpu').val(null).trigger('change.select2');
        updatePreview();
    }

    function fillBuilder(r) {
        el('rl-method').value = 'PUT';
        drawer.setAttribute('action', UPDATE_TPL.replace('__ID__', r.id));
        el('rl-b-title').textContent = 'Editar regla';
        el('rl-desc').value = r.descripcion || '';
        el('rl-prio').value = r.prioridad;
        el('rl-tipo').value = r.tipo_poste || ''; el('rl-material').value = r.material || ''; el('rl-tamano').value = r.tamano || '';
        setTri('rl-desmonto', r.desmonto || ''); setTri('rl-coloco', r.coloco || '');
        if ($j) { var o = new Option(r.lpu_text || ('#' + r.lpu_id), r.lpu_id, true, true); $j('#rl-lpu').append(o).trigger('change.select2'); }
        var activo = !!r.activo;
        el('rl-activo').value = activo ? '1' : '0';
        el('rl-estado-sw').classList.toggle('on', activo);
        el('rl-estado-txt').textContent = activo ? 'La regla está activa y se evalúa.' : 'La regla está inactiva (no se evalúa).';
        updatePreview();
    }

    var btnNew = el('rl-new'); if (btnNew) btnNew.addEventListener('click', function () { resetBuilder(); openBuilder(); });
    el('rl-b-close').addEventListener('click', closeBuilder);
    el('rl-b-cancel').addEventListener('click', closeBuilder);
    ovB.addEventListener('click', closeBuilder);

    document.querySelectorAll('.rl-seg[data-tri]').forEach(function (seg) {
        seg.addEventListener('click', function (e) {
            var b = e.target.closest('button'); if (!b) return;
            setTri(seg.getAttribute('data-tri'), b.getAttribute('data-v')); updatePreview();
        });
    });
    ['rl-tipo', 'rl-material', 'rl-tamano', 'rl-prio'].forEach(function (id) {
        el(id).addEventListener('input', updatePreview);
        el(id).addEventListener('change', updatePreview);
    });
    el('rl-estado-sw').addEventListener('click', function () {
        var on = this.classList.toggle('on');
        el('rl-activo').value = on ? '1' : '0';
        el('rl-estado-txt').textContent = on ? 'La regla está activa y se evalúa.' : 'La regla está inactiva (no se evalúa).';
    });
    document.querySelectorAll('[data-edit]').forEach(function (b) {
        b.addEventListener('click', function () { var r = RULES[b.getAttribute('data-edit')]; if (r) { resetBuilder(); fillBuilder(r); openBuilder(); } });
    });
    drawer.addEventListener('submit', function (e) {
        if ($j && !$j('#rl-lpu').val()) { e.preventDefault(); alert('Elegí un LPU resultante.'); }
    });

    // ===== SIMULADOR =====
    var ovS = el('rl-s-ov'), sim = el('rl-sim');
    var SIM_URL = "{{ route('admin.trabajos.reglas-lpu.simular') }}";
    var simState = { desmonto: '0', coloco: '1', tipo_poste: '', material: '', tamano: '' };
    var simTimer = null;

    function openSim() { ovS.classList.add('open'); sim.classList.add('open'); runSim(); }
    function closeSim() { ovS.classList.remove('open'); sim.classList.remove('open'); }

    function runSim() {
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('desmonto', simState.desmonto);
        fd.append('coloco', simState.coloco);
        if (simState.tipo_poste) fd.append('tipo_poste', simState.tipo_poste);
        if (simState.material) fd.append('material', simState.material);
        if (simState.tamano) fd.append('tamano', simState.tamano);
        fetch(SIM_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
            .then(function (r) { return r.json(); })
            .then(renderSim).catch(function () { renderSim({ winner: null, matches: [] }); });
    }

    function renderSim(d) {
        var win = d.winner, matches = d.matches || [];
        if (win) {
            el('rl-sim-winner').style.display = '';
            el('rl-sim-empty').style.display = 'none';
            el('rl-win-code').textContent = 'LPU ' + (win.lpuCodigo || '—');
            el('rl-win-desc').textContent = win.lpuDesc || '';
            el('rl-win-price').textContent = win.priceText || '';
            el('rl-win-rule').textContent = 'Regla ganadora: ' + (win.descripcion || '—') + ' · prioridad ' + win.prioridad;
        } else {
            el('rl-sim-winner').style.display = 'none';
            el('rl-sim-empty').style.display = '';
        }
        el('rl-sim-count').textContent = matches.length;
        var box = el('rl-sim-matches');
        box.innerHTML = '';
        el('rl-sim-nomatch').style.display = matches.length ? 'none' : '';
        matches.forEach(function (m) {
            var t = tierOf(m.prioridad);
            var row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid #f0f2f6;' + (m.ganadora ? 'background:#f0f9f3;' : '');
            row.innerHTML =
                '<span style="min-width:34px;height:34px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#fff;background:' + t.color + '">' + m.prioridad + '</span>'
                + '<div style="flex:1;min-width:0;"><div class="text-dark" style="font-size:13.5px;font-weight:600;">' + (m.descripcion || '—') + (m.ganadora ? ' <span style=\"color:#1f7a3d;font-size:11.5px;\">✓ gana</span>' : '') + '</div>'
                + '<div style="font-size:12px;color:#8a97ab;">LPU ' + (m.lpuCodigo || '—') + ' · ' + (m.lpuDesc || '') + '</div></div>';
            box.appendChild(row);
        });
    }
    function scheduleSim() { clearTimeout(simTimer); simTimer = setTimeout(runSim, 250); }

    el('rl-open-sim').addEventListener('click', openSim);
    el('rl-s-close').addEventListener('click', closeSim);
    ovS.addEventListener('click', closeSim);
    document.querySelectorAll('.rl-seg[data-simbool]').forEach(function (seg) {
        seg.addEventListener('click', function (e) {
            var b = e.target.closest('button'); if (!b) return;
            simState[seg.getAttribute('data-simbool')] = b.getAttribute('data-v');
            seg.querySelectorAll('button').forEach(function (x) { x.classList.toggle('active', x === b); });
            scheduleSim();
        });
    });
    document.querySelectorAll('.rl-sim').forEach(function (s) {
        s.addEventListener('change', function () { simState[s.getAttribute('data-k')] = s.value; scheduleSim(); });
    });

    applyFilter();
})();
</script>
@endsection
