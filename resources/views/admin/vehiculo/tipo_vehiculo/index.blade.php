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
                <div class="col-12">
                    <h4 class="mb-2">Tipos de vehículo</h4>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.vehiculo.index') }}">Vehículos</a></li>
                        <li class="breadcrumb-item active">Tipos de vehículo</li>
                    </ol>
                </div>
                @can('vehiculo.create')
                    <div class="col-sm-4 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTipoVehiculoModal">
                            Nuevo Tipo de Vehículo
                        </button>
                    </div>
                @endcan
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block row">
                        <div class="col-12">
                            <div class="bg-light border rounded p-3 m-3 mb-0">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Buscar</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i data-feather="search"></i></span>
                                            <input type="text" id="tipoVehiculoSearch" class="form-control bg-white"
                                                placeholder="Tipo de vehículo...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3" id="tipoVehiculoLimpiarFiltrosWrap" style="display:none;">
                                        <button type="button" id="tipoVehiculoLimpiarFiltros" class="btn btn-outline-secondary d-flex">
                                            <i data-feather="x"></i> Limpiar filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="user-table">
                            <div class="table-responsive p-3" style="position: relative;">
                                {!! $dataTable->table() !!}
                                <div id="tipoVehiculoLoadingSpinner" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
                                    <div class="loader">Cargando...</div>
                                </div>
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
<script>
    $(function () {
        if (window.feather) { feather.replace(); }
        var timer = null;
        function toggleLimpiar() {
            $('#tipoVehiculoLimpiarFiltrosWrap').toggle($('#tipoVehiculoSearch').val() !== '');
        }
        function redraw() {
            if (window.LaravelDataTables && window.LaravelDataTables['tipovehiculo-table']) {
                window.LaravelDataTables['tipovehiculo-table'].draw();
            }
        }
        if (window.LaravelDataTables && window.LaravelDataTables['tipovehiculo-table']) {
            window.LaravelDataTables['tipovehiculo-table'].on('processing', function (e, settings, processing) {
                if (processing) {
                    $('#tipovehiculo-table').css('opacity', '0.2');
                    $('#tipoVehiculoLoadingSpinner').show();
                } else {
                    $('#tipovehiculo-table').css('opacity', '1');
                    $('#tipoVehiculoLoadingSpinner').hide();
                }
            });
        }
        $('#tipoVehiculoSearch').on('keyup', function () {
            toggleLimpiar();
            clearTimeout(timer);
            timer = setTimeout(redraw, 350);
        });
        $('#tipoVehiculoLimpiarFiltros').on('click', function () {
            $('#tipoVehiculoSearch').val('');
            toggleLimpiar();
            redraw();
        });
        toggleLimpiar();
    });
</script>
@endsection
