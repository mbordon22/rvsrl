<script>
    function getStock(selectElement) {
        const selectedOption = selectElement.value;
        document.getElementById('cantidad_entrega').setAttribute('readonly', true);
        document.getElementById('stock_disponible').setAttribute('readonly', true);

        //llamado ajax para obtener los datos del producto
        $.ajax({
            url: `/admin/epp/elementos/${selectedOption}`,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const stock = response.data.stock;
                    document.getElementById('stock_disponible').value = stock || '';
                    document.getElementById('cantidad_entrega').setAttribute('max', stock || 0);
                    document.getElementById('cantidad_entrega').value = '';
                    document.getElementById('id_elemento').value = selectedOption;
                    document.getElementById('talle').value = response.data.talle || '';
                    document.getElementById('producto').value = response.data.producto || '';
                    document.getElementById('tipo').value = response.data.tipo || '';
                    document.getElementById('marca').value = response.data.marca || '';
                } else {
                    document.getElementById('stock_disponible').value = '';
                    document.getElementById('cantidad_entrega').setAttribute('max', 0);
                }

                document.getElementById('cantidad_entrega').removeAttribute('readonly');
                document.getElementById('stock_disponible').removeAttribute('readonly');
            },
            error: function (error) {
                console.error("Error al obtener el stock:", error);
            }
            
        });
    }

    function actualizarCampoElementos() {
        const tableRows = document.querySelectorAll('#table-entrega tbody tr');
        const elementos = Array.from(tableRows).map(row => {
            return {
                id: row.cells[0].innerText,
                cantidad: row.cells[5].innerText
            };
        });
        document.getElementById('elementos_entrega').value = JSON.stringify(elementos);
    }

    function agregarElementoTable() {
        const selectElement = document.getElementById('select-producto');
        
        const id = document.getElementById('id_elemento').value;
        const producto = document.getElementById('producto').value;
        const tipo = document.getElementById('tipo').value;
        const marca = document.getElementById('marca').value;
        const talle = document.getElementById('talle').value;
        const stock = document.getElementById('stock_disponible').value;
        const cantidad = document.getElementById('cantidad_entrega').value;

        if(Number(cantidad) > Number(stock)) {
            swal({
                title: 'La cantidad a entregar no puede ser mayor al stock disponible.',
                icon: 'error'
            })
            document.getElementById('cantidad_entrega').value = '';
            return;
        }

        if (Number(cantidad) < 1) {
            swal({
                title: 'La cantidad a entregar debe ser mayor a 0.',
                icon: 'error'
            })
            document.getElementById('cantidad_entrega').value = '';
            return;
        }

        if(id == ""){
            swal({
                title: 'El registro se agregó exitosamente.',
                icon: 'error'
            })
            return;
        }

        if (id && cantidad) {
            const tableBody = document.querySelector('#table-entrega tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `<td>${id}</td>
                                <td>${producto}</td>
                                <td>${tipo}</td>
                                <td>${marca}</td>
                                <td>${stock}</td>
                                <td>${cantidad}</td>
                                <td>
                                    <button type="button" class="btn btn-danger" onclick="eliminarElementoTable(this, '${id}', '${producto}', '${tipo}', '${marca}', '${talle}')">Eliminar</button>
                                </td>`;
            tableBody.appendChild(newRow);

            // Eliminar la opción seleccionada del select
            selectElement.remove(selectElement.selectedIndex);

            // Limpiar campos
            document.getElementById('cantidad_entrega').value = '';
            document.getElementById('stock_disponible').value = '';
            document.getElementById('cantidad_entrega').removeAttribute('max');
            selectElement.selectedIndex = 0;

            // Actualizar el campo oculto
            actualizarCampoElementos();
        } else {
            alert('Por favor, selecciona un EPP y verifica el stock disponible.');
        }
    }

    function eliminarElementoTable(button, id, producto, tipo, marca, talle) {
        // Eliminar la fila de la tabla
        const row = button.parentElement.parentElement;
        const epp_id = row.cells[0].innerText
        row.remove();

        // Restaurar la opción al select
        const selectElement = document.getElementById('select-producto');
        const option = `
        <option value="${epp_id}">${producto} - Tipo: ${tipo} - Marca: ${marca} - Talle: ${talle}</option>
        `;
        selectElement.innerHTML = selectElement.innerHTML + option;

        // Actualizar el campo oculto
        actualizarCampoElementos();
    }

    function eliminarElementoTableEditar(button, id, stock, producto, tipo, marca, talle) {
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
                    url: `/admin/epp/entregas/remove-fila/${id}`,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            eliminarElementoTable(button, id, producto, tipo, marca, talle);
                            swal({
                                title: 'El registro se eliminó exitosamente.',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            })
                        } else {
                            swal({
                                title: 'Error al eliminar el registro.',
                                icon: 'error'
                            })
                        }
                    },
                    error: function (error) {
                        console.error("Error al eliminar el registro:", error);
                        swal({
                            title: 'Error al eliminar el registro.',
                            icon: 'error'
                        })
                    }
                    
                });
            }
        })
    }
</script>