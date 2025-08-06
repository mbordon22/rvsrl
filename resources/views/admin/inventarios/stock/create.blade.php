@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Gestión de Stock</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Gestión de Stock</li>
                    <li class="breadcrumb-item active">Tipo Movimiento</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between items-center">
                    <h4>Seleccionar Tipo Movimiento</h4>
                    <a href="{{route('admin.inventarios.stock.index')}}" class="btn btn-light mb-3">
                        Volver a gestión de Stock
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        @can('gestion_stock.ingreso')
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body border-b-primary border-2">
                                    <div class="upcoming-box">
                                        <div class="upcoming-icon bg-primary">
                                            <i class="icon-import"></i> 
                                        </div>
                                        <p>Ingreso Materiales</p>
                                        <a href="{{ route('admin.inventarios.stock.createIngreso') }}" class="btn btn-primary">Nuevo Ingreso</a>
                                    </div>
                                    <ul class="bubbles role">
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endcan
                        @can('gestion_stock.egreso')
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body border-b-secondary border-2">
                                    <div class="upcoming-box">
                                        <div class="upcoming-icon bg-secondary">
                                            <i class="icon-export"></i> 
                                        </div>
                                        <p>Egreso Materiales</p>
                                        <a href="{{ route('admin.inventarios.stock.createEgreso') }}" class="btn btn-secondary">Nuevo Egreso</a>
                                    </div>
                                    <ul class="bubbles role">
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endcan
                        @can('gestion_stock.transferencia')
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body border-b-success border-2">
                                    <div class="upcoming-box">
                                        <div class="upcoming-icon bg-success">
                                            <i class="icon-exchange-vertical"></i> 
                                        </div>
                                        <p>Transferencia Materiales</p>
                                        <a href="{{ route('admin.inventarios.stock.createTransferencia') }}" class="btn btn-success">Nueva Transferencia</a>
                                    </div>
                                    <ul class="bubbles role">
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endcan
                        @can('gestion_stock.ajuste')
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body border-b-info border-2">
                                    <div class="upcoming-box">
                                        <div class="upcoming-icon bg-info">
                                            <i class="icon-reload"></i> 
                                        </div>
                                        <p>Ajuste Stock</p>
                                        <a href="{{ route('admin.inventarios.stock.createAjuste') }}" class="btn btn-info">Nuevo Ajuste</a>
                                    </div>
                                    <ul class="bubbles role">
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
@endsection