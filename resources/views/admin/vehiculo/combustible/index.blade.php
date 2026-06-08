@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/dropzone.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-2">Cargas Combustible de {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - Patente: {{ $vehiculo->patente }}</h4>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-sm-8">
                <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.vehiculo.index')}}">Vehículos</a></li>
                    <li class="breadcrumb-item active">Combustible</li>
                </ol>
            </div>
            <div class="col-sm-4 text-end">
                @can('combustible.create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCombustibleModal">
                        Añadir Carga Combustible
                    </button>
                @endcan
                <a href="{{route('admin.vehiculo.index')}}" class="btn btn-light">
                    Volver a Vehículos
                </a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <!-- Modals -->
    @include('admin.vehiculo.combustible.create')
    @include('admin.vehiculo.combustible.edit')
    @include('admin.vehiculo.combustible.view_files')
    <!-- /Modals -->
    
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-block row">
                    <div class="col-12">
                        <div class="bg-light border rounded p-3 m-3 mb-0">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Usuario</label>
                                    <select id="combUserFilter" class="form-select bg-white">
                                        <option value="">Todos los usuarios</option>
                                        @foreach ($usuarios as $user)
                                            <option value="{{ $user->id }}">{{ $user->first_name . ' ' . $user->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Fecha desde</label>
                                    <input type="text" id="combFechaDesde" class="form-control bg-white" data-language="es"
                                        placeholder="dd/mm/aaaa" autocomplete="off" readonly>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label mb-1 fw-semibold text-muted small text-uppercase">Fecha hasta</label>
                                    <input type="text" id="combFechaHasta" class="form-control bg-white" data-language="es"
                                        placeholder="dd/mm/aaaa" autocomplete="off" readonly>
                                </div>
                                <div class="col-md-6 col-lg-2" id="combLimpiarFiltrosWrap" style="display:none;">
                                    <button type="button" id="combLimpiarFiltros" class="btn btn-outline-secondary d-flex">
                                        <i data-feather="x"></i> Limpiar filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="user-table">
                        <div class="table-responsive p-3" style="position: relative;">
                            {!! $dataTable->table() !!}
                            <div id="combLoadingSpinner" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
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
<!-- calendar js-->
<script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.es.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>
<script src="{{ asset('assets/js/dropzone/dropzone-script.js') }}"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-time-picker/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
{!! $dataTable->scripts() !!}
@include('admin.vehiculo.combustible.scripts')
<script>
    $(function () {
        var $modal = $('#addCombustibleModal');

        $modal.on('shown.bs.modal', function () {
            var $sel = $('#user_id');
            if (!$sel.hasClass('select2-hidden-accessible')) {
                $sel.select2({
                    dropdownParent: $modal,
                    placeholder: 'Seleccionar un usuario',
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function () { return 'No se encontraron usuarios'; },
                        searching: function () { return 'Buscando...'; }
                    }
                });
            }
        });

        // Al cerrar el modal, limpiar la selección para la próxima carga.
        $modal.on('hidden.bs.modal', function () {
            $('#user_id').val('').trigger('change');
        });

        /* ===================== Filtros del listado ===================== */
        var tableId = 'vehiculocombustible-table';
        function dt() {
            return window.LaravelDataTables && window.LaravelDataTables[tableId]
                ? window.LaravelDataTables[tableId] : null;
        }
        function redraw() { if (dt()) dt().draw(); }

        function hayFiltros() {
            return ($('#combUserFilter').val() || '') !== ''
                || $('#combFechaDesde').val() !== ''
                || $('#combFechaHasta').val() !== '';
        }
        function toggleLimpiar() {
            $('#combLimpiarFiltrosWrap').toggle(hayFiltros());
        }

        // Select2 de usuarios (no está dentro de un modal).
        $('#combUserFilter').select2({
            width: '100%',
            placeholder: 'Todos los usuarios',
            allowClear: true,
            language: {
                noResults: function () { return 'No se encontraron usuarios'; },
                searching: function () { return 'Buscando...'; }
            }
        });
        $('#combUserFilter').on('change', function () {
            toggleLimpiar();
            redraw();
        });

        // Datepickers desde / hasta (air-datepicker v2).
        $('#combFechaDesde, #combFechaHasta').datepicker({
            language: 'es',
            autoClose: true,
            dateFormat: 'dd/mm/yyyy',
            onSelect: function () {
                toggleLimpiar();
                redraw();
            }
        });

        // Limpiar filtros.
        $('#combLimpiarFiltros').on('click', function () {
            $('#combUserFilter').val('').trigger('change.select2');

            var dpDesde = $('#combFechaDesde').data('datepicker');
            var dpHasta = $('#combFechaHasta').data('datepicker');
            if (dpDesde) { dpDesde.clear(); } else { $('#combFechaDesde').val(''); }
            if (dpHasta) { dpHasta.clear(); } else { $('#combFechaHasta').val(''); }

            toggleLimpiar();
            redraw();
        });

        // Preloader mientras corre la consulta.
        if (dt()) {
            dt().on('processing', function (e, settings, processing) {
                if (processing) {
                    $('#' + tableId).css('opacity', '0.2');
                    $('#combLoadingSpinner').show();
                } else {
                    $('#' + tableId).css('opacity', '1');
                    $('#combLoadingSpinner').hide();
                }
            });
        }

        toggleLimpiar();
    });
</script>
@endsection