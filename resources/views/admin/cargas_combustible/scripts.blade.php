<script>
// Configurar el token CSRF para todas las solicitudes AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

// ===================== Dropzone (modo manual) =====================
// Los archivos se adjuntan al FormData al enviar el formulario; Dropzone
// no sube por su cuenta (autoProcessQueue/autoQueue en false).
Dropzone.autoDiscover = false;
var createDropzone = null;
var editDropzone = null;
var editDropzoneClearing = false; // evita registrar remove_files al limpiar programáticamente

// Limpia el Dropzone de edición sin marcar los archivos existentes para borrar.
function clearEditDropzone() {
    if (!editDropzone) { return; }
    editDropzoneClearing = true;
    editDropzone.removeAllFiles(true);
    editDropzoneClearing = false;
}

function getDropzoneOpciones() {
    return {
        url: '{{ route('admin.cargas-combustible.store') }}', // requerido por Dropzone; no se usa
        method: 'post',
        autoProcessQueue: false,
        autoQueue: false,
        uploadMultiple: true,
        parallelUploads: 10,
        maxFiles: 10,
        maxFilesize: 10, // MB
        addRemoveLinks: true,
        acceptedFiles: 'image/jpeg,image/png,image/jpg,application/pdf',
        dictDefaultMessage: 'Arrastrá los archivos acá o hacé clic para seleccionar',
        dictRemoveFile: 'Quitar',
        dictMaxFilesExceeded: 'No podés subir más archivos.',
        dictInvalidFileType: 'Tipo de archivo no permitido (solo imágenes o PDF).',
        dictCancelUpload: 'Cancelar',
        dictFileTooBig: 'El archivo es muy grande (@{{filesize}}MB). Máximo: @{{maxFilesize}}MB.'
    };
}

$(function () {
    if (document.getElementById('createDropzone')) {
        createDropzone = new Dropzone('#createDropzone', getDropzoneOpciones());
    }
    if (document.getElementById('editDropzone')) {
        editDropzone = new Dropzone('#editDropzone', getDropzoneOpciones());

        // Al quitar un archivo existente, registrarlo para eliminar en el backend.
        editDropzone.on('removedfile', function (file) {
            if (editDropzoneClearing) { return; }
            if (file && file.existing && file.serverId) {
                $('#edit-combustible-form').append(
                    '<input type="hidden" name="remove_files[]" value="' + file.serverId + '">'
                );
            }
        });
    }
});

// Agregar un nuevo registro
$(document).on('click', '#add-combustible-btn', function (e) {
    e.preventDefault();
    e.target.disabled = true; // Deshabilitar el botón para evitar múltiples envíos
    e.target.innerHTML = 'Cargando...'; // Cambiar el texto del botón

    const form = $(this).closest('form');
    const url = form.attr('action');
    const formData = new FormData(form[0]);

    // Adjuntar los archivos cargados en el Dropzone.
    if (createDropzone) {
        createDropzone.getAcceptedFiles().forEach(function (file) {
            formData.append('archivo[]', file);
        });
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        processData: false, // Evita que jQuery procese los datos
        contentType: false, // Evita que jQuery establezca un tipo de contenido incorrecto
        success: function (response) {
            $('#cargascombustible-table').DataTable().ajax.reload(null, false);
            form[0].reset(); // Reinicia el formulario
            if (createDropzone) { createDropzone.removeAllFiles(true); }
            swal({
                title: 'El registro se agregó exitosamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                $('#addCombustibleModal').modal('hide');
            });
        },
        error: function (xhr) {
            const response = JSON.parse(xhr.responseText);
            const errorList = Object.values(response.errors)
                .flat()
                .map(error => `<li>${error}</li>`)
                .join('');
            $('#errors').html(errorList);
        },
        complete: function () {
            e.target.disabled = false; // Habilitar el botón nuevamente
            e.target.innerHTML = 'Añadir Registro'; // Restaurar el texto del botón
        }
    });
});

// Abrir el modal de edición
$(document).on('click', '.edit-document-btn', function () {
    const id = $(this).data('id');
    const url = `{{ url('admin/vehiculos/combustible') }}/${id}`;
    
    $.get(url, function (data) {
        $('#edit-vehiculo_id').val(data.vehiculo_id);
        $('#edit-tipo_combustible').val(data.tipo_combustible);
        $('#edit-monto').val(data.monto);
        $('#edit-litros').val(data.litros);
        $('#edit-fecha_carga').val(data.fecha_carga ? moment(data.fecha_carga).format('DD/MM/YYYY') : '');
        $('#edit-user_id').val(data.user_id);
        // Refrescar selects con select2 si ya están inicializados.
        $('#edit-vehiculo_id, #edit-user_id').trigger('change.select2');
        $('#edit-combustible-form').attr('action', `{{ url('admin/cargas-combustible') }}/${id}`);

        // Reiniciar el Dropzone y los archivos marcados para borrar de una edición anterior.
        clearEditDropzone();
        $('#edit-combustible-form').find('input[name="remove_files[]"]').remove();

        // Mostrar los archivos existentes como previews dentro del propio Dropzone.
        if (editDropzone && data.media && data.media.length > 0) {
            data.media.forEach(function (file) {
                var mockFile = {
                    name: file.name,
                    size: file.size,
                    existing: true,
                    serverId: file.id,
                    accepted: true
                };
                editDropzone.emit('addedfile', mockFile);
                if (file.mime_type && file.mime_type.indexOf('image/') === 0) {
                    editDropzone.emit('thumbnail', mockFile, file.original_url);
                }
                editDropzone.emit('complete', mockFile);
                editDropzone.files.push(mockFile);
            });
        }

        $('#editCombustibleModal').modal('show');
    });
});

