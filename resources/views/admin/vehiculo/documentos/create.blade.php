<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Añadir Documentación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.vehiculo.documento.store', $vehiculo->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                        <select name="tipo_documento" id="tipo_documento" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach ($tipos_documentos as $tipo)
                                <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="text" name="fecha_vencimiento" id="fecha_vencimiento" class="datepicker-here form-control" data-language="es" value="">
                        @error('fecha_vencimiento')
                            <span class="text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Archivo</label>
                        <input type="file" name="archivo[]" id="archivo" class="form-control" multiple accept="application/pdf, image/jpeg, image/png, image/jpg">
                        @error('archivo')
                            <span class="text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" id="add-documento-btn" class="btn btn-success">Añadir Documento</button>
                    </div>
                    <ul class="my-3 text-danger" id="errors" style="list-style-type: none; padding: 0;">
                        <!-- Los errores se mostrarán aquí -->
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>