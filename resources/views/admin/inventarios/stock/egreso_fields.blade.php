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
                <label>Almacen Origen<span> *</span></label>
                <select name="almacen_id" id="almacen_id" class="form-select" onchange="getMaterialesStock(this)">
                    <option value="">Seleccionar</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" 
                            @if (isset($stock->almacen_id)) @selected(old('almacen_id', $stock->almacen_id) == $almacen->id) @endif>
                            {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('almacen_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Egreso Hacia<span> *</span></label>
                <select name="tercero_tipo" id="tercero_tipo" class="form-select">
                    <option value="">Seleccionar</option>
                    @foreach ($tercerosStock as $tercero)
                        <option value="{{ $tercero->name }}" 
                            @if (isset($stock->tercero_tipo)) @selected(old('tercero_tipo', $stock->tercero_tipo) == $tercero->name) @endif>
                            {{ $tercero->label() }}
                        </option>
                    @endforeach
                </select>
                @error('tercero_tipo')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Info Egreso</label>
                <input type="text" class="form-control" name="tercero_informacion"
                    value="{{ isset($stock->tercero_informacion) ? $stock->tercero_informacion : old('tercero_informacion') }}" 
                    placeholder="Ingrese información adicional">
                @error('tercero_informacion')
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
                <select class="form-select .js-example-basic-single" id="select-producto" onchange="getStock(this)">
                    <option value="">Seleccionar</option>
                    @foreach ($materiales as $material)
                    <option value="{{ $material->id }}">
                        {{ $material->codigo . ' - ' . $material->descripcion }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="mb-3">
                <label>Cantidad actual en Almacen de origen</label>
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
        <input type="hidden" id="materiales_egreso" name="materiales_egreso"
        value="{{ $stock->materiales_egreso }}">
    @else
        <input type="hidden" id="materiales_egreso" name="materiales_egreso" value="[]">
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
