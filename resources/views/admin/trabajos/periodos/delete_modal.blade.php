<div class="modal fade" id="confirmationModal{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Eliminar período</h4>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><b>¿Eliminar este período de certificación?</b></p>
                <p>Los trabajos asignados quedarán liberados (no se borran).</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Cerrar</button>
                <form action="{{ route('admin.trabajos.periodos.destroy', $id) }}" method="post">
                    @csrf
                    @method('delete')
                    <button class="btn btn-danger" type="submit">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
