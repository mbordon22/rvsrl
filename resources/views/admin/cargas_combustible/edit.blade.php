<div class="modal fade" id="editCombustibleModal" tabindex="-1" aria-labelledby="editCombustibleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCombustibleModalLabel">Editar Carga Combustible</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-combustible-form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="vehiculo_id" class="form-label">Seleccione el Vehículo</label>
                        <select name="vehiculo_id" id="edit-vehiculo_id" class="form-control">
                            <option value="">Seleccione el vehículo</option>
                            @foreach ($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->id }}">{{ $vehiculo->identificador_vehiculo . " - " . $vehiculo->marca . " " . $vehiculo->modelo . " - " . $vehiculo->patente }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_combustible" class="form-label">Tipo de Combustible</label>
                        <select name="tipo_combustible" id="edit-tipo_combustible" class="form-control">
                            <option value="">Seleccione un tipo de combustible</option>
                            <option value="Nafta">Nafta</option>
                            <option value="Diesel">Diesel</option>
                            <option value="GNC">GNC</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="litros" class="form-label">Litros</label>
                        <input type="number" name="litros" id="edit-litros" class="form-control" placeholder="Litros" min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto $</label>
                        <input type="number" name="monto" id="edit-monto" class="form-control" placeholder="Monto" min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_carga" class="form-label">Fecha de Carga</label>
                        <input type="text" name="fecha_carga" id="edit-fecha_carga" class="datepicker-here form-control" data-language="es">
                    </div>
                    <div id="edit-file-previews"></div>
                    <div class="form-group">
                        <label for="new_files">Subir nuevos archivos</label>
                        <input type="file" name="new_files[]" multiple class="form-control">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <input type="hidden" name="id" id="edit-id">
                        <button type="submit" id="edit-combustible-btn" class="btn btn-success">Guardar Cambios</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>