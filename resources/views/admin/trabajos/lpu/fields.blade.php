<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Código (S4)<span> *</span></label>
                <input class="form-control" type="text" name="codigo_lpu"
                    value="{{ isset($lpu->codigo_lpu) ? $lpu->codigo_lpu : old('codigo_lpu') }}"
                    placeholder="Ej: 5020448">
                @error('codigo_lpu')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Código Telecom</label>
                <input class="form-control" type="text" name="codigo_telecom"
                    value="{{ isset($lpu->codigo_telecom) ? $lpu->codigo_telecom : old('codigo_telecom') }}"
                    placeholder="Ej: 993000002">
                @error('codigo_telecom')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="mb-3">
                <label>Descripción<span> *</span></label>
                <input class="form-control" type="text" name="descripcion"
                    value="{{ isset($lpu->descripcion) ? $lpu->descripcion : old('descripcion') }}"
                    placeholder="Descripción del tipo de trabajo">
                @error('descripcion')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Unidad<span> *</span></label>
                <input class="form-control" type="text" name="unidad"
                    value="{{ isset($lpu->unidad) ? $lpu->unidad : old('unidad', 'UN') }}"
                    placeholder="Ej: UN, M">
                @error('unidad')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>$ Mantenimiento<span> *</span></label>
                <input class="form-control" type="number" step="0.0001" min="0" name="precio_mantenimiento"
                    value="{{ isset($lpu->precio_mantenimiento) ? $lpu->precio_mantenimiento : old('precio_mantenimiento', '0') }}"
                    placeholder="0.0000">
                @error('precio_mantenimiento')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>$ Obras<span> *</span></label>
                <input class="form-control" type="number" step="0.0001" min="0" name="precio_obras"
                    value="{{ isset($lpu->precio_obras) ? $lpu->precio_obras : old('precio_obras', '0') }}"
                    placeholder="0.0000">
                @error('precio_obras')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Vigencia desde</label>
                <input class="form-control datepicker-here" type="text" name="vigencia_desde"
                    value="{{ isset($lpu->vigencia_desde) ? $lpu->vigencia_desde->format('Y-m-d') : old('vigencia_desde') }}"
                    placeholder="YYYY-MM-DD" data-language="es">
                @error('vigencia_desde')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="text-end">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>
