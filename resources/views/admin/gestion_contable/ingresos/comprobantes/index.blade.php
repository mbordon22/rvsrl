@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid basic_table">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Comprobantes Ingresos</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Comprobantes Ingresos</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="w-full d-flex justify-content-between">
                @can('ingresos.create')
                <div class="col-xxl-2 col-sm-6 box-col-6">
                    <div class="card user-role">
                        <div class="card-body border-b-primary border-2">
                            <div class="upcoming-box">
                                <a href="{{ route('admin.gestion-contable.ingresos.comprobantes.create') }}" class="btn btn-primary">Nuevo Comprobante</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
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
@endsection

@section('scripts')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>

{!! $dataTable->scripts() !!}
@endsection
