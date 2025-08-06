<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Producto<span> *</span></label>
                <input class="form-control" type="text" name="producto"
                    value="{{ isset($epp->producto) ? $epp->producto : old('producto') }}"
                    placeholder="Ingrese la producto del EPP">
                @error('producto')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Tipo/Modelo<span> *</span></label>
                <input class="form-control" type="text" name="tipo"
                    value="{{ isset($epp->tipo) ? $epp->tipo : old('tipo') }}"
                    placeholder="Ingrese el tipo/modelo del EPP">
                @error('tipo')
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
                <label>Marca</label>
                <input class="form-control" type="text" name="marca"
                    value="{{ isset($epp->marca) ? $epp->marca : old('marca') }}"
                    placeholder="Ingrese el marca del EPP">
                @error('marca')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Posee Certificación</label>
                <select class="form-select" name="certificacion">
                    <option value="1" @if (isset($epp->certificacion)) @selected(old('certificacion', $epp->certificacion) == 1) @endif>Si</option>
                    <option value="0" @if (isset($epp->certificacion)) @selected(old('certificacion', $epp->certificacion) == 0) @endif>No</option>
                </select>
                @error('certificacion')
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
                <label>Talle</label>
                <input class="form-control" type="text" name="talle"
                    value="{{ isset($epp->talle) ? $epp->talle : old('talle') }}"
                    placeholder="Ingrese la talle del EPP">
                @error('talle')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Stock<span> *</span></label>
                <input class="form-control" type="number" min="0" name="stock"
                    value="{{ isset($epp->stock) ? $epp->stock : old('stock') }}"
                    placeholder="Ingrese el stock del EPP">
                @error('stock')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Minimo Stock<span> *</span></label>
                <input class="form-control" type="number" min="0" name="min_stock"
                    value="{{ isset($epp->min_stock) ? $epp->min_stock : old('min_stock') }}"
                    placeholder="Ingrese el minimo de stock del EPP">
                @error('min_stock')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label>Mas Información</label>
                <textarea class="form-control" id="exampleFormControlTextarea4" rows="2" name="observacion"
                    placeholder="Mas información">{{ isset($epp->observacion) ? $epp->observacion : old('observacion') }}</textarea>
                @error('observacion')
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
