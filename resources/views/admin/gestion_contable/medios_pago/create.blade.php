<div class="modal fade" id="addMediosPagoModal" tabindex="-1" aria-labelledby="addMediosPagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMediosPagoModalLabel">Añadir Nuevo Medio de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.gestion-contable.medio-pago.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="" placeholder="Nombre">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" id="add-medios-pago-btn" class="btn btn-success">Añadir Registro</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>