@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/photoswipe.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Editar Perfil</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Usuarios</li>
                    <li class="breadcrumb-item active">Editar Perfil</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid starts-->
<form action="{{ route('admin.user.update-profile') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="container-fluid">
        <div class="edit-profile">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Mi Perfil</h4>
                            <div class="card-options"><a class="card-options-collapse" href="#"
                                    data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a
                                    class="card-options-remove" href="#" data-bs-toggle="card-remove"><i
                                        class="fe fe-x"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="profile-title">
                                    <div class="media">
                                        @php
                                        $image = auth()->user()->getFirstMedia('image');
                                        @endphp

                                        @isset($image)
                                        <img src="{{ $image->getUrl() }}" alt="Image" class="img-70 rounded-circle">
                                        @else
                                        <img src="{{ asset('assets/images/user/user.png') }}" alt="Image"
                                            class="img-70 rounded-circle">
                                        @endisset

                                        <div class="media-body">
                                            <h5 class="mb-1">
                                                {{ ucfirst(auth()->user()->first_name) . ' ' .
                                                ucfirst(auth()->user()->last_name) }}
                                            </h5>
                                            <p>{{auth()?->user()->role->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Imagen</label>
                                <input class="form-control form-control-sm" type="file" name="image">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" placeholder="correo@correo.com" name="email"
                                    value="{{ auth()->user()->email }}">
                                @error('email')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input class="form-control" type="password" name="password" placeholder="Contraseña">
                                @error('password')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input class="form-control" type="password" name="confirm_password"
                                    placeholder="Confirmar Contraseña">
                                @error('confirm_password')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Editar Perfil</h4>
                        <div class="card-options"><a class="card-options-collapse" href="#"
                                data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a
                                class="card-options-remove" href="#" data-bs-toggle="card-remove"><i
                                    class="fe fe-x"></i></a></div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Nombre<span> *</span></label>
                                    <input class="form-control" type="text" placeholder="Nombre" name="first_name"
                                        value="{{ ucfirst(auth()->user()?->first_name) }}">
                                    @error('first_name')
                                    <span class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Apellido<span> *</span></label>
                                    <input class="form-control" type="text" placeholder="Apellido" name="last_name"
                                        value="{{ ucfirst(auth()->user()?->last_name) }}">
                                    @error('last_name')
                                    <span class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Código Postal</label>
                                    <input class="form-control" type="number" placeholder="Código Postal"
                                        name="postal_code" value="{{ auth()->user()?->postal_code }}">
                                    @error('postal_code')
                                    <span class="text-danger">
                                        <strong>{{ $postal_code }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Domicilio</label>
                                    <input class="form-control" type="text" placeholder="Domicilio" name="address"
                                        value="{{ auth()->user()?->address }}">
                                    @error('address')
                                    <span class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Telefono <span> *</span></label>
                                    <div class="row phone-select-edit">
                                        <div class="col-12 ps-0">
                                            <input class="form-control" type="number" name="phone"
                                                value="{{ isset($user->phone) ? $user->phone : old('phone') }}"
                                                placeholder="Teléfono">
                                            @error('phone')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">localidad</label>
                                    <input class="form-control" type="text" placeholder="Localidad" name="location"
                                        value="{{ auth()->user()?->location }}">
                                    @error('location')
                                    <span class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div>
                                    <label class="form-label">Acerca de mi</label>
                                    <textarea class="form-control" rows="4" placeholder="Acerca de mi"
                                        name="about_me">{{ auth()->user()->about_me }}</textarea>
                                    @error('about_me')
                                    <span class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary" type="submit">Actualizar Perfil</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
<!-- Container-fluid Ends-->
@endsection

@section('scripts')
<script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
<!-- calendar js-->
<script src="{{ asset('assets/js/photoswipe/photoswipe.min.js') }}"></script>
<script src="{{ asset('assets/js/photoswipe/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('assets/js/photoswipe/photoswipe.js') }}"></script>
<script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script>
@endsection