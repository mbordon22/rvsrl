<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentModalLabel">Editar Documentación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-document-form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="tipo_documento" class="form-label text-dark">Tipo de Documento</label>
                        <select name="tipo_documento" id="edit-tipo_documento" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach ($tipos_documentos as $tipo)
                                <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label text-dark">Fecha de Vencimiento</label>
                        <input type="text" name="fecha_vencimiento" id="edit-fecha_vencimiento" class="datepicker-here form-control" data-language="es">
                    </div>
                    <div class="mb-3 form-check form-switch d-flex align-items-center gap-2">
                        <input type="checkbox" role="switch" name="genera_alerta" id="edit-genera_alerta" value="1" class="form-check-input m-0" style="cursor:pointer; font-size:1.6rem;">
                        <label for="edit-genera_alerta" class="form-check-label text-dark mb-0">Emitir una alerta cuando llegue la fecha de vencimiento</label>
                    </div>
                    <div id="edit-file-previews"></div>
                    <div class="form-group">
                        <label for="new_files" class="text-dark">Subir nuevos archivos</label>
                        <input type="file" name="new_files[]" multiple class="form-control">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" id="edit-documento-btn" class="btn btn-success">Guardar Cambios</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>