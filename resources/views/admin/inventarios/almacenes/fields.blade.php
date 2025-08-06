<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Nombre<span> *</span></label>
                <input class="form-control" type="text" name="nombre"
                    value="{{ isset($almacen->nombre) ? $almacen->nombre : old('nombre') }}"
                    placeholder="Ingrese la nombre del almacen">
                @error('nombre')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Descripción<span> *</span></label>
                <input class="form-control" type="text" name="descripcion"
                    value="{{ isset($almacen->descripcion) ? $almacen->descripcion : old('descripcion') }}"
                    placeholder="Ingrese el descripción del almacen">
                @error('descripcion')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Ubicación</label>
                <input class="form-control" type="text" name="ubicacion"
                    value="{{ isset($almacen->ubicacion) ? $almacen->ubicacion : old('ubicacion') }}"
                    placeholder="Ingrese el ubicación del almacen">
                @error('ubicacion')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="text-end">
                <button type="submit" class="btn btn-success">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
