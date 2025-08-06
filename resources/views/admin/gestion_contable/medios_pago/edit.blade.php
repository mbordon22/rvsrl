<div class="modal fade" id="editMediosPagoModal" tabindex="-1" aria-labelledby="editMediosPagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMediosPagoModalLabel">Editar Medio de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-medios-pago-form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-control" value="" placeholder="Nombre">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <input type="hidden" name="id" id="edit-id">
                        <button type="submit" id="edit-medios-pago-btn" class="btn btn-success">Guardar Cambios</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>