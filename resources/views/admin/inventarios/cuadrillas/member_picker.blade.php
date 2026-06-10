{{-- Selector de integrantes: buscador con autocompletado + lista de miembros. --}}
<script>
    window.__cuadrillaUsuarios = @json($usuarios);
    window.__cuadrillaSeleccionados = @json(array_map('intval', (array) $empleadosSeleccionados));
</script>
<script>
    (function () {
        function init() {
            var usuarios = window.__cuadrillaUsuarios || [];
            var preseleccionados = window.__cuadrillaSeleccionados || [];

            var $search = document.getElementById('empleadoSearch');
            var $suggestions = document.getElementById('empleadoSuggestions');
            var $list = document.getElementById('empleadoList');
            var $empty = document.getElementById('empleadoEmpty');
            var $count = document.getElementById('empleadoCount');

            if (!$search || !$list) return;

            var byId = {};
            usuarios.forEach(function (u) { byId[u.id] = u; });
            var seleccionados = []; // array de ids (number)

            function escapeHtml(str) {
                return String(str == null ? '' : str)
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            function avatarHtml(u, size) {
                size = size || 36;
                if (u.avatar) {
                    return '<img src="' + escapeHtml(u.avatar) + '" alt="" class="rounded-circle" ' +
                        'style="width:' + size + 'px;height:' + size + 'px;object-fit:cover;">';
                }
                return '<span class="rounded-circle bg-primary text-white d-inline-flex align-items-center ' +
                    'justify-content-center fw-bold" style="width:' + size + 'px;height:' + size + 'px;font-size:' +
                    Math.round(size / 2.5) + 'px;">' + escapeHtml(u.inicial || '?') + '</span>';
            }

            function refreshState() {
                $count.textContent = seleccionados.length;
                $empty.style.display = seleccionados.length ? 'none' : '';
                if (window.feather) { window.feather.replace(); }
            }

            function addMember(id) {
                id = parseInt(id, 10);
                if (seleccionados.indexOf(id) !== -1) return;
                var u = byId[id];
                if (!u) return;
                seleccionados.push(id);

                var row = document.createElement('div');
                row.className = 'empleado-item d-flex align-items-center justify-content-between border rounded bg-white p-2 mb-2';
                row.setAttribute('data-id', id);
                row.innerHTML =
                    '<div class="d-flex align-items-center">' +
                        avatarHtml(u, 40) +
                        '<div class="ms-2">' +
                            '<div class="fw-semibold text-dark">' + escapeHtml(u.nombre) + '</div>' +
                            (u.rol ? '<small class="text-muted text-capitalize">' + escapeHtml(u.rol) + '</small>' : '') +
                        '</div>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger empleado-remove" title="Quitar">' +
                        '<i data-feather="x"></i>' +
                    '</button>' +
                    '<input type="hidden" name="empleados[]" value="' + id + '">';

                row.querySelector('.empleado-remove').addEventListener('click', function () {
                    var idx = seleccionados.indexOf(id);
                    if (idx !== -1) seleccionados.splice(idx, 1);
                    row.parentNode.removeChild(row);
                    refreshState();
                });

                $list.appendChild(row);
                refreshState();
            }

            function renderSuggestions(term) {
                term = (term || '').trim().toLowerCase();
                $suggestions.innerHTML = '';
                if (!term) { $suggestions.style.display = 'none'; return; }

                var matches = usuarios.filter(function (u) {
                    return seleccionados.indexOf(u.id) === -1 &&
                        u.nombre.toLowerCase().indexOf(term) !== -1;
                }).slice(0, 8);

                if (!matches.length) {
                    $suggestions.innerHTML =
                        '<div class="list-group-item text-muted">Sin resultados</div>';
                    $suggestions.style.display = 'block';
                    return;
                }

                matches.forEach(function (u) {
                    var item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action d-flex align-items-center';
                    item.setAttribute('data-id', u.id);
                    item.innerHTML =
                        avatarHtml(u, 32) +
                        '<div class="ms-2">' +
                            '<div class="text-dark">' + escapeHtml(u.nombre) + '</div>' +
                            (u.rol ? '<small class="text-muted text-capitalize">' + escapeHtml(u.rol) + '</small>' : '') +
                        '</div>';
                    item.addEventListener('click', function () {
                        addMember(u.id);
                        $search.value = '';
                        $suggestions.style.display = 'none';
                        $search.focus();
                    });
                    $suggestions.appendChild(item);
                });
                $suggestions.style.display = 'block';
                if (window.feather) { window.feather.replace(); }
            }

            $search.addEventListener('input', function () { renderSuggestions(this.value); });
            $search.addEventListener('focus', function () { if (this.value) renderSuggestions(this.value); });
            document.addEventListener('click', function (e) {
                if (!$suggestions.contains(e.target) && e.target !== $search) {
                    $suggestions.style.display = 'none';
                }
            });

            // Carga inicial (edición / validación fallida)
            preseleccionados.forEach(function (id) { addMember(id); });
            refreshState();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
