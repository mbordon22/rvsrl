<script>
    var result = document.querySelector(".results");
    var cleaveInstance = null; // <-- Agregamos variable global
    var numeroComprobanteInput = document.getElementById('numero_comprobante');
    
    // auto complete function
    function autoComplete(Arr, Input) {
        return Arr.filter((e) => e.nombre.toLowerCase().includes(Input.toLowerCase()));
    }

    function getValue(val) {
        // if no value
        if (!val) {
            result.innerHTML = "";
            return;
        }

        const productos_string = document.getElementById('listado_productos');
        const array_productos = JSON.parse(productos_string.value);

        // search goes here
        var data = autoComplete(array_productos, val);

        // append list data
        var res = "<ul>";
        data.forEach((e) => {
            res += `<li style='cursor:pointer;' onclick="seleccionarProducto('${e.nombre}')">${e.nombre}</li>`;
        });
        res += "</ul>";
        result.innerHTML = res;
    }

    function seleccionarProducto(nombre) {
        // Set the input value to the selected product name
        document.getElementById('producto').value = nombre;
        // Clear the results
        result.innerHTML = "";
    }

    function actualizarCampoElementos() {
        const tableRows = document.querySelectorAll('#table-productos-compra tbody tr');
        const elementos = Array.from(tableRows).map(row => {
            return {
                producto: row.cells[0].innerText,
                centro_costo: row.cells[1].innerText,
                descripcion: row.cells[2].innerText,
                cantidad: row.cells[3].innerText,
                precio: row.cells[4].innerText.replace(/[^0-9.,-]+/g, ''),
                importe_bruto: row.cells[5].innerText.replace(/[^0-9.,-]+/g, ''),
                descuento: row.cells[6].innerText.replace(/[^0-9.,-]+/g, ''),
                iva: row.cells[7].innerText.replace(/[^0-9.,-]+/g, ''),
                exe_no_grav: row.cells[8].innerText.replace(/[^0-9.,-]+/g, ''),
                importe_total: row.cells[9].innerText.replace(/[^0-9.,-]+/g, '')
            };
        });
        document.getElementById('productos_compra').value = JSON.stringify(elementos);
    }

    function agregarElementoTable() {
        const selectCentroCosto = document.getElementById('select-centro-costo');
        const centro_costo_id = selectCentroCosto.value;
        const centro_costo = (centro_costo_id > 0) ? selectCentroCosto.options[selectCentroCosto.selectedIndex].text : '';
        
        const producto = document.getElementById('producto').value;
        const descripcion_producto = document.getElementById('descripcion_producto').value;
        const cantidad = document.getElementById('cantidad').value;
        const precio = document.getElementById('precio').value;
        const descuento = document.getElementById('descuento').value;
        const iva = document.getElementById('iva').value;
        const exe_no_grav = document.getElementById('exe_no_grav').value;

        //calculos importes
        const importe_bruto = (Number(precio) * Number(cantidad));
        const importe_descuento = (importe_bruto * (Number(descuento) / 100));
        const importe_con_descuento = importe_bruto - importe_descuento;
        const importe_total = importe_con_descuento + (Number(iva)) + (Number(exe_no_grav));

        if(producto == ""){
            swal({
                title: 'Debe ingresar un producto.',
                icon: 'error'
            })
            return;
        }

        if (Number(cantidad) < 1) {
            swal({
                title: 'La cantidad debe ser mayor a 0.',
                icon: 'error'
            })
            document.getElementById('cantidad').value = '';
            return;
        }

        if (Number(precio) < 1) {
            swal({
                title: 'El precio debe ser mayor a 0.',
                icon: 'error'
            })
            document.getElementById('cantidad').value = '';
            return;
        }

        if (producto && cantidad) {
            const tableBody = document.querySelector('#table-productos-compra tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `<td>${producto}</td>
                                <td>${centro_costo}</td>
                                <td>${descripcion_producto}</td>
                                <td>${cantidad}</td>
                                <td>$${precio}</td>
                                <td>$${importe_bruto}</td>
                                <td>${descuento}%</td>
                                <td>$${iva}</td>
                                <td>$${exe_no_grav}</td>
                                <td>$${importe_total}</td>
                                <td>
                                    <button type="button" class="btn btn-danger" onclick="eliminarElementoTable(this)">Eliminar</button>
                                </td>`;
            tableBody.appendChild(newRow);

            // Limpiar campos
            document.getElementById('descripcion_producto').value = '';
            document.getElementById('cantidad').value = '';
            document.getElementById('precio').value = '0.00';
            document.getElementById('descuento').value = '0.00';
            document.getElementById('iva').value = '0.00';
            document.getElementById('exe_no_grav').value = '0.00';
            document.getElementById('producto').value = '';
            
            selectCentroCosto.selectedIndex = 0;

            // Actualizar el campo oculto
            actualizarCampoElementos();
        } else {
            alert('Selecciona un producto y una cantidad válida.');
        }
    }

    function eliminarElementoTable(button) {
        // Eliminar la fila de la tabla
        const row = button.parentElement.parentElement;
        row.remove();

        // Actualizar el campo oculto
        actualizarCampoElementos();
    }

    document.getElementById('comprobante_tipo').addEventListener('change', function() {
        const tipo = this.value;
        numeroComprobanteInput.value = '';

        // Remover event listeners previos
        var newInput = numeroComprobanteInput.cloneNode(true);
        numeroComprobanteInput.parentNode.replaceChild(newInput, numeroComprobanteInput);
        numeroComprobanteInput = newInput;

        // Limpiar instancia previa de Cleave
        if (cleaveInstance) {
            cleaveInstance.destroy && cleaveInstance.destroy();
            cleaveInstance = null;
        }

        if(tipo == 'Factura' || tipo == 'Recibo' || tipo == 'NotaDeDebito' || tipo == 'NotaDeCredito') {
            numeroComprobanteInput.setAttribute("placeholder", "A-00001-00000001");
            cleaveInstance = new Cleave("#numero_comprobante", {
                delimiters: ["-", "-"],
                blocks: [1, 5, 8],
                uppercase: true,
                numericOnly: false // permitimos letras en el primer bloque
            });
            numeroComprobanteInput.addEventListener('input', function(e) {
                let val = e.target.value.toUpperCase();
                let match = val.match(/^([ABCX])-(\d{0,5})-(\d{0,8})$/);
                if (!match) {
                    let parts = val.split('-');
                    let first = parts[0] ? parts[0].replace(/[^ABCX]/g, '').charAt(0) || '' : '';
                    let second = parts[1] ? parts[1].replace(/\D/g, '') : '';
                    let third = parts[2] ? parts[2].replace(/\D/g, '') : '';
                    let newVal = first;
                    if (second.length > 0) newVal += '-' + second;
                    if (third.length > 0) newVal += '-' + third;
                    e.target.value = newVal;
                }
            });
        } else {
            numeroComprobanteInput.setAttribute("placeholder", "00000001");
            cleaveInstance = new Cleave("#numero_comprobante", {
                blocks: [8],
                numericOnly: true
            });
            numeroComprobanteInput.addEventListener('input', function(e) {
                let val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val;
            });
        }
    });

</script>