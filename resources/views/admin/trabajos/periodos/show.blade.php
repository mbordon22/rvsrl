@extends('layouts.simple.master')

@section('css')
<style>
    /* ===== Certificación — diseño "CertPeriodo.dc.html" (Variante A) ===== */
    .cert-wrap { --verde:#28a745; --verde-d:#218a3a; --indigo:#4f5fbf; }
    .cert-card { background:#fff; border:1px solid #e6eaf1; border-radius:12px; }
    .cert-statusbar { padding:13px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
    .cert-badge-estado { background:#1b2a63; color:#fff; padding:5px 13px; border-radius:6px; font-size:12.5px; font-weight:600; }
    .cert-badge-estado.cerrado { background:#8794a8; }
    .cert-badge-cat { background:#20242e; color:#fff; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; }

    .cert-grid { display:flex; gap:22px; align-items:flex-start; }
    .cert-grid .sel { flex:1; min-width:0; overflow:hidden; }
    .cert-grid .side { position:sticky; top:16px; width:388px; flex:none; display:flex; flex-direction:column; gap:16px; }
    @media (max-width:1100px){ .cert-grid{ flex-direction:column; } .cert-grid .side{ width:100%; position:static; } }

    /* Tabla selección */
    .sel-head, .sel-row { display:grid; grid-template-columns:70px 100px 90px 1fr 110px 92px 96px; align-items:center; }
    .sel-head { background:#eef1f6; padding:11px 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.03em; color:#3a4658; }
    .sel-row { border-bottom:1px solid #eef0f3; cursor:pointer; transition:background .12s; background:#fff; }
    .sel-row:hover { background:#f6f8ff; }
    .sel-row.on { background:#f0f9f3; }
    .sel-row > div { padding:13px 6px; font-size:13.5px; color:#2b3247; }
    .sel-row .dom { font-weight:500; }
    .chip-cuad { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:24px; padding:0 8px; background:#eef1f6; border-radius:6px; font-weight:600; font-size:12.5px; color:#4a5568; }
    .pill-poste { display:inline-flex; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600; color:#fff; }
    .lpu-code { color:#5a6b82; font-variant-numeric:tabular-nums; }

    /* Switch */
    .sw { position:relative; display:inline-block; width:44px; height:24px; }
    .sw input { position:absolute; opacity:0; width:0; height:0; }
    .sw .track { display:block; width:44px; height:24px; border-radius:20px; background:#c9d0dc; padding:3px; transition:background .15s; }
    .sw .knob { display:block; width:18px; height:18px; border-radius:50%; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.2); transition:transform .15s; }
    .sw input:checked + .track { background:var(--verde); }
    .sw input:checked + .track .knob { transform:translateX(20px); }
    .sw input:disabled + .track { opacity:.6; }

    /* Chips filtro */
    .cert-chip { padding:6px 13px; border-radius:20px; font-size:12.5px; font-weight:500; cursor:pointer; border:1.5px solid #dde3ec; background:#fff; color:#7a8699; transition:all .12s; }
    .cert-chip.active { background:#eef0fb; border-color:#c2c9f0; color:#3949ab; }

    /* Sidebar resumen */
    .side-total { background:var(--verde); padding:16px 20px; display:flex; align-items:center; justify-content:space-between; }
    .side-total .lbl { color:#dff5e7; font-size:13px; font-weight:500; text-transform:uppercase; letter-spacing:.05em; }
    .side-total .val { color:#fff; font-size:21px; font-weight:700; font-variant-numeric:tabular-nums; }
    .lpu-row { display:flex; justify-content:space-between; gap:12px; padding:10px 0; border-top:1px solid #eef1f6; }
    .lpu-row .sub { font-size:14px; font-weight:600; color:#1f7a3d; white-space:nowrap; font-variant-numeric:tabular-nums; }
    .cons-row { display:flex; justify-content:space-between; gap:10px; padding:9px 20px; border-top:1px solid #f2f4f7; }

    /* Search */
    .cert-search { border:1.5px solid #d5dce6; border-radius:8px; background:#f7f9fc; padding:9px 12px 9px 33px; font-size:13.5px; color:#2b3247; width:240px; }
    .cert-search:focus { outline:none; border-color:#3c82ff; background:#fff; }

    /* Drawer */
    .drawer-ov { position:fixed; inset:0; background:rgba(20,26,40,.42); z-index:1040; display:none; }
    .drawer-ov.open { display:block; }
    .drawer { position:fixed; top:0; right:0; bottom:0; width:480px; max-width:92vw; background:#fff; z-index:1041; box-shadow:-10px 0 40px rgba(20,26,40,.2); transform:translateX(100%); transition:transform .26s cubic-bezier(.22,1,.36,1); display:flex; flex-direction:column; }
    .drawer.open { transform:translateX(0); }

    /* Sticky action bar (guardar) */
    .cert-actionbar { position:sticky; bottom:0; margin-top:18px; padding:14px 22px; display:flex; align-items:center; justify-content:space-between; gap:16px; box-shadow:0 -6px 22px rgba(30,40,70,.08); }

    label { color: #000 }
</style>
@endsection

@section('main_content')
@php $abierto = $periodo->estado === 'abierto'; @endphp
<div class="container-fluid cert-wrap">

    <div class="page-title">
        <div class="row">
            <div class="col-8"><h4 class="mb-0">{{ $periodo->nombre }}</h4></div>
            <div class="col-4">
                <ol class="breadcrumb mb-0" style="justify-content:flex-end;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.periodos.index') }}">Certificación</a></li>
                    <li class="breadcrumb-item active">Período</li>
                </ol>
            </div>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    {{-- Status bar --}}
    <div class="cert-card cert-statusbar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="cert-badge-estado {{ $abierto ? '' : 'cerrado' }}">{{ ucfirst($periodo->estado) }}</span>
            <strong class="text-dark">{{ $periodo->fecha_desde->format('d/m/Y') }} — {{ $periodo->fecha_hasta->format('d/m/Y') }}</strong>
            <span class="text-muted">| {{ $periodo->cuadrilla?->nombre ?? 'Todas las cuadrillas' }}</span>
            <span class="cert-badge-cat">{{ $periodo->categoria->label() }}</span>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form action="{{ route('admin.trabajos.periodos.cerrar', $periodo->id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('{{ $abierto ? '¿Cerrar el período? Sus trabajos quedarán certificados y bloqueados.' : '¿Reabrir el período? Sus trabajos volverán a aprobado.' }}')">
                @csrf
                <button class="btn btn-sm {{ $abierto ? 'btn-outline-warning' : 'btn-outline-secondary' }}" type="submit">
                    {{ $abierto ? 'Cerrar período' : 'Reabrir' }}
                </button>
            </form>
            <button type="button" class="btn btn-success btn-sm" id="btn-drawer">Generar Excel</button>
        </div>
    </div>

    <div class="cert-grid">
        {{-- ===== Selección de trabajos ===== --}}
        <section class="cert-card sel">
            <div style="padding:17px 20px 14px;border-bottom:1px solid #eef1f6;">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                    <div>
                        <h6 class="mb-1" style="font-size:16px;font-weight:600;color:#2b3648;">Trabajos del período</h6>
                        <span style="font-size:12.5px;color:#9aa6b8;">
                            @if($abierto) Activá el interruptor para incluir o quitar cada trabajo. El resumen se actualiza al instante. @else El período está cerrado (solo lectura). @endif
                        </span>
                    </div>
                    <div class="position-relative">
                        <i data-feather="search" style="width:15px;position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#a9b3c2;"></i>
                        <input type="text" id="cert-search" class="cert-search" placeholder="Buscar domicilio, LPU o cuadrilla…">
                    </div>
                </div>
                @if($abierto)
                <div class="d-flex gap-2 flex-wrap" id="cert-chips">
                    <button type="button" class="cert-chip active" data-filter="all">Todos (<span class="c-all">0</span>)</button>
                    <button type="button" class="cert-chip" data-filter="in">En certificación (<span class="c-in">0</span>)</button>
                    <button type="button" class="cert-chip" data-filter="out">Sin agregar (<span class="c-out">0</span>)</button>
                </div>
                @endif
            </div>

            <form action="{{ route('admin.trabajos.periodos.seleccion', $periodo->id) }}" method="POST" id="form-seleccion">
                @csrf
                <div class="sel-head">
                    <div class="text-center">Incluir</div><div style="padding:0 8px">Fecha</div><div>Cuadrilla</div>
                    <div style="padding:0 8px">Domicilio</div><div>Poste</div><div>LPU</div><div>Materiales</div>
                </div>

                <div id="sel-body">
                    @forelse($seleccionables as $t)
                        <label class="sel-row {{ $t['incluido'] ? 'on' : '' }}" data-id="{{ $t['id'] }}"
                            data-search="{{ \Illuminate\Support\Str::lower($t['domicilio'].' '.$t['lpu'].' '.$t['cuadrilla']) }}">
                            <div class="text-center">
                                <span class="sw">
                                    <input type="checkbox" class="sel-check" name="incluidos[]" value="{{ $t['id'] }}"
                                        {{ $t['incluido'] ? 'checked' : '' }} {{ $abierto ? '' : 'disabled' }}>
                                    <span class="track"><span class="knob"></span></span>
                                </span>
                            </div>
                            <div style="padding:0 8px">{{ $t['fecha'] }}</div>
                            <div><span class="chip-cuad">{{ $t['cuadrilla'] }}</span></div>
                            <div class="dom" style="padding:0 8px">{{ $t['domicilio'] }}</div>
                            <div><span class="pill-poste" style="background:{{ $t['posteBg'] }}">{{ $t['poste'] }}</span></div>
                            <div class="lpu-code">{{ $t['lpu'] ?? '—' }}</div>
                            <div style="color:#5a6b82">{{ $t['mats']->sum('cantidad') }} u.</div>
                        </label>
                    @empty
                        <div style="padding:38px 16px;text-align:center;color:#9aa6b8;">
                            No hay trabajos {{ $abierto ? 'aprobados disponibles para esta categoría y rango' : 'en este período' }}.
                        </div>
                    @endforelse
                </div>
                <div id="sel-noresult" style="display:none;padding:30px 16px;text-align:center;color:#9aa6b8;">No hay trabajos que coincidan con la búsqueda.</div>
            </form>
        </section>

        {{-- ===== Resumen en vivo ===== --}}
        <aside class="side">
            <div class="cert-card" style="overflow:hidden;">
                <div class="side-total">
                    <span class="lbl">Total certificación</span>
                    <span class="val" id="sum-total">$ 0,00</span>
                </div>
                <div style="padding:16px 20px 18px;">
                    <div style="font-size:14px;font-weight:600;color:#2b3648;margin-bottom:4px;">Certificación (por LPU)</div>
                    <div id="sum-lpu"></div>
                    <div id="sum-lpu-empty" style="padding:14px 0;color:#9aa6b8;font-size:13.5px;text-align:center;">Sin trabajos incluidos.</div>
                </div>
            </div>

            <div class="cert-card" style="overflow:hidden;">
                <div style="padding:14px 20px 10px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:14px;font-weight:600;color:#2b3648;">Consumos (materiales)</span>
                    <span style="font-size:12px;color:#9aa6b8;" id="sum-cons-meta">0 u. · 0 ítems</span>
                </div>
                <div style="max-height:280px;overflow:auto;" id="sum-consumos"></div>
                <div id="sum-cons-empty" style="padding:14px 20px;color:#9aa6b8;font-size:13px;">Sin consumos.</div>
            </div>

            @if($abierto)
            <div class="cert-card cert-actionbar">
                <span style="font-size:13.5px;color:#7a8699;" id="sum-count-text">0 trabajos</span>
                <span id="autosave" class="d-flex align-items-center gap-1" style="font-size:13px;color:#1f7a3d;">
                    <i data-feather="check-circle" style="width:15px;"></i> <span id="autosave-txt">Se guarda automáticamente</span>
                </span>
            </div>
            @endif
        </aside>
    </div>
</div>

{{-- ===== Drawer: datos de la certificación (Excel) ===== --}}
<div class="drawer-ov" id="drawer-ov"></div>
<div class="drawer" id="drawer">
    <div style="padding:20px 24px;border-bottom:1px solid #eef1f6;" class="d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-0" style="font-size:17px;font-weight:600;color:#2b3648;">Datos de la certificación</h6>
            <span style="font-size:12.5px;color:#9aa6b8;">Se usan para armar el archivo Excel</span>
        </div>
        <button type="button" class="btn btn-light btn-sm" id="btn-drawer-close"><i data-feather="x" style="width:17px;"></i></button>
    </div>
    <form action="{{ route('admin.trabajos.periodos.exportar', $periodo->id) }}" method="POST" enctype="multipart/form-data"
        id="form-export" style="flex:1;display:flex;flex-direction:column;overflow:hidden;">
        @csrf
        {{-- categoria: la usa "Guardar datos" (updateMeta) --}}
        <input type="hidden" name="categoria" value="{{ $periodo->categoria->value }}">
        <div style="padding:22px 24px;overflow:auto;flex:1;">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Obra / Tarea</label><input type="text" name="obra" class="form-control" value="{{ old('obra', $periodo->obra) }}"></div>
                <div class="col-6"><label class="form-label">PEP / OC</label><input type="text" name="pep" class="form-control" value="{{ old('pep', $periodo->pep) }}"></div>
                <div class="col-6"><label class="form-label">Certif. N°</label><input type="text" name="certif_numero" class="form-control" value="{{ old('certif_numero', $periodo->certif_numero) }}"></div>
                <div class="col-12"><label class="form-label">Descripción</label><input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $periodo->descripcion) }}"></div>
                <div class="col-6"><label class="form-label">Supervisión TECO</label><input type="text" name="supervisor_teco" class="form-control" value="{{ old('supervisor_teco', $periodo->supervisor_teco) }}"></div>
                <div class="col-6"><label class="form-label">Contratista</label><input type="text" name="contratista" class="form-control" value="{{ old('contratista', $periodo->contratista) }}"></div>
                <div class="col-6"><label class="form-label">Inicio de obra</label><input type="date" name="fecha_inicio_obra" class="form-control" value="{{ old('fecha_inicio_obra', $periodo->fecha_inicio_obra?->format('Y-m-d')) }}"></div>
                <div class="col-6"><label class="form-label">Fin de obra</label><input type="date" name="fecha_fin_obra" class="form-control" value="{{ old('fecha_fin_obra', $periodo->fecha_fin_obra?->format('Y-m-d')) }}"></div>

                <div class="col-12"><hr class="my-1"></div>
                <div class="col-12">
                    <label class="form-label">Plantilla de Telecom (.xlsx) <span class="text-danger">*</span></label>
                    <input type="file" name="archivo" id="export-archivo" class="form-control" accept=".xlsx,.xls" required>
                    <small class="text-muted d-block mt-1">Subí la plantilla con los precios de LPU/materiales actualizados. El sistema llena la hoja DETALLE y descarga el archivo.</small>
                    <div id="export-nofile" class="text-danger small mt-1" style="display:none;">Subí la plantilla de Telecom para generar el Excel.</div>
                </div>
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid #eef1f6;" class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-light" id="btn-guardar-datos">Guardar datos</button>
            <button type="submit" class="btn btn-success">Generar Excel</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    if (window.feather) { feather.replace(); }
(function () {
    var DATA = {};
    (@json($seleccionables)).forEach(function (t) { DATA[t.id] = t; });

    var body    = document.getElementById('sel-body');
    var rows    = Array.prototype.slice.call(document.querySelectorAll('.sel-row'));
    var checks  = Array.prototype.slice.call(document.querySelectorAll('.sel-check'));
    var search  = document.getElementById('cert-search');
    var chipsBox = document.getElementById('cert-chips');
    var filtro  = 'all';

    function money(n) {
        return '$ ' + Number(n).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recompute() {
        var incluidos = checks.filter(function (c) { return c.checked; }).map(function (c) { return +c.value; });
        var byLpu = {}, total = 0, mats = {};

        incluidos.forEach(function (id) {
            var t = DATA[id]; if (!t) return;
            if (t.lpu) {
                if (!byLpu[t.lpu]) byLpu[t.lpu] = { lpu: t.lpu, desc: t.lpuDesc, precio: t.precio, cant: 0 };
                byLpu[t.lpu].cant++;
            }
            (t.mats || []).forEach(function (m) {
                if (!mats[m.codigo]) mats[m.codigo] = { codigo: m.codigo, nombre: m.nombre, cant: 0 };
                mats[m.codigo].cant += m.cantidad;
            });
        });

        // LPU
        var lpuHtml = '';
        Object.keys(byLpu).forEach(function (k) {
            var g = byLpu[k], sub = g.cant * g.precio; total += sub;
            lpuHtml += '<div class="lpu-row"><div style="min-width:0">'
                + '<div style="font-size:13.5px;font-weight:600;color:#2b3247;font-variant-numeric:tabular-nums">' + g.lpu + '</div>'
                + '<div style="font-size:12px;color:#8a97ab;line-height:1.3;margin-top:2px">' + (g.desc || '') + '</div>'
                + '<div style="font-size:12px;color:#9aa6b8;margin-top:3px">' + g.cant + ' × ' + money(g.precio) + '</div>'
                + '</div><div class="sub">' + money(sub) + '</div></div>';
        });
        document.getElementById('sum-lpu').innerHTML = lpuHtml;
        document.getElementById('sum-lpu-empty').style.display = lpuHtml ? 'none' : '';
        document.getElementById('sum-total').textContent = money(total);

        // Consumos
        var consList = Object.keys(mats).map(function (k) { return mats[k]; });
        var consHtml = '', units = 0;
        consList.forEach(function (m) {
            units += m.cant;
            consHtml += '<div class="cons-row"><div style="min-width:0">'
                + '<span style="font-size:12px;color:#9aa6b8;font-variant-numeric:tabular-nums">' + (m.codigo || '') + '</span>'
                + '<div style="font-size:12.5px;color:#3a4658;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + (m.nombre || '') + '</div>'
                + '</div><span style="font-size:13.5px;font-weight:600;color:#2b3247;font-variant-numeric:tabular-nums">' + (Math.round(m.cant * 100) / 100) + '</span></div>';
        });
        document.getElementById('sum-consumos').innerHTML = consHtml;
        document.getElementById('sum-cons-empty').style.display = consHtml ? 'none' : '';
        document.getElementById('sum-cons-meta').textContent = (Math.round(units * 100) / 100) + ' u. · ' + consList.length + ' ítems';

        // Counters
        var inCount = incluidos.length, allCount = checks.length, outCount = allCount - inCount;
        var setTxt = function (sel, v) { var el = document.querySelector(sel); if (el) el.textContent = v; };
        setTxt('.c-all', allCount); setTxt('.c-in', inCount); setTxt('.c-out', outCount);
        var ct = document.getElementById('sum-count-text');
        if (ct) ct.textContent = inCount + (inCount === 1 ? ' trabajo' : ' trabajos') + ' · ' + consList.length + ' materiales';
    }

    // --- Auto-guardado (sin botón): persiste la selección al togglear ---
    var seleccionUrl = "{{ route('admin.trabajos.periodos.seleccion', $periodo->id) }}";
    var token = document.querySelector('#form-seleccion input[name="_token"]')?.value || '';
    var saveTimer = null;
    function setAutosave(txt, color) {
        var box = document.getElementById('autosave'); var t = document.getElementById('autosave-txt');
        if (t) t.textContent = txt; if (box) box.style.color = color || '#1f7a3d';
    }
    function autosave() {
        setAutosave('Guardando…', '#8a97ab');
        var fd = new FormData();
        fd.append('_token', token);
        checks.filter(function (c) { return c.checked; }).forEach(function (c) { fd.append('incluidos[]', c.value); });
        fetch(seleccionUrl, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
            .then(function (r) { if (!r.ok) throw 0; return r.json(); })
            .then(function () { setAutosave('Cambios guardados', '#1f7a3d'); })
            .catch(function () { setAutosave('Error al guardar — reintentá', '#e05a45'); });
    }
    function scheduleSave() { clearTimeout(saveTimer); saveTimer = setTimeout(autosave, 500); }

    // Marcar fila on/off + recomputar + auto-guardar
    checks.forEach(function (c) {
        c.addEventListener('change', function () {
            var row = c.closest('.sel-row');
            if (row) row.classList.toggle('on', c.checked);
            applyFilter();
            recompute();
            scheduleSave();
        });
    });

    // Filtro (chips + búsqueda)
    function applyFilter() {
        var q = (search.value || '').trim().toLowerCase();
        var visibles = 0;
        rows.forEach(function (row) {
            var chk = row.querySelector('.sel-check');
            var on = chk && chk.checked;
            var okChip = filtro === 'all' || (filtro === 'in' && on) || (filtro === 'out' && !on);
            var okSearch = !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
            var show = okChip && okSearch;
            row.style.display = show ? '' : 'none';
            if (show) visibles++;
        });
        var nr = document.getElementById('sel-noresult');
        if (nr) nr.style.display = (visibles === 0 && rows.length) ? '' : 'none';
    }

    if (search) search.addEventListener('input', applyFilter);
    if (chipsBox) chipsBox.addEventListener('click', function (e) {
        var btn = e.target.closest('.cert-chip'); if (!btn) return;
        filtro = btn.getAttribute('data-filter');
        chipsBox.querySelectorAll('.cert-chip').forEach(function (c) { c.classList.toggle('active', c === btn); });
        applyFilter();
    });

    // Drawer (se abre desde "Generar Excel")
    var ov = document.getElementById('drawer-ov'), dw = document.getElementById('drawer');
    function openD() { ov.classList.add('open'); dw.classList.add('open'); }
    function closeD() { ov.classList.remove('open'); dw.classList.remove('open'); }
    ['btn-drawer'].forEach(function (id){ var b=document.getElementById(id); if(b) b.addEventListener('click', openD); });
    ['btn-drawer-close'].forEach(function (id){ var b=document.getElementById(id); if(b) b.addEventListener('click', closeD); });
    if (ov) ov.addEventListener('click', closeD);

    // Generar Excel: exigir plantilla
    var formExport = document.getElementById('form-export');
    var fileInput = document.getElementById('export-archivo');
    if (formExport) formExport.addEventListener('submit', function (e) {
        if (!fileInput || !fileInput.files.length) {
            e.preventDefault();
            var nf = document.getElementById('export-nofile'); if (nf) nf.style.display = '';
        }
    });

    // Guardar datos (sin generar): AJAX a updateMeta
    var btnGuardar = document.getElementById('btn-guardar-datos');
    var metaUrl = "{{ route('admin.trabajos.periodos.updateMeta', $periodo->id) }}";
    if (btnGuardar && formExport) btnGuardar.addEventListener('click', function () {
        var campos = ['obra','pep','certif_numero','descripcion','supervisor_teco','contratista','fecha_inicio_obra','fecha_fin_obra','categoria'];
        var fd = new FormData();
        fd.append('_token', token);
        fd.append('_method', 'PUT');
        campos.forEach(function (name) {
            var el = formExport.querySelector('[name="' + name + '"]');
            if (el) fd.append(name, el.value);
        });
        btnGuardar.disabled = true; btnGuardar.textContent = 'Guardando…';
        fetch(metaUrl, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                btnGuardar.disabled = false; btnGuardar.textContent = 'Guardar datos';
                if (res.ok && res.d.ok) { btnGuardar.textContent = 'Guardado ✓'; setTimeout(function(){ btnGuardar.textContent = 'Guardar datos'; }, 1500); }
                else { alert(res.d.error || 'No se pudieron guardar los datos.'); }
            })
            .catch(function () { btnGuardar.disabled = false; btnGuardar.textContent = 'Guardar datos'; alert('Error al guardar los datos.'); });
    });

    recompute();
    applyFilter();
})();
</script>
@endsection
