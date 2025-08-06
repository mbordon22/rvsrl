<script>
// Configurar el token CSRF para todas las solicitudes AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    
    $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        processData: false, // Evita que jQuery procese los datos
        contentType: false, // Evita que jQuery establezca un tipo de contenido incorrecto
        success: function (response) {
            $('#cargascombustible-table').DataTable().ajax.reload(null, false);
            form[0].reset(); // Reinicia el formulario
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
        $('#edit-combustible-form').attr('action', `{{ url('admin/cargas-combustible') }}/${id}`);

        // Clear existing file previews
        $('#edit-file-previews').empty();

        // Display existing files
        if (data.media.length > 0) {
            data.media.forEach(file => {
                const filePreview = file.mime_type.startsWith('image/')
                    ? `<div class="file-preview">
                            <img src="${file.original_url}" alt="${file.name}" style="height: 100px; object-fit: contain;">
                            <button type="button" class="btn btn-danger btn-sm remove-file-btn" data-id="${file.id}">Eliminar</button>
                       </div>`
                    : `<div class="file-preview">
                            <a href="${file.original_url}" target="_blank">${file.name}</a>
                            <button type="button" class="btn btn-danger btn-sm remove-file-btn" data-id="${file.id}">Eliminar</button>
                       </div>`;
                $('#edit-file-previews').append(filePreview);
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
    
    $.ajax({
        type: 'POST', // Laravel requiere POST para spoofing PUT
        url: url,
        data: formData,
        processData: false, // Evita que jQuery procese los datos
        contentType: false, // Evita que jQuery establezca un tipo de contenido incorrecto
        success: function (response) {
            $('#cargascombustible-table').DataTable().ajax.reload(null, false);
            form[0].reset(); // Reinicia el formulario
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

// Handle file removal
$(document).on('click', '.remove-file-btn', function () {
    const fileId = $(this).data('id');
    const input = `<input type="hidden" name="remove_files[]" value="${fileId}">`;
    $('#edit-document-form').append(input);
    $(this).closest('.file-preview').remove();
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