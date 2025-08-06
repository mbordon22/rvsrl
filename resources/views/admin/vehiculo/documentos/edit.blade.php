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
                        <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                        <select name="tipo_documento" id="edit-tipo_documento" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach ($tipos_documentos as $tipo)
                                <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="text" name="fecha_vencimiento" id="edit-fecha_vencimiento" class="datepicker-here form-control" data-language="es">
                    </div>
                    <div id="edit-file-previews"></div>
                    <div class="form-group">
                        <label for="new_files">Subir nuevos archivos</label>
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