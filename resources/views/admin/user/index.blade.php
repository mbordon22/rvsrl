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
                    <h4 class="mb-2">Gestión de usuarios</h4>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Gestión de usuarios</li>
                    </ol>
                </div>
                @can('user.create')
                    <div class="col-sm-4 text-end">
                        <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
                            Nuevo Usuario
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
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Buscar</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i data-feather="search"></i></span>
                                            <input type="text" id="userSearch" class="form-control bg-white"
                                                placeholder="Nombre, apellido o DNI...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3" id="userLimpiarFiltrosWrap" style="display:none;">
                                        <button type="button" id="userLimpiarFiltros" class="btn btn-outline-secondary d-flex">
                                            <i data-feather="x"></i> Limpiar filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="user-table">
                            <div class="table-responsive p-3" style="position: relative;">
                                {!! $dataTable->table() !!}
                                <div id="userLoadingSpinner" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
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
        function toggleLimpiar() {
            $('#userLimpiarFiltrosWrap').toggle($('#userSearch').val() !== '');
        }
        function redraw() {
            if (window.LaravelDataTables && window.LaravelDataTables['user-table']) {
                window.LaravelDataTables['user-table'].draw();
            }
        }
        if (window.LaravelDataTables && window.LaravelDataTables['user-table']) {
            window.LaravelDataTables['user-table'].on('processing', function (e, settings, processing) {
                if (processing) {
                    $('#user-table').css('opacity', '0.2');
                    $('#userLoadingSpinner').show();
                } else {
                    $('#user-table').css('opacity', '1');
                    $('#userLoadingSpinner').hide();
                }
            });
        }
        $('#userSearch').on('keyup', function () {
            toggleLimpiar();
            clearTimeout(timer);
            timer = setTimeout(redraw, 350);
        });
        $('#userLimpiarFiltros').on('click', function () {
            $('#userSearch').val('');
            toggleLimpiar();
            redraw();
        });
        toggleLimpiar();
    });
</script>
@endsection
