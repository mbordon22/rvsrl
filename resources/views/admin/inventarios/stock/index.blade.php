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
                    <h4>Gestión de Stock Materiales</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Gestión de Stock Materiales</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xxl-10 col-sm-6 box-col-6">
                <div class="card">
                    <div class="card-body border-b-primary border-2">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="tipo_filter">Filtrar por Tipo:</label>
                                <select id="tipo_filter" class="form-select js-example-basic-single">
                                    <option value="">Todos</option>
                                    <option value="almacen">Almacén</option>
                                    <option value="cuadrilla">Cuadrilla</option>
                                </select>
                            </div>
                            <div class="col-sm-4 d-none" id="almacen_filter_div">
                                <label for="almacen_filter">Filtrar por Almacén:</label>
                                <select id="almacen_filter" class="form-select js-example-basic-single">
                                    <option value="">Todos</option>
                                    @foreach ($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4 d-none" id="cuadrilla_filter_div">
                                <label for="cuadrilla_filter">Filtrar por Cuadrilla:</label>
                                <select id="cuadrilla_filter" class="form-select js-example-basic-single">
                                    <option value="">Todos</option>
                                    @foreach ($cuadrillas as $cuadrilla)
                                        <option value="{{ $cuadrilla->id }}">{{ $cuadrilla->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="material_filter">Filtrar por Material:</label>
                                <select id="material_filter" class="form-select js-example-basic-single">
                                    <option value="">Todos</option>
                                    @foreach ($materiales as $material)
                                        <option value="{{ $material->id }}">{{ $material->codigo . ' - ' . $material->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <div class="media">
                                    <label class="col-form-label m-r-10">Sólo mostrar stock bajo: </label>
                                    <div class="media-body text-end icon-state switch-outline">
                                        <label class="switch mb-0">
                                            <input type="checkbox" id="stock_bajo"><span class="switch-state bg-primary"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                &nbsp;
                            </div>
                            <div class="col-sm-4 text-end">
                                <button class="btn btn-secondary" id="clear-filters">Limpiar Filtros</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @canany(['gestion_stock.ingreso', 'gestion_stock.egreso', 'gestion_stock.transferencia', 'gestion_stock.ajuste'])
            <div class="col-xxl-2 col-sm-6 box-col-6">
                <div class="card user-role">
                    <div class="card-body border-b-primary border-2">
                        <div class="upcoming-box">
                            <div class="upcoming-icon bg-primary">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#user-plus') }}"></use>
                                </svg>
                            </div>
                            <p>Stock Materiales</p>
                            <a href="{{ route('admin.inventarios.stock.create') }}" class="btn btn-primary">Nuevo Movimiento</a>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block row">
                        <div class="user-table">
                            <div class="table-responsive p-3">
                                {!! $dataTable->table() !!}
                                <div id="loading-spinner" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
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
<script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2-custom.js') }}"></script>
<script src="{{ asset('assets/js/height-equal.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>
    function buildUrl() {
        const almacenId = $('#almacen_filter').val();
        const cuadrillaId = $('#cuadrilla_filter').val();
        const materialId = $('#material_filter').val();
        const tipo = $('#tipo_filter').val();
        const stockBajo = $('#stock_bajo').is(':checked') ? '1' : '';

        let url = '/admin/inventarios/stock?';

        if (almacenId) url += 'almacen_id=' + almacenId + '&';
        if (cuadrillaId) url += 'cuadrilla_id=' + cuadrillaId + '&';
        if (materialId) url += 'material_id=' + materialId + '&';
        if (tipo) url += 'tipo=' + tipo + '&';
        if (stockBajo) url += 'stock_bajo=' + stockBajo;

        return url;
    }

    $(document).ready(function() {
        const table = $('#stock-material-table').DataTable();

        // Show spinner overlay on processing
        table.on('processing', function(e, settings, processing) {
            if (processing) {
                $('#stock-material-table').css('opacity', '0.2');
                $('#loading-spinner').show();
            } else {
                $('#stock-material-table').css('opacity', '1');
                $('#loading-spinner').hide();
            }
        });

        $('#almacen_filter').on('change', function() {
            const url = buildUrl();
            if(!url){
                return;
            }
            table.ajax.url(url).load();
        });

        $('#cuadrilla_filter').on('change', function() {
            const url = buildUrl();
            if(!url){
                return;
            }
            table.ajax.url(url).load();
        });

        $('#material_filter').on('change', function() {
            const url = buildUrl();
            if(!url){
                return;
            }
            table.ajax.url(url).load();
        });

        $('#tipo_filter').on('change', function() {
            const tipo = $(this).val();
            if (tipo === 'almacen') {
                $('#almacen_filter_div').removeClass('d-none');
                $('#cuadrilla_filter_div').addClass('d-none');
            } else if (tipo === 'cuadrilla') {
                $('#almacen_filter_div').addClass('d-none');
                $('#cuadrilla_filter_div').removeClass('d-none');
            } else {
                $('#almacen_filter_div').addClass('d-none');
                $('#cuadrilla_filter_div').addClass('d-none');
            }
            const url = buildUrl();
            if(!url){
                return;
            }
            table.ajax.url(url).load();
        });

        $('#stock_bajo').on('change', function() {
            table.ajax.url(buildUrl()).load();
        });

        $('#clear-filters').on('click', function() {
            $('#almacen_filter').val('');
            $('#cuadrilla_filter').val('');
            $('#material_filter').val('');
            $('#tipo_filter').val('');
            $('#stock_bajo').prop('checked', false);

            $('#almacen_filter').select2('destroy').select2();
            $('#cuadrilla_filter').select2('destroy').select2();
            $('#material_filter').select2('destroy').select2();
            $('#tipo_filter').select2('destroy').select2();

            $('#almacen_filter_div').addClass('d-none');
            $('#cuadrilla_filter_div').addClass('d-none');
            table.ajax.url(buildUrl()).load();
        });
    });
</script>
@endsection
