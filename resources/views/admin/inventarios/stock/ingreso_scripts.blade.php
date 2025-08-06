<script>
    function getStock(selectElement) {
        const selectedOption = selectElement.value;
        const selectAlmacen = document.getElementById('almacen_id');
        const almacenId = selectAlmacen.value;

        //llamado ajax para obtener los datos del producto
        $.ajax({
            url: `/admin/inventarios/stock/getMaterialByAlmacen/${selectedOption}/${almacenId}`,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const stock = response.data.cantidad;
                    document.getElementById('stock_disponible').value = stock || '';
                } else {
                    document.getElementById('stock_disponible').value = '';
                }
            },
            error: function (error) {
                document.getElementById('stock_disponible').value = '';
                console.error("Error al obtener el stock:", error);
            }
        });
    }

    function agregarElementoTable() {
        const selectProducto = document.getElementById('select-producto');
        const productoId = selectProducto.value;
        const productoText = selectProducto.options[selectProducto.selectedIndex].text;

        const cantidadIngreso = document.getElementById('cantidad_ingreso').value;
        const puntoAlerta = document.getElementById('punto_alerta').value;

        if (productoId === "" || cantidadIngreso === "") {
            swal({
                title: 'Debe seleccionar un producto y una cantidad.',
                icon: 'error'
            });
            return;
        }

        // Por ejemplo, crear una nueva fila en una tabla existente
        const tableBody = document.querySelector('#table-materiales tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `<td>${productoId}</td>
                            <td>${productoText}</td>
                            <td>${cantidadIngreso}</td>
                            <td>${puntoAlerta}</td>
                            <td>
                                <button type="button" class="btn btn-danger" onclick="eliminarElementoTable(this)">Eliminar</button>
                            </td>`;
        tableBody.appendChild(newRow);

        //ademas tengo un input oculto para almacenar los materiales ingresados
        const materialesIngresoInput = document.getElementById('materiales_ingreso');
        const materialesIngreso = JSON.parse(materialesIngresoInput.value || '[]');

        // Agregar el nuevo material al array
        materialesIngreso.push({
            id: productoId,
            cantidad: cantidadIngreso,
            punto_alerta: puntoAlerta,
            material: productoText
        });

        // Actualizar el input oculto
        materialesIngresoInput.value = JSON.stringify(materialesIngreso);

        // Limpiar los campos de entrada
        selectProducto.value = '';
        document.getElementById('stock_disponible').value = '';
        document.getElementById('cantidad_ingreso').value = '';
        document.getElementById('punto_alerta').value = '';

        // quitar el producto seleccionado del select
        selectProducto.querySelector(`option[value="${productoId}"]`).remove();
    }

    function eliminarElementoTable(button) {
        
        const row = button.closest('tr');

        // Obtener el materialId antes de eliminar la fila
        const materialId = row.cells[0].textContent; // Asumiendo que el ID está en la primera celda

        // Actualizar el campo oculto
        const materialesIngresoInput = document.getElementById('materiales_ingreso');
        const materialesIngreso = JSON.parse(materialesIngresoInput.value || '[]');

        // Filtrar el material eliminado
        const updatedMateriales = materialesIngreso.filter(item => item.id !== materialId);

        // Actualizar el input oculto
        materialesIngresoInput.value = JSON.stringify(updatedMateriales);

        // Agregar nuevamente el producto al select
        const selectProducto = document.getElementById('select-producto');
        const newOption = document.createElement('option');
        newOption.value = materialId;
        newOption.textContent = row.cells[1].textContent; // Asumiendo que el nombre del producto está en la segunda celda
        selectProducto.appendChild(newOption);

        // Eliminar la fila de la tabla
        row.remove();
    }

    document.getElementById('StockForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto
        
        const almacen_id = document.getElementById('almacen_id').value;
        const tercero_tipo = document.getElementById('tercero_tipo').value;
         
        // Validar que al menos uno de los campos de origen tenga un valor
        if (almacen_id == "" || tercero_tipo == "") {
            swal({
                title: 'Debe seleccionar un Almacen de Destino y de quien Ingresa.',
                icon: 'error'
            });
            return;
        }

        // Validar que se hayan agregado materiales a la tabla
        const materialesIngresoInput = document.getElementById('materiales_ingreso').value
        if (materialesIngresoInput == "" || materialesIngresoInput == "[]") {
            swal({
                title: 'Debe agregar al menos un material a la tabla.',
                icon: 'error'
            });
            return;
        }

        event.target.submit(); // Enviar el formulario si las validaciones pasan
    });
    
</script>