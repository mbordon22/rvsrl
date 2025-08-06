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
        <div class="col-sm-3 d-none" id="almacen_ajuste_div">
            <div class="mb-3">
                <label>Almacen Ajuste<span> *</span></label>
                <select name="almacen_ajuste_id" id="almacen_ajuste_id" class="form-select" onchange="getMaterialesStock(this)">
                    <option value="">Seleccionar</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" 
                            @if (isset($stock->almacen_ajuste_id)) @selected(old('almacen_ajuste_id', $stock->almacen_ajuste_id) == $almacen->id) @endif>
                            {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('almacen_ajuste_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-3 d-none" id="cuadrilla_ajuste_div">
            <div class="mb-3">
                <label>Cuadrilla Ajuste<span> *</span></label>
                <select name="cuadrilla_ajuste_id" id="cuadrilla_ajuste_id" class="form-select" onchange="getMaterialesStock(this)">
                    <option value="">Seleccionar</option>
                    @foreach ($cuadrillas as $cuadrilla)
                        <option value="{{ $cuadrilla->id }}" 
                            @if (isset($stock->cuadrilla_ajuste_id)) @selected(old('cuadrilla_ajuste_id', $stock->cuadrilla_ajuste_id) == $cuadrilla->id) @endif>
                            {{ $cuadrilla->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('cuadrilla_ajuste_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
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
                <label id="label_stock_disponible">Cantidad actual</label>
                <input type="text" readonly class="form-control bg-light" id="stock_disponible" name="stock_disponible">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Cantidad Nueva</label>
                <input type="number" min="0" step="1" class="form-control" id="cantidad_ajuste" name="cantidad_ajuste"
                    placeholder="Cantidad Nueva">
            </div>
        </div>
        <div class="col">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-primary form-control" onclick="agregarElementoTable()">Agregar</button>
        </div>
    </div>

    @if (isset($stock))
        <input type="hidden" id="materiales_ajuste" name="materiales_ajuste"
        value="{{ $stock->materiales_ajuste }}">
    @else
        <input type="hidden" id="materiales_ajuste" name="materiales_ajuste" value="[]">
    @endif
    <div class="row mt-3">
        <div class="col-12">
            <h5>Materiales Egreso</h5>
            <table class="table table-bordered" id="table-materiales">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Material</th>
                        <th>Cantidad Actual</th>
                        <th>Cantidad Nueva</th>
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
