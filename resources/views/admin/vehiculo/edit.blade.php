@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-2">Editar Vehículo</h4>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-sm-12">
                <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vehiculo.index') }}">Vehículos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
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
                <div class="card-body">
                    <div class="form theme-form">
                        <form class="row g-3 custom-input" id="vehiculoForm"
                            action="{{ route('admin.vehiculo.update', $vehiculo->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.vehiculo.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/custom-validation/validation.js') }}?v={{ filemtime(public_path('assets/js/custom-validation/validation.js')) }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.es.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
@endsection
