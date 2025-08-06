<div class="modal fade" id="addCombustibleModal" tabindex="-1" aria-labelledby="addCombustibleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCombustibleModalLabel">Añadir Nueva Carga de Combustible</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.vehiculo.combustible.store', $vehiculo->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="tipo_combustible" class="form-label">Tipo de Combustible</label>
                        <select name="tipo_combustible" id="tipo_combustible" class="form-control">
                            <option value="">Seleccione un tipo de combustible</option>
                            <option value="Nafta">Nafta</option>
                            <option value="Diesel">Diesel</option>
                            <option value="GNC">GNC</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="litros" class="form-label">Litros</label>
                        <input type="number" name="litros" id="litros" class="form-control" value="" placeholder="Litros" min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto $</label>
                        <input type="number" name="monto" id="monto" class="form-control" value="" placeholder="Monto" min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_carga" class="form-label">Fecha de Carga</label>
                        <input type="text" name="fecha_carga" id="fecha_carga" class="datepicker-here form-control" data-language="es" value="{{date('d/m/Y')}}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Usuario que realizó la carga</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Seleccionar un usuario</option>
                            @foreach ($usuarios as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name . " " . $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Imagen de la factura</label>
                        <input type="file" name="archivo[]" id="archivo" class="form-control" multiple accept="application/pdf, image/jpeg, image/png, image/jpg">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" id="add-combustible-btn" class="btn btn-success">Añadir Registro</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>