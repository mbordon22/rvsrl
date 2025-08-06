<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Fecha de Entrega</label>
                <input class="datepicker-here form-control" type="text" name="fecha"
                    value="{{ isset($entrega->fecha) ? $entrega->fecha : old('fecha', now()->format('d/m/Y')) }}"
                    data-language="es" placeholder="Fecha de entrega de los epp">
                @error('fecha')
                <span class="text-danger">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Usuario</label>
                <select class="form-select" name="user_id">
                    <option value="">Seleccione un usuario</option>
                    @foreach ($usuarios as $key => $usuario)
                    <option value="{{ $usuario->id }}" @if (isset($entrega->user_id)) @selected(old('user_id',
                        $entrega->user_id) == $usuario->id) @endif>{{ $usuario->first_name . ' ' . $usuario->last_name
                        }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Logica seleccionar elementos y su cantidad a entregar --}}
    <br>
    <hr><br>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <label>Seleccione los EPP a entregar</label>
                <select class="form-select .js-example-basic-single" id="select-producto" onchange="getStock(this)">
                    <option value="">Seleccionar</option>
                    @foreach ($elementos as $key => $epp)
                    <option value="{{ $epp->id }}">
                        {{ $epp->producto . ' - Tipo: ' . ($epp->tipo ?? 'N/A') . ' - Marca: ' . ($epp->marca ?? 'N/A')
                        . ' - Talle: ' . ($epp->talle ?? 'N/A') }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Stock Disponible</label>
                <input type="text" readonly class="form-control" id="stock_disponible" name="stock_disponible">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="mb-3">
                <label>Cantidad a entregar</label>
                <input type="number" min="0" class="form-control" id="cantidad_entrega" name="cantidad_entrega"
                    placeholder="Cantidad a entregar">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="mb-3">
                <label>&nbsp;</label>
                <input type="hidden" name="id_elemento" id="id_elemento">
                <input type="hidden" name="talle" id="talle">
                <input type="hidden" name="producto" id="producto">
                <input type="hidden" name="tipo" id="tipo">
                <input type="hidden" name="marca" id="marca">
                <button type="button" class="btn btn-primary form-control"
                    onclick="agregarElementoTable()">Agregar</button>
            </div>
        </div>
    </div>

    @if (isset($entrega))
        <input type="hidden" id="elementos_entrega" name="elementos_entrega"
        value="{{ $entrega->elementos_entrega }}">
    @else
        <input type="hidden" id="elementos_entrega" name="elementos_entrega" value="[]">
    @endif
    <div class="row mt-3">
        <div class="col-12">
            <h5>Elementos a agregar</h5>
            <table class="table table-bordered" id="table-entrega">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Producto</th>
                        <th>Tipo/Modelo</th>
                        <th>Marca</th>
                        <th>Cantidad Disponible</th>
                        <th>Cantidad a Entregar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($entrega))
                    @foreach ($entrega->filas as $key => $fila)
                    <tr id="row_{{ $key }}">
                        <td>{{ $fila->elemento->id }}</td>
                        <td>{{ $fila->elemento->producto }}</td>
                        <td>{{ $fila->elemento->tipo }}</td>
                        <td>{{ $fila->elemento->marca }}</td>
                        <td>{{ $fila->elemento->stock + $fila->cantidad }}</td>
                        <td>{{ $fila->cantidad }}</td>
                        <td><button type="button" class="btn btn-danger"
                                onclick="eliminarElementoTableEditar(this, {{ $fila->id }}, '{{$fila->cantidad}}', '{{$fila->elemento->producto}}', '{{$fila->elemento->tipo}}', '{{$fila->elemento->marca}}', '{{$fila->elemento->talle}}')">Eliminar</button>
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