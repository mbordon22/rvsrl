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
                    <h4>Gestión de usuarios</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Gestión de usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xxl-2 col-lg-4 box-col-4">
                <div class="card user-management">
                    <div class="card-body bg-primary">
                        <div class="blog-tags">
                            <div class="tags-icon">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                                </svg>
                            </div>
                            <div class="tag-details">
                                <h2 class="total-num counter">{{ sprintf("%02d",Spatie\Permission\Models\Role::where('system_reserve', false)->count()) }}</h2>
                                <p>Total Roles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-lg-8 box-col-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Total de usuarios por rol</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="total-num counter">
                                    @php
                                        $roles = Spatie\Permission\Models\Role::where('system_reserve', false)->with('users')->latest()->take(7)->get();
                                    @endphp
                                    <div class="d-flex by-role custom-scrollbar">
                                        @foreach ($roles as $role)
                                            <div>
                                                <div class="total-user bg-light-primary">
                                                    <h5> {{ $role->name }} </h5>
                                                    <span class="total-num counter">{{ sprintf("%02d",$role->users->count()) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @can('user.create')
            <div class="col-xxl-2 col-sm-6 box-col-6">
                <div class="card user-role">
                    <div class="card-body border-b-primary border-2">
                        <div class="upcoming-box">
                            <div class="upcoming-icon bg-primary">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#user-plus') }}"></use>
                                </svg>
                            </div>
                            <p>Usuario</p>
                            <a href="{{ route('admin.user.create') }}" class="btn btn-primary">Nuevo Usuario</a>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can('role.create')
            <div class="col-xxl-2 col-sm-6 box-col-6">
                <div class="card user-role">
                    <div class="card-body border-b-secondary border-2">
                        <div class="upcoming-box">
                            <div class="upcoming-icon bg-secondary">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-social') }}"></use>
                                </svg>
                            </div>
                            <p>Rol</p>
                            <a href="{{ route('admin.role.create') }}" class="btn btn-secondary">{{ __('Nuevo Rol') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endauth
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
