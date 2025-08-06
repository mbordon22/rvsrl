<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Nombre<span> *</span></label>
                <input class="form-control" type="text" name="nombre"
                    value="{{ isset($proveedor->nombre) ? $proveedor->nombre : old('nombre') }}"
                    placeholder="Ingrese el nombre del proveedor">
                @error('nombre')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Código</label>
                <input class="form-control" type="text" name="codigo"
                    value="{{ isset($proveedor->codigo) ? $proveedor->codigo : old('codigo') }}"
                    placeholder="Ingrese el codigo del proveedor">
                @error('codigo')
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
                <label>Tipo Documento<span> *</span></label>
                @php
                    use App\Enums\TipoDocumentoContable;
                    $selectedTipo = old('tipo_documento', $proveedor->tipo_documento ?? null);
                @endphp
                <select class="form-select" name="tipo_documento">
                    @foreach (TipoDocumentoContable::cases() as $tipo)
                        <option value="{{ $tipo->name }}"
                            @selected($selectedTipo === $tipo->name)>
                            {{ $tipo->label() }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_documento')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Número de Documento<span> *</span></label>
                <input class="form-control" type="text" name="numero_documento"
                    value="{{ isset($proveedor->numero_documento) ? $proveedor->numero_documento : old('numero_documento') }}" 
                    placeholder="Ingrese el número de documento" >
                @error('numero_documento')
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
                <label>Condición Frente al IVA<span> *</span></label>
                <select class="form-select" name="condicion_iva">
                    @php
                        use App\Enums\CondicionIva;
                        $selected = old('condicion_iva', $provedoor->condicion_iva ?? null);
                    @endphp
                    @foreach (CondicionIva::cases() as $condicion)
                        <option value="{{ $condicion->name }}"
                            @selected($selected === $condicion->name)>
                            {{ $condicion->label() }}
                        </option>
                    @endforeach
                </select>
                @error('condicion_iva')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Email</label>
                <input class="form-control" type="email" name="email"
                    value="{{ isset($proveedor->email) ? $proveedor->email : old('email') }}" 
                    placeholder="Ingrese el email" >
                @error('email')
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
                <label>Telefono</label>
                <input class="form-control" type="phone" name="telefono"
                    value="{{ isset($proveedor->telefono) ? $proveedor->telefono : old('telefono') }}" 
                    placeholder="Ingrese el telefono" >
                @error('telefono')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Domicilio</label>
                <input class="form-control" type="text" name="direccion"
                    value="{{ isset($proveedor->direccion) ? $proveedor->direccion : old('direccion') }}" 
                    placeholder="Ingrese el domicilio" >
                @error('direccion')
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
                <label>Provincia</label>
                <select class="form-select" name="state_id">
                    @foreach ($provincias as $provincia)    
                        <option value="{{ $provincia->id }}"
                            @if (isset($provedoor->state_id)) @selected(old('state_id', $provedoor->state_id) == $provincia->id) @else @selected($provincia->id == 231) @endif>{{ $provincia->name }}</option>
                        </option>
                    @endforeach
                </select>
                @error('state_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Localidad</label>
                <input class="form-control" type="text" name="localidad"
                    value="{{ isset($proveedor->localidad) ? $proveedor->localidad : old('localidad') }}" 
                    placeholder="Ingrese la localidad" >
                @error('localidad')
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
                <label>Codigo Postal</label>
                <input class="form-control" type="text" name="codigo_postal"
                    value="{{ isset($proveedor->codigo_postal) ? $proveedor->codigo_postal : old('codigo_postal') }}" 
                    placeholder="Ingrese el codigo postal">
                @error('codigo_postal')
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
                <textarea class="form-control" id="exampleFormControlTextarea4" rows="2" name="observaciones"
                    placeholder="Mas información">{{ isset($vehiculo->observaciones) ? $vehiculo->observaciones : old('observaciones') }}</textarea>
                @error('observaciones')
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
