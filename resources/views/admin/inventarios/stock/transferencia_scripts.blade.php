<script>
    function toggleOrigenFields(){
        const origen = document.getElementById('origen').value;
        const almacenOrigenDiv = document.getElementById('almacen_origen_div');
        const cuadrillaOrigenDiv = document.getElementById('cuadrilla_origen_div');
        const almacenOrigen = document.getElementById('almacen_origen_id');
        const cuadrillaOrigen = document.getElementById('cuadrilla_origen_id');
        const labelStock = document.getElementById('label_stock_disponible');
        
        limpiarTabla();

        if (origen === 'almacen') {
            almacenOrigenDiv.classList.remove('d-none');
            cuadrillaOrigenDiv.classList.add('d-none');
            cuadrillaOrigen.value = '';
            labelStock.textContent = 'Cantidad actual en Almacen de origen';
        } else if (origen === 'cuadrilla') {
            cuadrillaOrigenDiv.classList.remove('d-none');
            almacenOrigenDiv.classList.add('d-none');
            almacenOrigen.value = '';
            labelStock.textContent = 'Cantidad actual en Cuadrilla de origen';
        } else {
            almacenOrigenDiv.classList.add('d-none');
            cuadrillaOrigenDiv.classList.add('d-none');
            almacenOrigen.value = '';
            cuadrillaOrigen.value = '';
            labelStock.textContent = 'Cantidad actual en origen';
        }
    }

    function toggleDestinoFields(){
        const destino = document.getElementById('destino').value;
        const almacenDestinoDiv = document.getElementById('almacen_destino_div');
        const cuadrillaDestinoDiv = document.getElementById('cuadrilla_destino_div');
        const almacenDestino = document.getElementById('almacen_destino_id');
        const cuadrillaDestino = document.getElementById('cuadrilla_destino_id');

        if (destino === 'almacen') {
            almacenDestinoDiv.classList.remove('d-none');
            cuadrillaDestinoDiv.classList.add('d-none');
            cuadrillaDestino.value = '';
        } else if (destino === 'cuadrilla') {
            cuadrillaDestinoDiv.classList.remove('d-none');
            almacenDestinoDiv.classList.add('d-none');
            almacenDestino.value = '';
        } else {
            almacenDestinoDiv.classList.add('d-none');
            cuadrillaDestinoDiv.classList.add('d-none');
            almacenDestino.value = '';
            cuadrillaDestino.value = '';
        }
    }

    function limpiarTabla(){
        const tbody = document.querySelector('#table-materiales tbody');
        const materialesTransferencia = document.getElementById('materiales_transferencia');
        
        tbody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos materiales
        materialesTransferencia.value = '[]'; // Reiniciar el campo oculto
    }

    function getMaterialesStock(selectElement) {
        const selectedOption = selectElement.value;
        const origen = document.getElementById('origen').value;
        
        limpiarTabla();

        const URL = origen === 'almacen' 
            ? `/admin/inventarios/stock/getMaterialsByAlmacen/${selectedOption}`
            : `/admin/inventarios/stock/getMaterialsByCuadrilla/${selectedOption}`;

        //llamado ajax para obtener los datos del producto
        $.ajax({
            url: URL,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const selectProducto = $('#select-producto'); // Usar jQuery para Select2
                    selectProducto.empty(); // Vaciar las opciones actuales

                    const option = new Option(`Seleccionar`, '', false, false);
                    selectProducto.append(option);

                    if(response.data.length === 0) {
                        swal({
                            title: 'No hay materiales disponibles en el origen seleccionado.',
                            icon: 'info'
                        });
                        selectProducto.trigger('change');
                        return;
                    }

                    response.data.forEach(item => {
                        const option = new Option(`${item.material.codigo} - ${item.material.descripcion}`, item.material.id, false, false);
                        selectProducto.append(option);
                    });

                    // Recargar Select2 para reflejar los cambios
                    selectProducto.trigger('change');
                }
            },
            error: function (error) {
                console.error("Error al obtener los materiales:", error);
                const selectProducto = $('#select-producto');
                selectProducto.empty(); // Vaciar las opciones actuales
                selectProducto.trigger('change'); // Recargar Select2
            }
        });
    }

    function getStock(selectElement) {
        const selectedOption = selectElement.value;
        const almacenId = document.getElementById('almacen_origen_id').value;
        const cuadrillaId = document.getElementById('cuadrilla_origen_id').value;
        const origen = document.getElementById('origen').value;

        if(selectedOption === "") {
            document.getElementById('stock_disponible').value = '';
            return;
        }

        const URL = origen === 'almacen'
            ? `/admin/inventarios/stock/getMaterialByAlmacen/${selectedOption}/${almacenId}`
            : `/admin/inventarios/stock/getMaterialByCuadrilla/${selectedOption}/${cuadrillaId}`;

        //llamado ajax para obtener los datos del material
        $.ajax({
            url: URL,
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
        const stock = document.getElementById('stock_disponible').value;

        const cantidadEgreso = document.getElementById('cantidad_egreso').value;

        if (productoId === "" || cantidadEgreso === "") {
            swal({
                title: 'Debe seleccionar un producto y una cantidad.',
                icon: 'error'
            });
            return;
        }

        if( parseInt(cantidadEgreso) > parseInt(stock)) {
            swal({
                title: 'La cantidad a egresar no puede ser mayor al stock disponible.',
                icon: 'error'
            });
            return;
        }

        // Por ejemplo, crear una nueva fila en una tabla existente
        const tableBody = document.querySelector('#table-materiales tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `<td>${productoId}</td>
                            <td>${productoText}</td>
                            <td>${cantidadEgreso}</td>
                            <td>
                                <button type="button" class="btn btn-danger" onclick="eliminarElementoTable(this)">Eliminar</button>
                            </td>`;
        tableBody.appendChild(newRow);

        //ademas tengo un input oculto para almacenar los materiales ingresados
        const materialesTransferenciaInput = document.getElementById('materiales_transferencia');
        const materialesTransferencia = JSON.parse(materialesTransferenciaInput.value || '[]');

        // Agregar el nuevo material al array
        materialesTransferencia.push({
            id: productoId,
            cantidad: cantidadEgreso,
            material: productoText
        });

        // Actualizar el input oculto
        materialesTransferenciaInput.value = JSON.stringify(materialesTransferencia);

        // Limpiar los campos de entrada
        selectProducto.value = '';
        document.getElementById('stock_disponible').value = '';
        document.getElementById('cantidad_egreso').value = '';

        // quitar el producto seleccionado del select
        selectProducto.querySelector(`option[value="${productoId}"]`).remove();
    }

    function eliminarElementoTable(button) {
        
        const row = button.closest('tr');

        // Obtener el materialId antes de eliminar la fila
        const materialId = row.cells[0].textContent; // Asumiendo que el ID está en la primera celda

        // Actualizar el campo oculto
        const materialesTransferenciaInput = document.getElementById('materiales_transferencia');
        const materialesTransferencia = JSON.parse(materialesTransferenciaInput.value || '[]');

        // Filtrar el material eliminado
        const updatedMateriales = materialesTransferencia.filter(item => item.id !== materialId);

        // Actualizar el input oculto
        materialesTransferenciaInput.value = JSON.stringify(updatedMateriales);

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
        const origen = document.getElementById('origen').value;
        const destino = document.getElementById('destino').value;

        const almacenOrigenId = document.getElementById('almacen_origen_id').value;
        const cuadrillaOrigenId = document.getElementById('cuadrilla_origen_id').value;

        const almacenDestinoId = document.getElementById('almacen_destino_id').value;
        const cuadrillaDestinoId = document.getElementById('cuadrilla_destino_id').value
         
        // Validar que al menos uno de los campos de origen tenga un valor
        if (almacenOrigenId == "" && cuadrillaOrigenId == "") {
            swal({
                title: 'Debe seleccionar un Almacen Origen o una Cuadrilla Origen.',
                icon: 'error'
            });
            return;
        }

        // Validar que al menos uno de los campos de destino tenga un valor
        if (almacenDestinoId == "" && cuadrillaDestinoId == "") {
            swal({
                title: 'Debe seleccionar un Almacen Destino o una Cuadrilla Destino.',
                icon: 'error'
            });
            return;
        }

        // Validar que se hayan agregado materiales a la tabla
        const materialesTransferenciaInput = document.getElementById('materiales_transferencia').value
        if (materialesTransferenciaInput == "" || materialesTransferenciaInput == "[]") {
            swal({
                title: 'Debe agregar al menos un material a la tabla.',
                icon: 'error'
            });
            return;
        }

        event.target.submit(); // Enviar el formulario si las validaciones pasan
    });
    
</script>