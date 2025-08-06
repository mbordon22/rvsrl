<script>
    function toggleOrigenFields(){
        const origen = document.getElementById('origen').value;
        const almacenAjusteDiv = document.getElementById('almacen_ajuste_div');
        const cuadrillaAjusteDiv = document.getElementById('cuadrilla_ajuste_div');
        const almacenAjuste = document.getElementById('almacen_ajuste_id');
        const cuadrillaAjuste = document.getElementById('cuadrilla_ajuste_id');
        const labelStock = document.getElementById('label_stock_disponible');
        
        limpiarTabla();

        if (origen === 'almacen') {
            almacenAjusteDiv.classList.remove('d-none');
            cuadrillaAjusteDiv.classList.add('d-none');
            cuadrillaAjuste.value = '';
            labelStock.textContent = 'Cantidad actual en Almacen';
        } else if (origen === 'cuadrilla') {
            cuadrillaAjusteDiv.classList.remove('d-none');
            almacenAjusteDiv.classList.add('d-none');
            almacenAjuste.value = '';
            labelStock.textContent = 'Cantidad actual en Cuadrilla';
        } else {
            almacenAjusteDiv.classList.add('d-none');
            cuadrillaAjusteDiv.classList.add('d-none');
            almacenAjuste.value = '';
            cuadrillaAjuste.value = '';
            labelStock.textContent = 'Cantidad actual en origen';
        }
    }

    function limpiarTabla(){
        const tbody = document.querySelector('#table-materiales tbody');
        const materialesAjuste = document.getElementById('materiales_ajuste');
        
        tbody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos materiales
        materialesAjuste.value = '[]'; // Reiniciar el campo oculto
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
        const almacenId = document.getElementById('almacen_ajuste_id').value;
        const cuadrillaId = document.getElementById('cuadrilla_ajuste_id').value;
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
                    document.getElementById('stock_disponible').value = stock || 0;
                } else {
                    document.getElementById('stock_disponible').value = 0;
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

        const cantidadAjuste = document.getElementById('cantidad_ajuste').value;
        const cantidadActual = document.getElementById('stock_disponible').value;

        if (productoId === "" || cantidadAjuste === "") {
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
                            <td>${cantidadActual}</td>
                            <td>${cantidadAjuste}</td>
                            <td>
                                <button type="button" class="btn btn-danger" onclick="eliminarElementoTable(this)">Eliminar</button>
                            </td>`;
        tableBody.appendChild(newRow);

        //ademas tengo un input oculto para almacenar los materiales ingresados
        const materialesAjusteInput = document.getElementById('materiales_ajuste');
        const materialesAjuste = JSON.parse(materialesAjusteInput.value || '[]');

        // Agregar el nuevo material al array
        materialesAjuste.push({
            id: productoId,
            cantidad: cantidadAjuste,
            material: productoText
        });

        // Actualizar el input oculto
        materialesAjusteInput.value = JSON.stringify(materialesAjuste);

        // Limpiar los campos de entrada
        selectProducto.value = '';
        document.getElementById('stock_disponible').value = '';
        document.getElementById('cantidad_ajuste').value = '';

        // quitar el producto seleccionado del select
        selectProducto.querySelector(`option[value="${productoId}"]`).remove();
    }

    function eliminarElementoTable(button) {
        
        const row = button.closest('tr');

        // Obtener el materialId antes de eliminar la fila
        const materialId = row.cells[0].textContent; // Asumiendo que el ID está en la primera celda

        // Actualizar el campo oculto
        const materialesAjusteInput = document.getElementById('materiales_ajuste');
        const materialesAjuste = JSON.parse(materialesAjusteInput.value || '[]');

        // Filtrar el material eliminado
        const updatedMateriales = materialesAjuste.filter(item => item.id !== materialId);

        // Actualizar el input oculto
        materialesAjusteInput.value = JSON.stringify(updatedMateriales);

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

        const almacenAjusteId = document.getElementById('almacen_ajuste_id').value;
        const cuadrillaAjusteId = document.getElementById('cuadrilla_ajuste_id').value;

        // Validar que al menos uno de los campos de origen tenga un valor
        if (almacenAjusteId == "" && cuadrillaAjusteId == "") {
            swal({
                title: 'Debe seleccionar un Almacen o una Cuadrilla a Ajustar.',
                icon: 'error'
            });
            return;
        }

        // Validar que se hayan agregado materiales a la tabla
        const materialesAjusteInput = document.getElementById('materiales_ajuste').value;
        if (materialesAjusteInput == "[]" || materialesAjusteInput == "") {
            swal({
                title: 'Debe agregar al menos un material a la tabla.',
                icon: 'error'
            });
            return;
        }

        event.target.submit(); // Enviar el formulario si las validaciones pasan
    });
    
</script>