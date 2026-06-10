@extends('layouts.authentication.master')

@section('css')
    <style>
        /* Reemplaza el texto "show"/"hide" del template por iconos de ojo (solo en login) */
        .login-card .show-hide span:before {
            content: none !important;
        }

        .login-card .show-hide span {
            display: inline-flex;
            align-items: center;
            line-height: 0;
        }

        .login-card .show-hide span svg {
            width: 18px;
            height: 18px;
            color: var(--theme-deafult);
        }

        /* Contraseña oculta (clase .show presente) -> mostramos el ojo abierto */
        .login-card .show-hide span.show .icon-eye-off {
            display: none;
        }

        /* Contraseña visible (sin clase .show) -> mostramos el ojo tachado */
        .login-card .show-hide span:not(.show) .icon-eye {
            display: none;
        }
    </style>
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
                                <p>Ingrese su DNI y contraseña para iniciar sesión</p>
                                <div class="form-group">

                                    <label class="col-form-label">DNI</label>
                                    <input id="dni" type="text" class="form-control @error('dni') is-invalid @enderror" name="dni" value="{{ old('dni') }}" placeholder="Ingrese su DNI" required autocomplete="username" autofocus>

                                    @error('dni')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-form-label">Contraseña</label>
                                    <div class="form-input position-relative">
                                            <input id="password" type="password"  class="form-control @error('password') is-invalid @enderror" name="password" value=""  placeholder="Ingrese la contraseña" required autocomplete="current-password">
                                        <div class="show-hide">
                                            <span class="show">
                                                <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            </span>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group mb-0 text-end">
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