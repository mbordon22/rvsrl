<div class="form theme-form w-100">

    {{-- ===================== Datos del rol ===================== --}}
    <div class="col-12">
        <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Datos del rol</h6>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="name">Nombre <span class="text-danger">*</span></label>
                <input class="form-control bg-light" type="text" id="name" placeholder="Ingresar nombre del rol"
                    name="name" value="{{ isset($role->name) ? $role->name : old('name') }}">
                @error('name')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    @include('admin.role._permissions')
</div>
