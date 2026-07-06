<script>
(function () {
    // --- Toggles genéricos: checkbox .toggle con data-toggle-target ---
    function aplicarToggle(el) {
        const target = document.querySelector(el.getAttribute('data-toggle-target'));
        if (!target) return;
        target.style.display = el.checked ? '' : 'none';
    }
    document.querySelectorAll('.toggle[data-toggle-target]').forEach(function (el) {
        aplicarToggle(el);
        el.addEventListener('change', function () { aplicarToggle(el); });
    });

    // --- Central: mostrar "aclarar" solo cuando el valor = data-toggle-value (CYO) ---
    document.querySelectorAll('select[data-toggle-target][data-toggle-value]').forEach(function (sel) {
        const target = document.querySelector(sel.getAttribute('data-toggle-target'));
        const wanted = sel.getAttribute('data-toggle-value');
        function apply() { if (target) target.style.display = (sel.value === wanted) ? '' : 'none'; }
        apply();
        sel.addEventListener('change', apply);
    });

    // --- Datos del poste (tamaño+material): visibles si desmontó O colocó ---
    const desmonto = document.getElementById('desmonto_poste');
    const coloco = document.getElementById('coloco_poste');
    function aplicarDatosPoste() {
        const grp = document.getElementById('grp-datos-poste');
        const on = (desmonto && desmonto.checked) || (coloco && coloco.checked);
        if (grp) grp.style.display = on ? '' : 'none';
    }
    [desmonto, coloco].forEach(function (el) { if (el) el.addEventListener('change', aplicarDatosPoste); });
    aplicarDatosPoste();

    // --- Material=reutilizado -> mostrar "qué material se reutilizó" ---
    const posteMaterial = document.getElementById('poste_material');
    function aplicarReutilizado() {
        const grp = document.getElementById('grp-reutilizado');
        if (grp) grp.style.display = (posteMaterial && posteMaterial.value === 'reutilizado') ? '' : 'none';
    }
    if (posteMaterial) { aplicarReutilizado(); posteMaterial.addEventListener('change', aplicarReutilizado); }

    // --- CDO/Caja Terminal/NAP: elegir uno -> mostrar cantidad ---
    const elemento = document.getElementById('elemento_tipo');
    function aplicarElemento() {
        const grp = document.getElementById('grp-elemento-cantidad');
        if (grp) grp.style.display = (elemento && elemento.value) ? '' : 'none';
    }
    if (elemento) { aplicarElemento(); elemento.addEventListener('change', aplicarElemento); }

    // --- Sifón: SÍ -> cables, NO -> protecciones ---
    const sifon = document.getElementById('sifon');
    function aplicarSifon() {
        const cables = document.getElementById('grp-sifon-cables');
        const protec = document.getElementById('grp-sifon-protecciones');
        const on = sifon && sifon.checked;
        if (cables) cables.style.display = on ? '' : 'none';
        if (protec) protec.style.display = on ? 'none' : '';
    }
    if (sifon) { aplicarSifon(); sifon.addEventListener('change', aplicarSifon); }

    // --- Resaltado de preguntas activas (bloque .pregunta con .pregunta-control) ---
    function marcarActiva(el) {
        const p = el.closest('.pregunta');
        if (!p) return;
        const activa = (el.type === 'checkbox') ? el.checked : (el.value !== '' && el.value != null);
        p.classList.toggle('activa', activa);
    }
    document.querySelectorAll('.pregunta-control').forEach(function (el) {
        marcarActiva(el);
        el.addEventListener('change', function () { marcarActiva(el); });
    });

    // --- Tipo de suelo: contrapiso/os -> mostrar reparación de vereda ---
    const suelo = document.getElementById('tipo_suelo');
    function aplicarSuelo() {
        const grp = document.getElementById('grp-rep-vereda');
        const val = suelo ? suelo.value : '';
        if (grp) grp.style.display = (val === 'contrapiso' || val === 'os') ? '' : 'none';
    }
    if (suelo) { aplicarSuelo(); suelo.addEventListener('change', aplicarSuelo); }

    // --- Empleados según cuadrilla seleccionada (admin) ---
    const cuadSel = document.querySelector('select[name="cuadrilla_id"]');
    const empCont = document.getElementById('empleados-container');
    const empBase = "{{ url('admin/trabajos/ordenes/cuadrilla') }}";

    function renderEmpleados(empleados) {
        if (!empCont) return;
        if (!empleados.length) {
            empCont.innerHTML = '<p class="text-muted mb-0">La cuadrilla no tiene empleados asignados.</p>';
            return;
        }
        empCont.innerHTML = empleados.map(function (e) {
            return '<div class="form-check form-check-inline mb-2">' +
                '<input class="form-check-input" type="checkbox" name="empleados[]" id="emp' + e.id + '" value="' + e.id + '" checked>' +
                '<label class="form-check-label" for="emp' + e.id + '">' + e.nombre + '</label></div>';
        }).join('');
    }

    function cargarEmpleados(cid) {
        if (!empCont) return;
        if (!cid) { empCont.innerHTML = '<p class="text-muted mb-0">Seleccioná una cuadrilla para ver sus empleados.</p>'; return; }
        empCont.innerHTML = '<p class="text-muted mb-0">Cargando empleados…</p>';
        fetch(empBase + '/' + cid + '/empleados')
            .then(function (r) { return r.json(); })
            .then(function (data) { renderEmpleados(data.empleados || []); })
            .catch(function () { empCont.innerHTML = '<p class="text-danger mb-0">No se pudieron cargar los empleados.</p>'; });
    }

    if (cuadSel) {
        cuadSel.addEventListener('change', function () { cargarEmpleados(cuadSel.value); });
        // Carga inicial si ya hay una cuadrilla seleccionada y el checklist está vacío (create como admin)
        const yaTieneCheckboxes = empCont && empCont.querySelector('input[type="checkbox"]');
        if (cuadSel.value && !yaTieneCheckboxes) { cargarEmpleados(cuadSel.value); }
    }

    // --- Preview de fotos (múltiples) antes de subir ---
    document.querySelectorAll('.foto-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const cont = document.querySelector(input.getAttribute('data-preview'));
            if (!cont) return;
            cont.innerHTML = '';
            Array.from(input.files).forEach(function (file) {
                if (!file.type.startsWith('image/')) return;
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;';
                cont.appendChild(img);
            });
        });
    });
})();
</script>
