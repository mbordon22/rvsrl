<div class="modal fade" id="addTipoVehiculoModal" tabindex="-1" aria-labelledby="addTipoVehiculoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTipoVehiculoModalLabel">Añadir Nueva Tipo de Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.vehiculo.tiposVehiculo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="tipo_vehiculo" class="form-label">Tipo de Vehiculo</label>
                        <input type="text" name="tipo_vehiculo" id="tipo_vehiculo" class="form-control" value="" required>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" id="add-tipo-vehiculo-btn" class="btn btn-success">Añadir Registro</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>