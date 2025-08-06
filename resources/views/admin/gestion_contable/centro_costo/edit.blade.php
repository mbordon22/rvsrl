<div class="modal fade" id="editCentroCostoModal" tabindex="-1" aria-labelledby="editCentroCostoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCentroCostoModalLabel">Editar Centro de Costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-centro-costo-form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-control" value="" placeholder="Nombre">
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit-descripcion" cols="30" rows="10" placeholder="Descripción del centro de costo" class="form-control"></textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <input type="hidden" name="id" id="edit-id">
                        <button type="submit" id="edit-centro-costo-btn" class="btn btn-success">Guardar Cambios</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>