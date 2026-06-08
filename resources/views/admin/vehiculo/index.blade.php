@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid basic_table">
        <div class="page-title">
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-2">Gestión de vehículos</h4>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Gestión de vehículos</li>
                    </ol>
                </div>
                @can('vehiculo.create')
                    <div class="col-sm-4 text-end">
                        <a href="{{ route('admin.vehiculo.create') }}" class="btn btn-primary">
                            Nuevo Vehículo
                        </a>
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
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Buscar</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i data-feather="search"></i></span>
                                            <input type="text" id="vehiculoSearch" class="form-control bg-white"
                                                placeholder="Marca, modelo o patente...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Identificador</label>
                                        <input type="text" id="vehiculoIdentificador" class="form-control bg-white"
                                            placeholder="Identificador del vehículo...">
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Tipo de vehículo</label>
                                        <select id="vehiculoTipoVehiculo" class="form-select bg-white">
                                            <option value="">Todos</option>
                                            @foreach ($tipos_vehiculo as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->tipo_vehiculo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Tipo de combustible</label>
                                        <select id="vehiculoTipoCombustible" class="form-select bg-white">
                                            <option value="">Todos</option>
                                            @foreach ($tipos_combustible as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->tipo_combustible }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3" id="vehiculoLimpiarFiltrosWrap" style="display:none;">
                                        <button type="button" id="vehiculoLimpiarFiltros" class="btn btn-outline-secondary d-flex">
                                            <i data-feather="x"></i> Limpiar filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="user-table">
                            <div class="table-responsive p-3" style="position: relative;">
                                {!! $dataTable->table() !!}
                                <div id="vehiculoLoadingSpinner" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
                                    <div class="loader">Cargando...</div>
                                </div>
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

{!! $dataTable->scripts() !!}
<script>
    $(function () {
        if (window.feather) { feather.replace(); }
        var timer = null;
        var $filtros = $('#vehiculoSearch, #vehiculoIdentificador, #vehiculoTipoVehiculo, #vehiculoTipoCombustible');
        function hayFiltrosActivos() {
            return $filtros.toArray().some(function (el) {
                return $(el).val() !== '';
            });
        }
        function toggleLimpiar() {
            $('#vehiculoLimpiarFiltrosWrap').toggle(hayFiltrosActivos());
        }
        function redraw() {
            if (window.LaravelDataTables && window.LaravelDataTables['vehiculo-table']) {
                window.LaravelDataTables['vehiculo-table'].draw();
            }
        }
        if (window.LaravelDataTables && window.LaravelDataTables['vehiculo-table']) {
            window.LaravelDataTables['vehiculo-table'].on('processing', function (e, settings, processing) {
                if (processing) {
                    $('#vehiculo-table').css('opacity', '0.2');
                    $('#vehiculoLoadingSpinner').show();
                } else {
                    $('#vehiculo-table').css('opacity', '1');
                    $('#vehiculoLoadingSpinner').hide();
                }
            });
        }
        $('#vehiculoSearch, #vehiculoIdentificador').on('keyup', function () {
            toggleLimpiar();
            clearTimeout(timer);
            timer = setTimeout(redraw, 350);
        });
        $('#vehiculoTipoVehiculo, #vehiculoTipoCombustible').on('change', function () {
            toggleLimpiar();
            redraw();
        });
        $('#vehiculoLimpiarFiltros').on('click', function () {
            $('#vehiculoSearch, #vehiculoIdentificador').val('');
            $('#vehiculoTipoVehiculo, #vehiculoTipoCombustible').val('');
            toggleLimpiar();
            redraw();
        });
        toggleLimpiar();
    });
</script>
@endsection
