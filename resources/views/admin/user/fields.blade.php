@php
    $isEdit = $user->exists;
@endphp

<div class="form theme-form w-100">

    {{-- ===================== Datos personales ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Datos personales</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="first_name">Nombre <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="text" id="first_name" name="first_name"
                    value="{{ isset($user->first_name) ? $user->first_name : old('first_name') }}"
                    placeholder="Ingrese su nombre">
                @error('first_name')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="last_name">Apellido <span class="text-danger">*</span></label>
                <input class="form-control bg-light bg-light bg-light" type="text" id="last_name" name="last_name"
                    value="{{ isset($user->last_name) ? $user->last_name : old('last_name') }}"
                    placeholder="Ingrese su apellido">
                @error('last_name')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="dni">Número de DNI <span class="text-danger">*</span></label>
                <input class="form-control bg-light bg-light bg-light" type="text" inputmode="numeric" id="dni" name="dni"
                    value="{{ isset($user->dni) ? $user->dni : old('dni') }}" placeholder="Ingrese su DNI">
                @error('dni')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="dob">Fecha de Nacimiento</label>
                <input class="datepicker-here form-control bg-light bg-light bg-light" type="text" id="dob" name="dob"
                    value="{{ isset($user->dob) ? $user->dob : old('dob') }}" data-language="es"
                    placeholder="Ingrese su fecha de nacimiento">
                @error('dob')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Contacto y domicilio ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Contacto y domicilio</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="email">Email</label>
                <input class="form-control bg-light bg-light" type="email" id="email" name="email"
                    value="{{ isset($user->email) ? $user->email : old('email') }}"
                    placeholder="Ingrese su email">
                @error('email')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="phone">Teléfono</label>
                <input class="form-control bg-light bg-light" type="text" inputmode="tel" id="phone" name="phone"
                    value="{{ isset($user->phone) ? $user->phone : old('phone') }}" placeholder="Ingrese su teléfono">
                @error('phone')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="location">Domicilio</label>
                <input class="form-control bg-light bg-light" type="text" id="location" name="location"
                    value="{{ isset($user->location) ? $user->location : old('location') }}"
                    placeholder="Ingrese su domicilio">
                @error('location')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="postal_code">Código Postal</label>
                <input class="form-control bg-light bg-light" type="text" inputmode="numeric" id="postal_code" name="postal_code"
                    value="{{ isset($user->postal_code) ? $user->postal_code : old('postal_code') }}"
                    placeholder="Ingrese su código postal">
                @error('postal_code')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Acceso y permisos ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Acceso y permisos</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="role_id">Rol <span class="text-danger">*</span></label>
                <select class="form-select bg-light bg-light" id="role_id" name="role_id">
                    <option value="" selected disabled hidden>Seleccionar Rol</option>
                    @foreach ($roles as $key => $role)
                        <option value="{{ $role->id }}"
                            @if (isset($user->roles)) @selected(old('role_id', $user->roles->pluck('id')->first()) == $role->id) @endif>{{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                @php $statusVal = old('status', $user->status ?? 1); @endphp
                <label for="status">Estado</label>
                <select class="form-select bg-light bg-light" id="status" name="status">
                    <option value="1" @selected($statusVal == 1)>{{ __('Habilitado') }}</option>
                    <option value="0" @selected($statusVal == 0)>{{ __('Deshabilitado') }}</option>
                </select>
            </div>
        </div>
    </div>

    @if ($isEdit)
        <div class="alert alert-light border d-flex align-items-center py-2" role="alert">
            <small class="mb-0">Dejá los campos de contraseña en blanco si no querés cambiarla.</small>
        </div>
    @endif
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="password">Contraseña @unless($isEdit)<span class="text-danger">*</span>@endunless</label>
                <div class="input-group">
                    <input class="form-control bg-light" type="password" id="password" name="password"
                        placeholder="Ingrese su contraseña" autocomplete="new-password">
                    <button class="btn btn-outline-secondary toggle-password" type="button"
                        data-target="password" tabindex="-1" aria-label="Mostrar contraseña">👁</button>
                </div>
                @error('password')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="confirm_password">Confirme contraseña @unless($isEdit)<span class="text-danger">*</span>@endunless</label>
                <div class="input-group">
                    <input class="form-control bg-light" type="password" id="confirm_password" name="confirm_password"
                        placeholder="Ingrese su contraseña nuevamente" autocomplete="new-password">
                    <button class="btn btn-outline-secondary toggle-password" type="button"
                        data-target="confirm_password" tabindex="-1" aria-label="Mostrar contraseña">👁</button>
                </div>
                @error('confirm_password')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Datos de la empresa ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Datos de la empresa</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="admission_date">Fecha de Ingreso a la empresa</label>
                <input class="datepicker-here form-control bg-light" type="text" id="admission_date" name="admission_date"
                    value="{{ isset($user->admission_date) ? $user->admission_date : old('admission_date', now()->format('d/m/Y')) }}"
                    data-language="es" placeholder="Ingrese la fecha de ingreso">
                @error('admission_date')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="telecom_id">ID Telecom</label>
                <input class="form-control bg-light" type="text" id="telecom_id" name="telecom_id"
                    value="{{ isset($user->telecom_id) ? $user->telecom_id : old('telecom_id') }}"
                    placeholder="Ingrese ID Telecom">
                @error('telecom_id')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
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
                <label for="image">Imagen de perfil</label>
                <input class="form-control bg-light" type="file" id="image" name="image" accept="image/*">

                <div class="mt-3 comman-image d-flex align-items-start gap-3 flex-wrap">
                    {{-- Vista previa de la imagen recién seleccionada --}}
                    <img id="imagePreview" alt="Vista previa" class="img-thumbnail img-fix d-none"
                        style="max-height:120px;max-width:120px;object-fit:cover;">

                    {{-- Imagen actual (solo en edición) --}}
                    @if ($isEdit && $image)
                        <div>
                            <img src="{{ $image->getUrl() }}" alt="Imagen actual" class="img-thumbnail img-fix"
                                style="max-height:120px;max-width:120px;object-fit:cover;">
                            <div class="dz-preview">
                                <a href="{{ route('admin.user.removeImage', $user?->id) }}" class="dz-remove text-danger"
                                    data-bs-target="#tooltipmodal" data-bs-toggle="modal">Eliminar</a>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($isEdit)
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
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="about_me">Más Información</label>
                <textarea class="form-control bg-light" id="about_me" rows="2" name="about_me"
                    placeholder="Más información">{{ isset($user->about_me) ? $user->about_me : old('about_me') }}</textarea>
                @error('about_me')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== Acciones ===================== --}}
    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Mostrar/ocultar contraseña y vista previa de imagen (sin dependencias externas) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.textContent = show ? '🙈' : '👁';
                btn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });

        var imageInput = document.getElementById('image');
        var preview = document.getElementById('imagePreview');
        if (imageInput && preview) {
            imageInput.addEventListener('change', function () {
                var file = this.files && this.files[0];
                if (file) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                }
            });
        }
    });
</script>
