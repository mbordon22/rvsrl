@use('App\Helpers\Helpers')
@php
    $permissions = @$role?->getAllPermissions()->pluck('name')->toArray() ?? [];
@endphp

<style>
    /* Toggles de permisos más grandes (form-switch usa medidas en em) */
    #permissionsWrapper .form-switch .form-check-input {
        font-size: 1.4rem;
        cursor: pointer;
    }
</style>

{{-- ===================== Permisos ===================== --}}
<div class="col-12">
    <h6 class="text-uppercase bg-primary text-white fw-bold mb-3 p-2 rounded">Permisos</h6>
</div>
@error('permissions')
    <div class="col-12">
        <span class="text-danger"><strong>{{ $message }}</strong></span>
    </div>
@enderror

<div class="row g-3" id="permissionsWrapper">
    @foreach ($modules as $key => $module)
        @php
            $isAllSelected = count(array_diff(array_values($module->actions), $permissions)) === 0;
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card border rounded-3 h-100 mb-0 permission-module" data-module="{{ $module->name }}">
                {{-- Encabezado del módulo + toggle maestro --}}
                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ $module->nombre_es }}</h6>
                    <div class="d-flex align-items-center gap-2">
                        <small class="module-toggle-label text-muted"></small>
                        <div class="form-check form-switch m-0 p-0 d-flex">
                            <input type="checkbox" role="switch"
                                class="form-check-input m-0 js-select-all" style="cursor:pointer;"
                                data-module-target="{{ $module->name }}" id="all-{{ $module->name }}"
                                {{ $isAllSelected ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                {{-- Permisos del módulo --}}
                <div class="card-body py-1 px-3">
                    @foreach ($module->actions as $action => $permission)
                        @if ($action != 'delete')
                            <div class="d-flex justify-content-between align-items-center py-2 permission-row">
                                <label class="mb-0 me-2" for="{{ $permission }}" style="cursor:pointer;">
                                    {{ Helpers::getNamePermissionEs($action) }}
                                </label>
                                <div class="form-check form-switch m-0 p-0 d-flex">
                                    <input type="checkbox" role="switch" name="permissions[]"
                                        class="form-check-input m-0 js-permission module_{{ $module->name }} module_{{ $module->name }}_{{ $action }}"
                                        style="cursor:pointer;" data-module="{{ $module->name }}"
                                        data-action="{{ $action }}" value="{{ $permission }}" id="{{ $permission }}"
                                        {{ in_array($permission, $permissions) ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ===================== Acciones ===================== --}}
<div class="row mt-4">
    <div class="col">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.role.index') }}" class="btn btn-light">{{ __('Cancelar') }}</a>
            <button class="btn btn-primary" type="submit">{{ __('Guardar') }}</button>
        </div>
    </div>
</div>

{{-- Lógica de toggles de permisos (vanilla JS, autónoma) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var wrapper = document.getElementById('permissionsWrapper');
        if (!wrapper) return;

        function refreshModule(moduleName) {
            var items = wrapper.querySelectorAll('.js-permission[data-module="' + moduleName + '"]');
            var checked = wrapper.querySelectorAll('.js-permission[data-module="' + moduleName + '"]:checked');
            var master = wrapper.querySelector('.js-select-all[data-module-target="' + moduleName + '"]');
            var allOn = items.length > 0 && checked.length === items.length;

            if (master) {
                master.checked = allOn;
                var label = master.closest('.card-header').querySelector('.module-toggle-label');
                if (label) {
                    if (allOn) {
                        label.textContent = 'Todos activados';
                        label.className = 'module-toggle-label text-success fw-semibold';
                    } else if (checked.length === 0) {
                        label.textContent = 'Todos desactivados';
                        label.className = 'module-toggle-label text-muted';
                    } else {
                        label.textContent = checked.length + ' de ' + items.length;
                        label.className = 'module-toggle-label text-primary fw-semibold';
                    }
                }
            }
        }

        // Toggle maestro: activa/desactiva todos los permisos del módulo
        wrapper.querySelectorAll('.js-select-all').forEach(function (master) {
            master.addEventListener('change', function () {
                var moduleName = master.getAttribute('data-module-target');
                wrapper.querySelectorAll('.js-permission[data-module="' + moduleName + '"]').forEach(function (item) {
                    item.checked = master.checked;
                });
                refreshModule(moduleName);
            });
        });

        // Toggle individual: recalcula el estado del maestro y la etiqueta
        wrapper.querySelectorAll('.js-permission').forEach(function (item) {
            item.addEventListener('change', function () {
                refreshModule(item.getAttribute('data-module'));
            });
        });

        // Estado inicial de las etiquetas
        var seen = {};
        wrapper.querySelectorAll('.js-permission').forEach(function (item) {
            var m = item.getAttribute('data-module');
            if (!seen[m]) { seen[m] = true; refreshModule(m); }
        });
    });
</script>
