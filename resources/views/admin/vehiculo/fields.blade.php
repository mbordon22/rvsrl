<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Marca<span> *</span></label>
                <input class="form-control" type="text" name="marca"
                    value="{{ isset($vehiculo->marca) ? $vehiculo->marca : old('marca') }}"
                    placeholder="Ingrese la marca del vehículo">
                @error('marca')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Modelo<span> *</span></label>
                <input class="form-control" type="text" name="modelo"
                    value="{{ isset($vehiculo->modelo) ? $vehiculo->modelo : old('modelo') }}"
                    placeholder="Ingrese el modelo del vehículo">
                @error('modelo')
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
                <label>Año Fabricación</label>
                <input class="form-control" type="number" name="ano" min="1950" max="{{ date('Y') }}"
                    value="{{ isset($vehiculo->ano) ? $vehiculo->ano : old('ano') }}" placeholder="Ingrese el año de fabricación">
                @error('ano')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Patente</label>
                <input class="form-control" type="text" name="patente"
                    value="{{ isset($vehiculo->patente) ? $vehiculo->patente : old('patente') }}" 
                    placeholder="Ingrese la patente del vehículo" >
                @error('patente')
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
                <label>Tipo vehículo</label>
                <select class="form-select" name="tipo_vehiculo">
                    @foreach ($tipos_vehiculo as $key => $tipo)
                        <option value="{{ $tipo->id }}"
                            @if (isset($vehiculo->tipo_vehiculo)) @selected(old('tipo_vehiculo', $vehiculo->tipo_vehiculo) == $tipo->id) @endif>{{ $tipo->tipo_vehiculo }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Tipo combustible</label>
                <select class="form-select" name="tipo_combustible">
                    @foreach ($tipos_combustible as $key => $tipo)
                        <option value="{{ $tipo->id }}"
                            @if (isset($vehiculo->tipo_combustible)) @selected(old('tipo_combustible', $vehiculo->tipo_combustible) == $tipo->id) @endif>{{ $tipo->tipo_combustible }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Fecha de Ingreso a la empresa</label>
                <input class="datepicker-here form-control" type="text" name="fecha_compra"
                    value="{{ isset($vehiculo->fecha_compra) ? $vehiculo->fecha_compra : old('fecha_compra', now()->format('d/m/Y')) }}" data-language="es"
                    placeholder="Fecha de compra del vehículo" >
                @error('fecha_compra')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Identificador de Vehiculo</label>
                <input class="form-control" type="text" name="identificador_vehiculo"
                    value="{{ isset($vehiculo->identificador_vehiculo) ? $vehiculo->identificador_vehiculo : old('identificador_vehiculo') }}" 
                    placeholder="Ingrese un código de indentificación del vehículo" >
                @error('identificador_vehiculo')
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
                @php
                    $imagen = $vehiculo->getFirstMedia('imagen');
                @endphp
                <label>Imagen</label>
                <input class="form-control" type="file" name="imagen">

                @isset($vehiculo)
                    <div class="mt-3 comman-image">
                        @if ($imagen)
                            <img src="{{ $imagen->getUrl() }}" alt="Imagen" class="img-thumbnail img-fix" height="50%"
                                width="50%">
                            <div class="dz-preview">
                                <a href="{{ route('admin.vehiculo.removeImage', $vehiculo?->id) }}" class="dz-remove text-danger"
                                    data-bs-target="#tooltipmodal" data-bs-toggle="modal">Eliminar</a>
                            </div>
                        @endif
                    </div>

                    <!-- Remove File Confirmation-->
                    <div class="modal fade" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Eliminar</h4>
                                    <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><b>¿Seguro que quieres eliminar?</b></p>
                                    <p>Este elemento se eliminará permanentemente. No se puede deshacer esta acción.</p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Cerrar</button>
                                    @if ($vehiculo->id)
                                        <a href="{{ route('admin.vehiculo.removeImage', $vehiculo->id) }}"
                                            class="btn btn-danger">Eliminar</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label>Mas Información</label>
                <textarea class="form-control" id="exampleFormControlTextarea4" rows="2" name="mas_informacion"
                    placeholder="Mas información">{{ isset($vehiculo->mas_informacion) ? $vehiculo->mas_informacion : old('mas_informacion') }}</textarea>
                @error('mas_informacion')
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
