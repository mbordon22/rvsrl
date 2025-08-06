<div class="form theme-form">
    <div class="row">
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Fecha<span> *</span></label>
                <input class="datepicker-here form-control" type="text" name="fecha"
                    value="{{ isset($fecha) ? $fecha : old('fecha', now()->format('d/m/Y')) }}" data-language="es"
                    placeholder="Ingrese la fecha" >
                @error('fecha')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Origen<span> *</span></label>
                <select name="origen" id="origen" class="form-select" onchange="toggleOrigenFields()">
                    <option value="">Seleccionar</option>
                    <option value="almacen">Almacen</option>
                    <option value="cuadrilla">Cuadrilla</option>
                </select>
                @error('origen')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-3 d-none" id="almacen_origen_div">
            <div class="mb-3">
                <label>Almacen Origen<span> *</span></label>
                <select name="almacen_origen_id" id="almacen_origen_id" class="form-select" onchange="getMaterialesStock(this)">
                    <option value="">Seleccionar</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" 
                            @if (isset($stock->almacen_origen_id)) @selected(old('almacen_origen_id', $stock->almacen_origen_id) == $almacen->id) @endif>
                            {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('almacen_origen_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-3 d-none" id="cuadrilla_origen_div">
            <div class="mb-3">
                <label>Cuadrilla Origen<span> *</span></label>
                <select name="cuadrilla_origen_id" id="cuadrilla_origen_id" class="form-select" onchange="getMaterialesStock(this)">
                    <option value="">Seleccionar</option>
                    @foreach ($cuadrillas as $cuadrilla)
                        <option value="{{ $cuadrilla->id }}" 
                            @if (isset($stock->cuadrilla_origen_id)) @selected(old('cuadrilla_origen_id', $stock->cuadrilla_origen_id) == $cuadrilla->id) @endif>
                            {{ $cuadrilla->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('cuadrilla_origen_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Destino<span> *</span></label>
                <select name="destino" id="destino" class="form-select" onchange="toggleDestinoFields()">
                    <option value="">Seleccionar</option>
                    <option value="almacen">Almacen</option>
                    <option value="cuadrilla">Cuadrilla</option>
                </select>
                @error('destino')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-3 d-none" id="almacen_destino_div">
            <div class="mb-3">
                <label>Almacen Destino<span> *</span></label>
                <select name="almacen_destino_id" id="almacen_destino_id" class="form-select">
                    <option value="">Seleccionar</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" 
                            @if (isset($stock->almacen_destino_id)) @selected(old('almacen_destino_id', $stock->almacen_destino_id) == $almacen->id) @endif>
                            {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('almacen_destino_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-3 d-none" id="cuadrilla_destino_div">
            <div class="mb-3">
                <label>Cuadrilla Destino<span> *</span></label>
                <select name="cuadrilla_destino_id" id="cuadrilla_destino_id" class="form-select">
                    <option value="">Seleccionar</option>
                    @foreach ($cuadrillas as $cuadrilla)
                        <option value="{{ $cuadrilla->id }}" 
                            @if (isset($stock->cuadrilla_destino_id)) @selected(old('cuadrilla_destino_id', $stock->cuadrilla_destino_id) == $cuadrilla->id) @endif>
                            {{ $cuadrilla->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('cuadrilla_destino_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <hr>

    <div class="row">    
        <div class="col-sm-12">
            <div class="mb-3">
                <label>Seleccione los Materiales</label>
                <select class="form-select .js-example-basic-single" id="select-producto" onchange="getStock(this)"></select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="mb-3">
                <label id="label_stock_disponible">Cantidad actual en origen</label>
                <input type="text" readonly class="form-control bg-light" id="stock_disponible" name="stock_disponible">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Cantidad</label>
                <input type="number" min="0" class="form-control" id="cantidad_egreso" name="cantidad_egreso"
                    placeholder="Cantidad a egresar">
            </div>
        </div>
        <div class="col">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-primary form-control" onclick="agregarElementoTable()">Agregar</button>
        </div>
    </div>

    @if (isset($stock))
        <input type="hidden" id="materiales_transferencia" name="materiales_transferencia"
        value="{{ $stock->materiales_transferencia }}">
    @else
        <input type="hidden" id="materiales_transferencia" name="materiales_transferencia" value="[]">
    @endif
    <div class="row mt-3">
        <div class="col-12">
            <h5>Materiales Egreso</h5>
            <table class="table table-bordered" id="table-materiales">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Material</th>
                        <th>Cantidad</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($stock))
                    @foreach ($stock->movimientos as $key => $fila)
                    <tr id="row_{{ $key }}">
                        <td>{{ $fila->elemento->id }}</td>
                        <td>{{ $fila->elemento->codigo . ' - ' . $fila->elemento->descripcion }}</td>
                        <td>{{ $fila->elemento->cantidad }}</td>
                        <td>
                            <button type="button" class="btn btn-danger" 
                                onclick="eliminarElementoTable(this)">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <div class="text-end">
                <button type="submit" class="btn btn-success">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
