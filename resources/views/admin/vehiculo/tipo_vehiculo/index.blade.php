@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid basic_table">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Tipos de vehículo</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Tipos de vehículo</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="w-full d-flex justify-content-between">
                <div class="col-xxl-2 col-lg-4 box-col-4">
                    <div class="card user-management">
                        <div class="card-body bg-primary">
                            <div class="blog-tags">
                                <div class="tags-icon">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                                    </svg>
                                </div>
                                <div class="tag-details">
                                    <h2 class="total-num counter">{{ sprintf("%02d",App\Models\TipoVehiculo::all()->count()) }}</h2>
                                    <p>Total Tipos de Vehículo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-sm-6 box-col-6">
                    <div class="card user-role">
                        <div class="card-body border-b-primary border-2">
                            <div class="upcoming-box">
                                <div class="upcoming-icon bg-primary">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#user-plus') }}"></use>
                                    </svg>
                                </div>
                                <p>Tipos de Vehiculo</p>
                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTipoVehiculoModal">
                                    Nuevo Tipo de Vehiculo
                                </button>
                            </div>
                        </div>
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

    @include('admin.vehiculo.tipo_vehiculo.create')
@endsection

@section('scripts')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
{!! $dataTable->scripts() !!}
@include('admin.vehiculo.tipo_vehiculo.scripts')
@endsection
