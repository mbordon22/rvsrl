<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Nombre/Código<span> *</span></label>
                <input class="form-control" type="text" name="nombre"
                    value="{{ isset($cuadrilla->nombre) ? $cuadrilla->nombre : old('nombre') }}"
                    placeholder="Ingrese el nombre/código de la cuadrilla">
                @error('nombre')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-8">
            <div class="mb-3">
                <label>Empleados<span> *</span></label>
                <br>
                <div class="table-responsive table-seconday">
                    {!! $dataTable->table() !!}
                </div>
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
