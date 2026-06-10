<div class="form theme-form w-100">

    {{-- ===================== Datos de la cuadrilla ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Datos de la cuadrilla</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="nombre">Nombre/Código <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="text" id="nombre" name="nombre"
                    value="{{ isset($cuadrilla->nombre) ? $cuadrilla->nombre : old('nombre') }}"
                    placeholder="Ingrese el nombre/código de la cuadrilla">
                @error('nombre')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Integrantes ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Integrantes de la cuadrilla <span class="text-white">*</span></h6>
    </div>
    <div class="row" id="empleadoPicker">
        <div class="col-lg-6 mb-3">
            <label for="empleadoSearch">Agregar integrante</label>
            <div class="position-relative">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i data-feather="search"></i></span>
                    <input type="text" id="empleadoSearch" class="form-control bg-light"
                        placeholder="Buscar usuario por nombre..." autocomplete="off">
                </div>
                <div id="empleadoSuggestions" class="list-group position-absolute w-100 shadow-sm rounded"
                    style="z-index:1050; max-height:280px; overflow-y:auto; display:none;"></div>
            </div>
            <small class="text-muted">Escribí el nombre y seleccioná para sumarlo a la cuadrilla.</small>
            @error('empleados')
                <div class="text-danger mt-1"><strong>{{ $message }}</strong></div>
            @enderror
        </div>

        <div class="col-lg-6 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="mb-0 fw-semibold">Integrantes seleccionados</label>
                <span class="badge bg-primary" id="empleadoCount">0</span>
            </div>
            <div id="empleadoList" class="border rounded bg-light p-2" style="min-height:90px;">
                <div id="empleadoEmpty" class="text-muted text-center py-4">
                    <i data-feather="users"></i>
                    <div class="mt-1">Aún no agregaste integrantes</div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.inventarios.cuadrillas.member_picker', [
        'usuarios' => $usuarios,
        'empleadosSeleccionados' => old('empleados', $empleadosSeleccionados ?? []),
    ])

    {{-- ===================== Acciones ===================== --}}
    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.inventarios.cuadrillas.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
