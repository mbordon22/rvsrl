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
            <div class="col-6">
                <h3>Cargas Combustible de {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - Patente: {{ $vehiculo->patente }}</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.vehiculo.index')}}">Vehículos</a></li>
                    <li class="breadcrumb-item active">Combustible</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    @can('combustible.create')
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCombustibleModal">
        Añadir Carga Combustible
    </button>
    @endcan
    <a href="{{route('admin.vehiculo.index')}}" class="btn btn-light mb-3">
        Volver a Vehículos
    </a>
    
    <!-- Modals -->
    @include('admin.vehiculo.combustible.create')
    @include('admin.vehiculo.combustible.edit')
    @include('admin.vehiculo.combustible.view_files')
    <!-- /Modals -->
    
    <div class="row">
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
@endsection