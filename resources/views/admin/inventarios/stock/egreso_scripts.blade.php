<script>
    function limpiarTabla(){
        const tbody = document.querySelector('#table-materiales tbody');
        const materialesEgreso = document.getElementById('materiales_egreso');
        
        tbody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos materiales
        materialesEgreso.value = '[]'; // Reiniciar el campo oculto
    }

    function getMaterialesStock(selectElement) {
        const selectedOption = selectElement.value;
        
        limpiarTabla();

        const URL = `/admin/inventarios/stock/getMaterialsByAlmacen/${selectedOption}`;

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
                            title: 'No hay materiales disponibles en el almacen seleccionado.',
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
        const materialesEgresoInput = document.getElementById('materiales_egreso');
        const materialesEgreso = JSON.parse(materialesEgresoInput.value || '[]');

        // Agregar el nuevo material al array
        materialesEgreso.push({
            id: productoId,
            cantidad: cantidadEgreso,
            material: productoText
        });

        // Actualizar el input oculto
        materialesEgresoInput.value = JSON.stringify(materialesEgreso);

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
        const materialesEgresoInput = document.getElementById('materiales_egreso');
        const materialesEgreso = JSON.parse(materialesEgresoInput.value || '[]');

        // Filtrar el material eliminado
        const updatedMateriales = materialesEgreso.filter(item => item.id !== materialId);

        // Actualizar el input oculto
        materialesEgresoInput.value = JSON.stringify(updatedMateriales);

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
        const origen = document.getElementById('almacen_id').value;
        const destino = document.getElementById('tercero_tipo').value;
         
        // Validar que al menos uno de los campos de origen tenga un valor
        if (origen == "" || destino == "") {
            swal({
                title: 'Debe seleccionar un Almacen Origen y un destino.',
                icon: 'error'
            });
            return;
        }

        // Validar que se haya agregado al menos un material
        const materialesEgresoInput = document.getElementById('materiales_egreso').value;
        if (materialesEgresoInput == "[]" || materialesEgresoInput == "") {
            swal({
                title: 'Debe agregar al menos un material para el egreso.',
                icon: 'error'
            });
            return;
        }

        event.target.submit(); // Enviar el formulario si las validaciones pasan
    });
</script>