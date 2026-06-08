@php
    $isEdit = $vehiculo->exists;
@endphp

<div class="form theme-form w-100">

    {{-- ===================== Datos del vehículo ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Datos del vehículo</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="marca">Marca <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="text" id="marca" name="marca"
                    value="{{ isset($vehiculo->marca) ? $vehiculo->marca : old('marca') }}"
                    placeholder="Ingrese la marca del vehículo">
                @error('marca')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="modelo">Modelo <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="text" id="modelo" name="modelo"
                    value="{{ isset($vehiculo->modelo) ? $vehiculo->modelo : old('modelo') }}"
                    placeholder="Ingrese el modelo del vehículo">
                @error('modelo')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="ano">Año de Fabricación <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="number" id="ano" name="ano" min="1950" max="{{ date('Y') }}"
                    value="{{ isset($vehiculo->ano) ? $vehiculo->ano : old('ano') }}"
                    placeholder="Ingrese el año de fabricación">
                @error('ano')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="patente">Patente <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="text" id="patente" name="patente"
                    value="{{ isset($vehiculo->patente) ? $vehiculo->patente : old('patente') }}"
                    placeholder="Ingrese la patente del vehículo">
                @error('patente')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Clasificación ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Clasificación</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="tipo_vehiculo">Tipo de vehículo <span class="text-danger">*</span></label>
                <select class="form-select bg-light" id="tipo_vehiculo" name="tipo_vehiculo">
                    <option value="" selected disabled hidden>Seleccionar tipo de vehículo</option>
                    @foreach ($tipos_vehiculo as $tipo)
                        <option value="{{ $tipo->id }}"
                            @selected(old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == $tipo->id)>{{ $tipo->tipo_vehiculo }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_vehiculo')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="tipo_combustible">Tipo de combustible <span class="text-danger">*</span></label>
                <select class="form-select bg-light" id="tipo_combustible" name="tipo_combustible">
                    <option value="" selected disabled hidden>Seleccionar tipo de combustible</option>
                    @foreach ($tipos_combustible as $tipo)
                        <option value="{{ $tipo->id }}"
                            @selected(old('tipo_combustible', $vehiculo->tipo_combustible ?? '') == $tipo->id)>{{ $tipo->tipo_combustible }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_combustible')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Información adicional ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Información adicional</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="fecha_compra">Fecha de Ingreso a la empresa</label>
                <input class="datepicker-here form-control bg-light" type="text" id="fecha_compra" name="fecha_compra"
                    value="{{ isset($vehiculo->fecha_compra) ? $vehiculo->fecha_compra : old('fecha_compra', now()->format('d/m/Y')) }}"
                    data-language="es" placeholder="Fecha de ingreso del vehículo">
                @error('fecha_compra')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="identificador_vehiculo">Identificador de Vehículo <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="text" id="identificador_vehiculo" name="identificador_vehiculo"
                    value="{{ isset($vehiculo->identificador_vehiculo) ? $vehiculo->identificador_vehiculo : old('identificador_vehiculo') }}"
                    placeholder="Ingrese un código de identificación del vehículo">
                @error('identificador_vehiculo')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
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
                <label for="imagen">Imagen</label>
                <input class="form-control bg-light" type="file" id="imagen" name="imagen" accept="image/*">

                <div class="mt-3 comman-image d-flex align-items-start gap-3 flex-wrap">
                    {{-- Vista previa de la imagen recién seleccionada --}}
                    <img id="imagenPreview" alt="Vista previa" class="img-thumbnail img-fix d-none"
                        style="max-height:120px;max-width:120px;object-fit:cover;">

                    {{-- Imagen actual (solo en edición) --}}
                    @if ($isEdit && $imagen)
                        <div>
                            <img src="{{ $imagen->getUrl() }}" alt="Imagen actual" class="img-thumbnail img-fix"
                                style="max-height:120px;max-width:120px;object-fit:cover;">
                            <div class="dz-preview">
                                <a href="{{ route('admin.vehiculo.removeImage', $vehiculo?->id) }}" class="dz-remove text-danger"
                                    data-bs-target="#tooltipmodal" data-bs-toggle="modal">Eliminar</a>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($isEdit)
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
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="mas_informacion">Más Información</label>
                <textarea class="form-control bg-light" id="mas_informacion" rows="2" name="mas_informacion"
                    placeholder="Más información">{{ isset($vehiculo->mas_informacion) ? $vehiculo->mas_informacion : old('mas_informacion') }}</textarea>
                @error('mas_informacion')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Acciones ===================== --}}
    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.vehiculo.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Vista previa de imagen --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var imagenInput = document.getElementById('imagen');
        var preview = document.getElementById('imagenPreview');
        if (imagenInput && preview) {
            imagenInput.addEventListener('change', function () {
                var file = this.files && this.files[0];
                if (file) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                }
            });
        }
    });
</script>
