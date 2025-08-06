@extends('layouts.simple.master')

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h2>Reporte de Entrega de EPP</h2>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Entregas de EPP</li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Detalles Entrega EPP</h4>
                    <p><strong>Fecha:</strong> {{ $entrega->fecha }}</p>
                    <p><strong>Usuario:</strong> {{ $entrega->empleado->first_name . " " . $entrega->empleado->last_name }}
                    </p>

                    <h4 class="mb-3">Listado de EPP Entregados:</h4>
                    <table class="table table-bordered" id="table-entrega">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Tipo/Modelo</th>
                                <th>Marca</th>
                                <th>Cantidad Entregada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entrega->filas as $key => $fila)
                            <tr id="row_{{ $key }}">
                                <td>{{ $fila->elemento->producto }}</td>
                                <td>{{ $fila->elemento->tipo }}</td>
                                <td>{{ $fila->elemento->marca }}</td>
                                <td>{{ $fila->cantidad }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <a href="{{ route('admin.epp.entregas.reportPDF', $entrega->id) }}"
                            class="btn btn-primary">Descargar PDF</a>
                        <a href="{{ route('admin.epp.entregas.index') }}" class="btn btn-light">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection