<script>
// Configurar el token CSRF para todas las solicitudes AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

// Agregar un nuevo registro
$(document).on('click', '#add-medios-pago-btn', function (e) {
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
            $('#mediopago-table').DataTable().ajax.reload(null, false);
            form[0].reset(); // Reinicia el formulario
            swal({
                title: 'El registro se agregó exitosamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                $('#addMediosPagoModal').modal('hide');
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
$(document).on('click', '.edit-btn', function () {
    const id = $(this).data('id');
    const url = `{{ url('admin/gestion-contable/medio-pago') }}/${id}`;
    
    $.get(url, function (data) {
        $('#edit-nombre').val(data.nombre);
        $('#edit-id').val(data.id);
        $('#edit-medios-pago-form').attr('action', `{{ url('admin/gestion-contable/medio-pago') }}/${id}`);
        $('#editMediosPagoModal').modal('show');
    });
});

//Editar un registro
$(document).on('click', '#edit-medios-pago-btn', function (e) {
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
            $('#mediopago-table').DataTable().ajax.reload(null, false);
            form[0].reset(); // Reinicia el formulario
            swal({
                title: 'El registro se actualizó exitosamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                $('#editMediosPagoModal').modal('hide');
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
                url: `/admin/gestion-contable/medio-pago/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#mediopago-table').DataTable().ajax.reload(null, false);
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

</script>