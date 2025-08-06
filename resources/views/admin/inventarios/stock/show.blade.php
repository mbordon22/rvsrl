@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/intltelinput.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/tagify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid basic_table">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Gestion de Stock - Historial Movimientos</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.inventarios.stock.index') }}">Gestión de Stock</a></li>
                        <li class="breadcrumb-item active">Historial Movimientos</li>
                    </ol>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between items-center">
                        <h4>Detalles del Material</h4>
                        <a href="{{route('admin.inventarios.stock.index')}}" class="btn btn-light">
                            Volver a gestión de Stock
                        </a>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="icofont icofont-arrow-right"> </i><span class="f-w-700">MATERIAL:</span> {{ $stockMaterial->material->descripcion }}</li>
                            <li class="list-group-item"><i class="icofont icofont-arrow-right"> </i><span class="f-w-700">CÓDIGO:</span> {{ $stockMaterial->material->codigo }}</li>
                            @if($stockMaterial->almacen)
                                <li class="list-group-item"><i class="icofont icofont-arrow-right"> </i><span class="f-w-700">ALMACÉN:</span> {{ $stockMaterial->almacen->nombre }}</li>
                            @endif
                            @if($stockMaterial->cuadrilla)
                                <li class="list-group-item"><i class="icofont icofont-arrow-right"> </i><span class="f-w-700">CUADRILLA:</span> {{ $stockMaterial->cuadrilla->nombre }}</li>
                            @endif
                            <li class="list-group-item"><i class="icofont icofont-arrow-right"> </i><span class="f-w-700">STOCK ACTUAL:</span> {{ $stockMaterial->cantidad }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block row">
                        <div class="user-table">
                            <div class="table-responsive p-3">
                                {!! $dataTable->table() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2-custom.js') }}"></script>
<script src="{{ asset('assets/js/height-equal.js') }}"></script>

{!! $dataTable->scripts() !!}
@endsection
