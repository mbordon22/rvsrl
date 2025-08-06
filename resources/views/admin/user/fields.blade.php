<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Nombre<span> *</span></label>
                <input class="form-control" type="text" name="first_name"
                    value="{{ isset($user->first_name) ? $user->first_name : old('first_name') }}"
                    placeholder="Ingrese su nombre">
                @error('first_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Apellido<span> *</span></label>
                <input class="form-control" type="text" name="last_name"
                    value="{{ isset($user->last_name) ? $user->last_name : old('last_name') }}"
                    placeholder="Ingrese su apellido">
                @error('last_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Telefono</label>
                <input class="form-control" type="number" name="phone"
                    value="{{ isset($user->phone) ? $user->phone : old('phone') }}" placeholder="Ingrese su telefono">
                @error('phone')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Fecha de Nacimiento</label>
                <input class="datepicker-here form-control" type="text" name="dob"
                    value="{{ isset($user->dob) ? $user->dob : old('dob') }}" data-language="es"
                    placeholder="Ingrese su fecha de nacimiento" >
                @error('dob')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Número de DNI</label>
                <input class="form-control" type="text" name="dni"
                    value="{{ isset($user->dni) ? $user->dni : old('dni') }}" placeholder="Ingrese su DNI">
                @error('dni')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Estado</label>
                <select class="form-select" name="status">
                    <option value="1" {{ !($user->status ?? 1) == 0 ? 'selected' : '' }}>{{ __('Habilitado') }}
                    </option>
                    <option value="0" {{ ($user->status ?? 1) == 0 ? 'selected' : '' }}>{{ __('Deshabilitado') }}
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Domicilio</label>
                <input class="form-control" type="text" name="location"
                    value="{{ isset($user->location) ? $user->location : old('location') }}" placeholder="Ingrese su domicilio">
                @error('location')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Codigo Postal</label>
                <input class="form-control" type="number" name="postal_code"
                    value="{{ isset($user->postal_code) ? $user->postal_code : old('postal_code') }}"
                    placeholder="Ingrese su codigo postal">
                @error('postal_code')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Email <span> *</span></label>
                <input class="form-control" type="email" id="email"
                    value="{{ isset($user->email) ? $user->email : old('email') }}" name="email"
                    placeholder="Ingrese su email">
                @error('email')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Rol <span> *</span></label>
                <select class="form-select" name="role_id">
                    <option value="" selected disabled hidden>Seleccionar Rol</option>
                    @foreach ($roles as $key => $role)
                        <option value="{{ $role->id }}"
                            @if (isset($user->roles)) @selected(old('role_id', $user->roles->pluck('id')->first()) == $role->id) @endif>{{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        @if(isset($user))
            <hr>
            <h5 class="mb-3">Dejar en blanco si no desea cambiar la contraseña</h5>
        @endif
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Contraseña <span> *</span></label>
                <input class="form-control" type="password" id="password" name="password" placeholder="Ingrese su contraseña"
                    autocomplete="off">
                @error('password')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Confirme contraseña <span> *</span></label>
                <input class="form-control" type="password" id="confirm_password" name="confirm_password"
                    placeholder="Ingrese su contraseña nuevamente" autocomplete="off">
                @error('confirm_password')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Fecha de Ingreso a la empresa</label>
                <input class="datepicker-here form-control" type="text" name="admission_date"
                    value="{{ isset($user->admission_date) ? $user->admission_date : old('admission_date', now()->format('d/m/Y')) }}" data-language="es"
                    placeholder="Ingrese la fecha de ingreso" >
                @error('admission_date')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>ID Telecom</label>
                <input class="form-control" type="text" name="telecom_id"
                    value="{{ isset($user->telecom_id) ? $user->telecom_id : old('telecom_id') }}" placeholder="Ingrese ID Telecom">
                @error('telecom_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="mb-3">
                @php
                    $image = $user->getFirstMedia('image');
                @endphp
                <label>Imagen de perfil</label>
                <input class="form-control" type="file" name="image">

                @isset($user)
                    <div class="mt-3 comman-image">
                        @if ($image)
                            <img src="{{ $image->getUrl() }}" alt="Image" class="img-thumbnail img-fix" height="50%"
                                width="50%">
                            <div class="dz-preview">
                                <a href="{{ route('admin.user.removeImage', $user?->id) }}" class="dz-remove text-danger"
                                    data-bs-target="#tooltipmodal" data-bs-toggle="modal">Eliminar</a>
                            </div>
                        @endif
                    </div>

                    <!-- Remove File Confirmation-->
                    <div class="modal fade" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Eliminar</h4>
                                    <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><b>¿Seguro que quieres eliminar?</b></p>
                                    <p>Este elemento se eliminará permanentemente. No se puede deshacer esta acción.</p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Cerrar</button>
                                    @if ($user->id)
                                        <a href="{{ route('admin.user.removeImage', $user->id) }}"
                                            class="btn btn-danger">Eliminar</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label>Mas Información</label>
                <textarea class="form-control" id="exampleFormControlTextarea4" rows="2" name="about_me"
                    placeholder="Mas información">{{ isset($user->about_me) ? $user->about_me : old('about_me') }}</textarea>
                @error('about_me')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="text-end">
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
