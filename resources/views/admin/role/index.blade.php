@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollable.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid basic_table">
        <div class="page-title">
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-2">Gestión de Roles</h4>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Gestión de Roles</li>
                    </ol>
                </div>
                @can('role.create')
                    <div class="col-sm-4 text-end">
                        <a href="{{ route('admin.role.create') }}" class="btn btn-primary">{{ __('Nuevo Rol') }}</a>
                    </div>
                @endcan
            </div>
        </div>
        <div class="row">
            <!-- Container-fluid starts-->
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
                                            <input type="text" id="roleSearch" class="form-control bg-white"
                                                placeholder="Nombre del rol...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3" id="roleLimpiarFiltrosWrap" style="display:none;">
                                        <button type="button" id="roleLimpiarFiltros" class="btn btn-outline-secondary d-flex">
                                            <i data-feather="x"></i> Limpiar filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="role-table">
                            <div class="table-responsive p-3" style="position: relative;">
                                {!! $dataTable->table() !!}
                                <div id="roleLoadingSpinner" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
                                    <div class="loader">Cargando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Container-fluid Ends-->
        </div>
    </div>
@endsection

@section('scripts')
    <!-- calendar js-->
    <script src="{{ asset('assets/js/scrollable/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/scrollable/scrollable-custom.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
{!! $dataTable->scripts() !!}
<script>
    $(function () {
        if (window.feather) { feather.replace(); }
        var timer = null;
        function toggleLimpiar() {
            $('#roleLimpiarFiltrosWrap').toggle($('#roleSearch').val() !== '');
        }
        function redraw() {
            if (window.LaravelDataTables && window.LaravelDataTables['role-table']) {
                window.LaravelDataTables['role-table'].draw();
            }
        }
        if (window.LaravelDataTables && window.LaravelDataTables['role-table']) {
            window.LaravelDataTables['role-table'].on('processing', function (e, settings, processing) {
                if (processing) {
                    $('#role-table').css('opacity', '0.2');
                    $('#roleLoadingSpinner').show();
                } else {
                    $('#role-table').css('opacity', '1');
                    $('#roleLoadingSpinner').hide();
                }
            });
        }
        $('#roleSearch').on('keyup', function () {
            toggleLimpiar();
            clearTimeout(timer);
            timer = setTimeout(redraw, 350);
        });
        $('#roleLimpiarFiltros').on('click', function () {
            $('#roleSearch').val('');
            toggleLimpiar();
            redraw();
        });
        toggleLimpiar();
    });
</script>
@endsection