//Editar un registro
$(document).on('click', '#edit-combustible-btn', function (e) {
    e.preventDefault();
    e.target.disabled = true; // Deshabilitar el botón para evitar múltiples envíos
    e.target.innerHTML = 'Cargando...'; // Cambiar el texto del botón

    const form = $(this).closest('form');
    const url = form.attr('action');
    const formData = new FormData(form[0]);

    // Adjuntar solo los archivos nuevos del Dropzone (no los existentes mostrados como preview).
    if (editDropzone) {
        editDropzone.getAcceptedFiles().forEach(function (file) {
            if (!file.existing) {
                formData.append('new_files[]', file);
            }
        });
    }

    $.ajax({
        type: 'POST', // Laravel requiere POST para spoofing PUT
        url: url,
        data: formData,
        processData: false, // Evita que jQuery procese los datos
        contentType: false, // Evita que jQuery establezca un tipo de contenido incorrecto
        success: function (response) {
            $('#cargascombustible-table').DataTable().ajax.reload(null, false);
            form[0].reset(); // Reinicia el formulario
            clearEditDropzone();
            $('#edit-combustible-form').find('input[name="remove_files[]"]').remove();
            swal({
                title: 'El registro se actualizó exitosamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                $('#editCombustibleModal').modal('hide');
            });
        },
        error: function (xhr) {
            const response = JSON.parse(xhr.responseText);
            const errorList = Object.values(response.errors)
                .flat()
                .map(error => `<li>${error}</li>`)
                .join('');
            $('#errors').html(errorList);
        },
        complete: function () {
            e.target.disabled = false; // Habilitar el botón nuevamente
            e.target.innerHTML = 'Guardar Cambios'; // Restaurar el texto del botón
        }
    });
});

//Eliminar un registro
$(document).on('click', '.btn-delete', function() {
    const id = $(this).data('id');

    swal({
        title: '¿Estás seguro de eliminar el registro?',
        text: "No podrás revertir esto.",
        icon: 'warning',
        buttons: {
            cancel: {
                text: "Cancelar",
                visible: true,
            },
            confirm: {
                text: "Eliminar",
                className: "btn-danger",
            }
        },
        dangerMode: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: `/admin/cargas-combustible/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#cargascombustible-table').DataTable().ajax.reload(null, false);
                    swal({
                        title: 'El registro se eliminó exitosamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                },
                error: function() {
                    alert('Hubo un error al eliminar.');
                }
            });
        }
    });
});

// modal para visualizar archivos
$(document).on('click', '.view-file-btn', function () {
    const id = $(this).data('id');
    const url = `{{ url('admin/vehiculos/combustible') }}/${id}`;
    
    $.get(url, function (data) {
        $('#viewFilesCombustibleModalLabel').text(data.tipo_combustible + ' - ' + data.fecha_carga);
        $('#carouselExampleIndicators .carousel-inner').empty();
        if (data.media.length > 0) {
            data.media.forEach((file, index) => {
                const activeClass = index === 0 ? 'active' : '';
                if (file.mime_type === 'application/pdf') {
                    $('#carouselExampleIndicators .carousel-inner').append(`
                        <div class="carousel-item ${activeClass}">
                            <iframe class="d-block w-100" src="${file.original_url}" style="height: 250px; width: 100%; border: none;"></iframe>
                            <div class="d-flex justify-content-center">
                                <a href="${file.original_url}" class="btn btn-primary mt-2" download target="_blank">Descargar PDF</a>
                            </div>
                        </div>
                    `);
                } else {
                    $('#carouselExampleIndicators .carousel-inner').append(`
                        <div class="carousel-item ${activeClass}">
                            <img class="d-block w-100" src="${file.original_url}" alt="${data.tipo_combustible}" style="height: 250px; width: 250px; object-fit: contain;">
                            <div class="d-flex justify-content-center">
                                <a href="${file.original_url}" class="btn btn-primary mt-2" download target="_blank">Descargar Imagen</a>
                            </div>
                        </div>
                    `);
                }
            });
        } else {
            $('#carouselExampleIndicators .carousel-inner').append(`
                <div class="carousel-item active">
                    <img class="d-block w-100" src="{{ asset('assets/images/no-image.png') }}" alt="No Image" style="height: 250px; width: 250px; object-fit: contain;">
                </div>
            `);
        }
        $('#carouselExampleIndicators').carousel(0);
        $('#viewFilesCombustibleModal').modal('show');
    });
});
</script>