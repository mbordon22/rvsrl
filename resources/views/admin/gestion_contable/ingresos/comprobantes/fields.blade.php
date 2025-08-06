<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Cliente<span> *</span></label>
                <select class="form-select" name="cliente_id">
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}"
                            @if (isset($comprobante->cliente_id)) @selected(old('cliente_id', $comprobante->cliente_id) == $cliente->id) @endif>
                                {{ $cliente->nombre . " - " . $cliente->numero_documento }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Condición de Pago<span> *</span></label>
                <select class="form-select" name="condicion_cobro">
                    @foreach ($condicionesCobro as $condicion)
                        <option value="{{ $condicion->name }}"
                            @if (isset($comprobante->condicion_cobro)) @selected(old('condicion_cobro', $comprobante->condicion_cobro) == $condicion->name) @endif>
                                {{ $condicion->label() }}
                        </option>
                    @endforeach
                </select>
                @error('condicion_cobro')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Comprobante<span> *</span></label>
                <select class="form-select" name="comprobante_tipo" id="comprobante_tipo">
                    @foreach ($comprobanteTipos as $comprobanteTipo)
                        <option value="{{ $comprobanteTipo->name }}"
                            @if (isset($comprobante->comprobante_tipo->name)) @selected(old('comprobante_tipo', $comprobante->comprobante_tipo->name) == $comprobanteTipo->name)
                            @else @selected($comprobanteTipo->name == 'Recibo') @endif>
                                {{ $comprobanteTipo->label() }}
                        </option>
                    @endforeach
                </select>
                @error('comprobante_tipo')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Número<span> *</span></label>
                <input class="form-control" type="text" name="numero_comprobante" id="numero_comprobante"
                    value="{{ isset($comprobante->numero_comprobante) ? $comprobante->numero_comprobante : ( isset($numeroReciboProximo) ? $numeroReciboProximo : old('numero_comprobante')) }}" 
                    placeholder="Ingrese el número del documento" >
                @error('numero_comprobante')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Fecha Comprobante<span> *</span></label>
                <input class="datepicker-here form-control" type="text" name="fecha_comprobante"
                    value="{{ isset($fecha_comprobante) ? $fecha_comprobante : old('fecha_comprobante', now()->format('d/m/Y')) }}" data-language="es"
                    placeholder="Ingrese la fecha del comprobante" >
                @error('fecha_comprobante')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Vencimiento del Pago</label>
                <input class="datepicker-here form-control" type="text" name="fecha_vencimiento"
                    value="{{ isset($fecha_vencimiento) ? $fecha_vencimiento : old('fecha_vencimiento', now()->format('d/m/Y')) }}" data-language="es"
                    placeholder="Ingrese la fecha del vencimiento del pago">
                @error('fecha_vencimiento')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Medio de Pago</label>
                <select class="form-select" name="medio_pago_id">
                    @foreach ($mediosPago as $medioPago)
                        <option value="{{ $medioPago->id }}"
                            @if (isset($comprobante->medio_pago_id)) @selected(old('medio_pago_id', $comprobante->medio_pago_id) == $medioPago->id) @endif>
                                {{ $medioPago->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('medio_pago_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="mb-3">
                <label>Observaciones</label>
                <input class="form-control" type="text" name="descripcion"
                    value="{{ isset($comprobante->descripcion) ? $comprobante->descripcion : old('descripcion') }}" 
                    placeholder="" >
                @error('descripcion')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Logica seleccionar productos y su cantidad --}}
    <br>
    <hr><br>
    <div class="row">
        <div class="col-sm-4 height-equal">
            <div class="mb-3 international-num">
                <label>Producto / Servicio</label>
                <input type="hidden" id="listado_productos" value="{{ $productos }}">
                <input id="producto" class="form-control" style="width: 100%" type="text" placeholder="Ingrese el producto o servicio"
                    onkeyup="getValue(this.value)">
                <div class="results"></div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Centro de Costo</label>
                <select class="form-select .js-example-basic-single" id="select-centro-costo">
                    <option value="">Seleccionar</option>
                    @foreach ($centrosCosto as $centroCosto)
                    <option value="{{ $centroCosto->id }}">
                        {{ $centroCosto->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Descripción</label>
                <input type="text" class="form-control" id="descripcion_producto" name="descripcion_producto">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            <div class="mb-3">
                <label>Cantidad</label>
                <input type="number" min="1" step="1" class="form-control" id="cantidad" name="cantidad"
                    placeholder="0">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Precio</label>
                <input type="number" min="0" step="0.01" class="form-control" id="precio" name="precio"
                    value="0.00">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="mb-3">
                <label>% Descuento</label>
                <input type="number" min="0" step="0.01" class="form-control" id="descuento" name="descuento"
                    value="0.00">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="mb-3">
                <label>% Interes</label>
                <input type="number" min="0" step="0.01" class="form-control" id="interes" name="interes"
                    value="0.00">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="mb-3">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary form-control"
                    onclick="agregarElementoTable()">Agregar</button>
            </div>
        </div>
    </div>

    @if (isset($comprobante))
        <input type="hidden" id="productos_venta" name="productos_venta"
        value="{{ $comprobante->productos_venta }}">
    @else
        <input type="hidden" id="productos_venta" name="productos_venta" value="[]">
    @endif
    <div class="row mt-3">
        <div class="col-12">
            <h5>Elementos a agregar</h5>
            <table class="table table-bordered mt-2 mb-4" id="table-productos-venta">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Centro de Costo</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Importe</th>
                        <th>%Dto</th>
                        <th>Interes</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($comprobante))
                    @foreach ($comprobante->lineasIngreso as $key => $fila)
                    <tr id="row_{{ $key }}">
                        <td>{{ $fila->producto->nombre }}</td>
                        <td>{{ isset($fila->centroCosto) ? $fila->centroCosto->nombre : '' }}</td>
                        <td>{{ $fila->descripcion }}</td>
                        <td>{{ $fila->cantidad }}</td>
                        <td>${{ $fila->precio }}</td>
                        <td>${{ $fila->importe }}</td>
                        <td>{{ $fila->descuento }}%</td>
                        <td>{{ $fila->interes }}%</td>
                        <td>${{ $fila->total }}</td>
                        <td><button type="button" class="btn btn-danger"
                                onclick="eliminarElementoTable(this)">Eliminar</button>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="text-end">
                <button type="submit" class="btn btn-success">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
