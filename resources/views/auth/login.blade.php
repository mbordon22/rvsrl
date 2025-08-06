@extends('layouts.authentication.master')

@section('css')
@endsection

@section('main_content')
    <!-- login page start-->
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card login-dark">
                    <div>
                        <div>
                            <a class="logo" href="{{ route('admin.dashboard') }}">
                                <img class="img-fluid for-dark" style="height: 150px" src="{{ asset('assets/images/logo/logo_rv.png') }}" alt="paginadelogin">
                                <img class="img-fluid for-light" style="height: 150px" src="{{ asset('assets/images/logo/logo_rv.png') }}" alt="paginadelogin">
                            </a>
                        </div>
                        <div class="login-main">
                            <form class="theme-form" method="POST" action="{{ route('login') }}">
                                @csrf
                                <h4>Iniciar sesión</h4>
                                <p>Ingrese su correo electrónico y contraseña para iniciar sesión</p>
                                <div class="form-group">

                                    <label class="col-form-label">Correo Electrónico</label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="" placeholder="Test@gmail.com" required autocomplete="email" autofocus>
                                    
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-form-label">Contraseña</label>
                                    <div class="form-input position-relative">
                                            <input id="password" type="password"  class="form-control @error('password') is-invalid @enderror" name="password" value=""  placeholder="Ingrese la contraseña" required autocomplete="current-password">
                                        <div class="show-hide"> <span class="show"></span></div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group mb-0 text-end">
                                    @if (Route::has('password.request'))
                                        <a class="checkbox1" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                                    @endif
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Iniciar sesión</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection