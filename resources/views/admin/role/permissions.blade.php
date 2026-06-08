@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-2">Permisos del rol: {{ $role->name }}</h4>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-sm-12">
                    <ol class="breadcrumb mb-0" style="justify-content:flex-start;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Permisos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar permisos</h4>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 custom-input" id="rolePermissionsForm"
                            action="{{ route('admin.role.updatePermissions', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('admin.role._permissions')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
