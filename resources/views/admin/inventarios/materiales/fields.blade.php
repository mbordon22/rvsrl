<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Código<span> *</span></label>
                <input class="form-control" type="text" name="codigo"
                    value="{{ isset($material->codigo) ? $material->codigo : old('codigo') }}"
                    placeholder="Ingrese la codigo del material">
                @error('codigo')
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
                    value="{{ isset($material->descripcion) ? $material->descripcion : old('descripcion') }}"
                    placeholder="Ingrese el descripción del material">
                @error('descripcion')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="mb-3">
                <label>Descripción larga</label>
                <textarea class="form-control" id="exampleFormControlTextarea4" rows="2" name="descripcion_larga"
                    placeholder="Ingrese la descripción larga del material">{{ isset($material->descripcion_larga) ? $material->descripcion_larga : old('descripcion_larga') }}</textarea>
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
                <label>Unidad Medida</label>
                <select class="form-select" name="unidad_medida">
                    <option value="">Seleccione una unidad de medida</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->name }}" {{ (isset($material) && $material->unidad_medida === $unidad->name) ? 'selected' : '' }}>
                            {{ $unidad->label() }}
                        </option>
                    @endforeach
                </select>
                @error('talle')
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
