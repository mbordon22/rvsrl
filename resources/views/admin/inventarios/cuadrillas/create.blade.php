@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-2">Nueva Cuadrilla</h4>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-sm-12">
                <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventarios.cuadrillas.index') }}">Cuadrillas</a></li>
                    <li class="breadcrumb-item active">Nuevo</li>
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
                        <form class="row g-3 custom-input" id="CuadrillaForm"
                            action="{{ route('admin.inventarios.cuadrillas.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @include('admin.inventarios.cuadrillas.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script>
@endsection