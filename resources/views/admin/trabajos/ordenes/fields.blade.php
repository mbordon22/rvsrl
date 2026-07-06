@php
    $t = $trabajo ?? null;

    // Valor actual de un campo (maneja create/edit + enums)
    $v = function (string $f, $default = '') use ($t) {
        $cur = $t?->{$f};
        if ($cur instanceof \BackedEnum) $cur = $cur->value;
        return old($f, $cur ?? $default);
    };
    $chk = fn (string $f) => old($f, $t?->{$f}) ? 'checked' : '';
    $fecha = old('fecha', $t?->fecha?->format('Y-m-d') ?? \Carbon\Carbon::now()->format('Y-m-d'));
    $selEmp = $empleadosSeleccionados ?? ($empleados->pluck('id')->all() ?? []);
@endphp

<style>
    .trabajo-form .card-header { background:#eef1f6 !important; border-bottom:1px solid #dde2ea; }

    /* Inputs con borde marcado y fondo apenas gris para que se noten
       (usamos !important + selectores por tipo para ganarle al estilo del theme) */
    .trabajo-form input.form-control,
    .trabajo-form select.form-select,
    .trabajo-form textarea.form-control,
    .trabajo-form input[type="text"],
    .trabajo-form input[type="number"],
    .trabajo-form input[type="date"],
    .trabajo-form input[type="file"] {
        border:1.6px solid #97a2b2 !important;
        background-color:#f7f9fc !important;
        border-radius:8px !important;
    }
    .trabajo-form .form-control::placeholder { color:#9aa4b2 !important; }
    .trabajo-form input.form-control:focus,
    .trabajo-form select.form-select:focus,
    .trabajo-form textarea.form-control:focus {
        border-color:#3c82ff !important;
        background-color:#fff !important;
        box-shadow:0 0 0 .18rem rgba(60,130,255,.18);
    }

    /* Cada pregunta como bloque con contraste */
    .pregunta { background:#e9edf3; border:1px solid #d3dae4; border-left:4px solid #b7c1cf; border-radius:10px; padding:.8rem 1rem; margin-bottom:.6rem; transition:background .15s, border-color .15s; }
    .pregunta.activa { background:#e5f6ea; border-color:#8fd3a4; border-left-color:#28a745; }
    .pregunta > label.pregunta-titulo { font-weight:600; margin-bottom:0; color:#2b3648; }
    .pregunta .sub { background:#fff; border:1px dashed #b7c1cf; border-radius:8px; padding:.6rem .75rem; margin-top:.6rem; }

    /* Checkboxes de empleados con borde visible */
    .trabajo-form .form-check-input { border:1.6px solid #97a2b2; }

    /* Switches más grandes y visibles */
    .trabajo-form .form-switch .form-check-input { width:2.6em; height:1.35em; cursor:pointer; border-color:#97a2b2; }
    .trabajo-form .form-switch .form-check-input:checked { background-color:#28a745; border-color:#28a745; }
</style>

<div class="form theme-form trabajo-form">

    {{-- ===== 1. DATOS GENERALES ===== --}}
    <div class="card shadow-none border mb-3">
        <div class="card-header py-2"><h6 class="mb-0">Datos generales</h6></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-sm-4">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-lg" name="fecha" value="{{ $fecha }}" required>
                    @error('fecha') <span class="text-danger d-block">{{ $message }}</span> @enderror
                </div>

                @if($esAdmin)
                <div class="col-12 col-sm-4">
                    <label class="form-label">Cuadrilla <span class="text-danger">*</span></label>
                    <select name="cuadrilla_id" class="form-select form-select-lg">
                        <option value="">Seleccione…</option>
                        @foreach($cuadrillas as $c)
                            <option value="{{ $c->id }}" {{ (string)$v('cuadrilla_id', optional($cuadrilla)->id) === (string)$c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="col-12 col-sm-4">
                    <label class="form-label">Cuadrilla</label>
                    <input type="text" class="form-control form-control-lg" value="{{ optional($cuadrilla)->nombre }}" readonly>
                </div>
                @endif

                <div class="col-12 col-sm-4">
                    <label class="form-label">Vehículo</label>
                    <select name="vehiculo_id" class="form-select form-select-lg">
                        <option value="">Seleccione…</option>
                        @foreach($vehiculos as $veh)
                            <option value="{{ $veh->id }}" {{ (string)$v('vehiculo_id') === (string)$veh->id ? 'selected' : '' }}>
                                {{ $veh->patente }} — {{ $veh->marca }} {{ $veh->modelo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Domicilio</label>
                    <input type="text" class="form-control form-control-lg" name="domicilio" value="{{ $v('domicilio') }}" placeholder="Dirección del poste">
                </div>
            </div>
        </div>
    </div>

    {{-- ===== 2. EMPLEADOS ===== --}}
    <div class="card shadow-none border mb-3">
        <div class="card-header py-2"><h6 class="mb-0">Empleados de la cuadrilla</h6></div>
        <div class="card-body" id="empleados-container">
            @forelse($empleados as $emp)
                <div class="form-check form-check-inline mb-2">
                    <input class="form-check-input" type="checkbox" name="empleados[]" id="emp{{ $emp->id }}"
                        value="{{ $emp->id }}" {{ in_array($emp->id, $selEmp) ? 'checked' : '' }}>
                    <label class="form-check-label" for="emp{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</label>
                </div>
            @empty
                <p class="text-muted mb-0" id="empleados-placeholder">
                    {{ $esAdmin ? 'Seleccioná una cuadrilla para ver sus empleados.' : 'La cuadrilla no tiene empleados asignados.' }}
                </p>
            @endforelse
        </div>
    </div>

    {{-- ===== 3. INFRAESTRUCTURA ===== --}}
    <div class="card shadow-none border mb-3">
        <div class="card-header py-2"><h6 class="mb-0">Infraestructura</h6></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-sm-4">
                    <label class="form-label">Central</label>
                    <select name="central" class="form-select form-select-lg" data-toggle-value="CYO" data-toggle-target="#grp-central-aclarar">
                        <option value="">—</option>
                        @foreach($centrales as $val => $lab)
                            <option value="{{ $val }}" {{ $v('central') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-4 conditional" id="grp-central-aclarar">
                    <label class="form-label">Aclarar central</label>
                    <input type="text" class="form-control form-control-lg" name="central_aclarar" value="{{ $v('central_aclarar') }}">
                </div>
                <div class="col-12 col-sm-4">
                    <label class="form-label">Armario</label>
                    <input type="text" class="form-control form-control-lg" name="armario" value="{{ $v('armario') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TRABAJO REALIZADO (preguntas en orden) ===== --}}
    <div class="card shadow-none border mb-3">
        <div class="card-header py-2"><h6 class="mb-0">Trabajo realizado</h6></div>
        <div class="card-body">

            {{-- Tipo de poste (define la certificación) --}}
            <div class="pregunta">
                <label class="pregunta-titulo d-block mb-2">Tipo de poste</label>
                <select name="tipo_poste" class="form-select form-select-lg pregunta-control">
                    <option value="">Seleccione…</option>
                    @foreach($tiposPoste as $val => $lab)
                        <option value="{{ $val }}" {{ $v('tipo_poste') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 1. Desmontó poste --}}
            <div class="pregunta">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="pregunta-titulo" for="desmonto_poste">1. ¿Se desmontó poste?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input toggle-poste pregunta-control" type="checkbox" name="desmonto_poste" value="1" id="desmonto_poste" {{ $chk('desmonto_poste') }}>
                    </div>
                </div>
            </div>

            {{-- 2. Colocó poste --}}
            <div class="pregunta">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="pregunta-titulo" for="coloco_poste">2. ¿Se colocó poste?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input toggle-poste pregunta-control" type="checkbox" name="coloco_poste" value="1" id="coloco_poste" {{ $chk('coloco_poste') }}>
                    </div>
                </div>
            </div>

            {{-- Datos del poste (tamaño + material) — visibles si desmontó O colocó --}}
            <div class="pregunta conditional" id="grp-datos-poste">
                <label class="pregunta-titulo d-block mb-2">Datos del poste</label>
                <div class="row g-2">
                    <div class="col-12 col-sm-4">
                        <label class="form-label">Tamaño</label>
                        <select name="tamano_poste" class="form-select">
                            <option value="">—</option>
                            @foreach($tamanosPoste as $val => $lab)
                                <option value="{{ $val }}" {{ $v('tamano_poste') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label">Material</label>
                        <select name="poste_material" id="poste_material" class="form-select">
                            <option value="">—</option>
                            @foreach($materialesPoste as $val => $lab)
                                <option value="{{ $val }}" {{ $v('poste_material') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-4 conditional" id="grp-reutilizado">
                        <label class="form-label">¿Qué material se reutilizó?</label>
                        <select name="poste_reutilizado_material" class="form-select">
                            <option value="">—</option>
                            @foreach($materialesReutilizado as $val => $lab)
                                <option value="{{ $val }}" {{ $v('poste_reutilizado_material') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. CDO / Caja Terminal / NAP (elegir uno + cantidad) --}}
            <div class="pregunta">
                <label class="pregunta-titulo d-block mb-2">3. CDO / Caja Terminal / NAP</label>
                <select name="elemento_tipo" id="elemento_tipo" class="form-select pregunta-control">
                    <option value="">— Ninguno</option>
                    @foreach($elementosRed as $val => $lab)
                        <option value="{{ $val }}" {{ $v('elemento_tipo') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
                <div class="conditional sub" id="grp-elemento-cantidad">
                    <label class="form-label">Cantidad</label>
                    <input type="number" min="0" class="form-control" name="elemento_cantidad" value="{{ $v('elemento_cantidad') }}">
                </div>
            </div>

            {{-- 4. Sifón --}}
            <div class="pregunta">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="pregunta-titulo" for="sifon">4. ¿Tiene sifón?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input toggle-sifon pregunta-control" type="checkbox" name="sifon" value="1" id="sifon" {{ $chk('sifon') }}>
                    </div>
                </div>
                <div class="sub conditional" id="grp-sifon-cables">
                    <label class="form-label">Cantidad de cables</label>
                    <input type="number" min="0" class="form-control" name="sifon_cables" value="{{ $v('sifon_cables') }}">
                </div>
                <div class="sub conditional" id="grp-sifon-protecciones">
                    <label class="form-label">Cantidad de protecciones</label>
                    <input type="number" min="0" class="form-control" name="protecciones_cantidad" value="{{ $v('protecciones_cantidad') }}">
                </div>
            </div>

            {{-- 5. Rienda --}}
            <div class="pregunta">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="pregunta-titulo" for="rienda">5. ¿Tiene rienda?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input toggle pregunta-control" type="checkbox" name="rienda" value="1" id="rienda" data-toggle-target="#grp-rienda" {{ $chk('rienda') }}>
                    </div>
                </div>
                <div class="conditional sub" id="grp-rienda">
                    <label class="form-label">Tipo de rienda</label>
                    <select name="rienda_tipo" class="form-select">
                        <option value="">—</option>
                        @foreach($tiposRienda as $val => $lab)
                            <option value="{{ $val }}" {{ $v('rienda_tipo') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 6. Tipo de suelo (+ reparación de vereda) --}}
            <div class="pregunta">
                <label class="pregunta-titulo d-block mb-2" for="tipo_suelo">6. Tipo de suelo</label>
                <select name="tipo_suelo" id="tipo_suelo" class="form-select form-select-lg pregunta-control">
                    <option value="">Seleccione…</option>
                    @foreach($tiposSuelo as $val => $lab)
                        <option value="{{ $val }}" {{ $v('tipo_suelo') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
                <div class="conditional sub d-flex justify-content-between align-items-center" id="grp-rep-vereda">
                    <label class="form-label mb-0" for="rep_vereda">¿Se realizó reparación de vereda?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="rep_vereda" value="1" id="rep_vereda" {{ $chk('rep_vereda') }}>
                    </div>
                </div>
            </div>

            {{-- 7. Poda --}}
            <div class="pregunta">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="pregunta-titulo" for="poda">7. ¿Se realizó poda?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input pregunta-control" type="checkbox" name="poda" value="1" id="poda" {{ $chk('poda') }}>
                    </div>
                </div>
            </div>

            {{-- 8. Retensó cable o suspensor --}}
            <div class="pregunta">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="pregunta-titulo" for="retensado">8. ¿Se retensó cable o suspensor?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input pregunta-control" type="checkbox" name="retensado" value="1" id="retensado" {{ $chk('retensado') }}>
                    </div>
                </div>
            </div>

            {{-- 9. Cable de bajada --}}
            <div class="pregunta">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="pregunta-titulo" for="bajadas">9. ¿Se utilizó cable de bajada?</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input toggle pregunta-control" type="checkbox" name="bajadas" value="1" id="bajadas" data-toggle-target="#grp-bajadas" {{ $chk('bajadas') }}>
                    </div>
                </div>
                <div class="conditional sub" id="grp-bajadas">
                    <label class="form-label">Cantidad de bajadas</label>
                    <input type="number" min="0" class="form-control" name="bajadas_cantidad" value="{{ $v('bajadas_cantidad') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FOTOS ===== --}}
    <div class="card shadow-none border mb-3">
        <div class="card-header py-2"><h6 class="mb-0">Fotos</h6></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label">Fotos ANTES</label>
                    <input type="file" class="form-control form-control-lg foto-input" name="fotos_antes[]"
                        accept="image/*" capture="environment" multiple data-preview="#preview-antes">
                    <div id="preview-antes" class="d-flex flex-wrap gap-2 mt-2"></div>
                    @if($t)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($t->getMedia('fotos_antes') as $m)
                                <div class="position-relative">
                                    <img src="{{ $m->getUrl() }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
                                    <a href="{{ route('admin.trabajos.ordenes.removeFoto', $m->id) }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1" onclick="return confirm('¿Eliminar esta foto?')">×</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label">Fotos DESPUÉS</label>
                    <input type="file" class="form-control form-control-lg foto-input" name="fotos_despues[]"
                        accept="image/*" capture="environment" multiple data-preview="#preview-despues">
                    <div id="preview-despues" class="d-flex flex-wrap gap-2 mt-2"></div>
                    @if($t)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($t->getMedia('fotos_despues') as $m)
                                <div class="position-relative">
                                    <img src="{{ $m->getUrl() }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
                                    <a href="{{ route('admin.trabajos.ordenes.removeFoto', $m->id) }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1" onclick="return confirm('¿Eliminar esta foto?')">×</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== OBSERVACIONES ===== --}}
    <div class="card shadow-none border mb-3">
        <div class="card-body">
            <label class="form-label">Observaciones</label>
            <textarea class="form-control" name="observaciones" rows="3">{{ $v('observaciones') }}</textarea>
        </div>
    </div>

    <div class="text-end pb-4">
        <button type="submit" class="btn btn-success btn-lg w-100 w-sm-auto">Guardar trabajo</button>
    </div>
</div>
