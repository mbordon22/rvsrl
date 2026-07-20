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

    // --- Datos del poste (tamaño+material): visibles SOLO si colocó poste ---
    const coloco = document.getElementById('coloco_poste');
    function aplicarDatosPoste() {
        const grp = document.getElementById('grp-datos-poste');
        const on = coloco && coloco.checked;
        if (grp) grp.style.display = on ? '' : 'none';
    }
    if (coloco) { coloco.addEventListener('change', aplicarDatosPoste); }
    aplicarDatosPoste();

    // --- Material=reutilizado -> mostrar "qué material se reutilizó" ---
    const posteMaterial = document.getElementById('poste_material');
    function aplicarReutilizado() {
        const grp = document.getElementById('grp-reutilizado');
        if (grp) grp.style.display = (posteMaterial && posteMaterial.value === 'reutilizado') ? '' : 'none';
    }
    if (posteMaterial) { aplicarReutilizado(); posteMaterial.addEventListener('change', aplicarReutilizado); }

    // --- Sifón: NO -> nada; SÍ -> cables + protecciones ---
    const sifon = document.getElementById('sifon');
    function aplicarSifon() {
        const datos = document.getElementById('grp-sifon-datos');
        const on = sifon && sifon.checked;
        if (datos) datos.style.display = on ? '' : 'none';
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

    // --- CDO/Caja Terminal/NAP: resaltar el bloque si alguna cantidad tiene valor ---
    const grpElementos = document.getElementById('grp-elementos');
    const elementoInputs = document.querySelectorAll('.elemento-cantidad');
    function aplicarElementos() {
        if (!grpElementos) return;
        const activa = Array.from(elementoInputs).some(function (i) { return i.value !== '' && Number(i.value) > 0; });
        grpElementos.classList.toggle('activa', activa);
    }
    elementoInputs.forEach(function (i) { i.addEventListener('input', aplicarElementos); });
    aplicarElementos();

    // --- Tipo de suelo: contrapiso/os -> mostrar reparación de vereda ---
    const suelo = document.getElementById('tipo_suelo');
    function aplicarSuelo() {
        const grp = document.getElementById('grp-rep-vereda');
        const val = suelo ? suelo.value : '';
        if (grp) grp.style.display = (val === 'contrapiso') ? '' : 'none';
    }
    if (suelo) { aplicarSuelo(); suelo.addEventListener('change', aplicarSuelo); }

    // --- Empleados según cuadrilla seleccionada (admin) ---
    const cuadSel = document.querySelector('select[name="cuadrilla_id"]');
    const empCont = document.getElementById('empleados-container');
    const empBase = "{{ url('admin/trabajos/ordenes/cuadrilla') }}";

    function renderEmpleados(empleados) {
        if (!empCont) return;
        if (!empleados.length) {
            empCont.innerHTML = '<p class="text-muted mb-0">La cuadrilla no tiene integrantes asignados.</p>';
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
        if (!cid) { empCont.innerHTML = '<p class="text-muted mb-0">Seleccioná una cuadrilla para ver sus integrantes.</p>'; return; }
        empCont.innerHTML = '<p class="text-muted mb-0">Cargando integrantes…</p>';
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

    // --- Ubicación GPS (API de geolocalización del navegador) ---
    const btnUbic = document.getElementById('btn-ubicacion');
    const estadoUbic = document.getElementById('ubicacion-estado');
    const linkUbic = document.getElementById('ubicacion-link');
    const latI = document.getElementById('latitud');
    const lngI = document.getElementById('longitud');

    function actualizarLinkUbic() {
        if (!linkUbic) return;
        if (latI && lngI && latI.value && lngI.value) {
            linkUbic.href = 'https://www.google.com/maps?q=' + latI.value + ',' + lngI.value;
            linkUbic.style.display = '';
        } else {
            linkUbic.style.display = 'none';
        }
    }
    actualizarLinkUbic();

    if (btnUbic) {
        btnUbic.addEventListener('click', function () {
            if (!('geolocation' in navigator)) {
                estadoUbic.textContent = 'Este dispositivo no soporta geolocalización.';
                return;
            }
            estadoUbic.textContent = 'Obteniendo ubicación…';
            btnUbic.disabled = true;
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    latI.value = pos.coords.latitude.toFixed(7);
                    lngI.value = pos.coords.longitude.toFixed(7);
                    estadoUbic.textContent = 'Ubicación cargada (precisión ~' + Math.round(pos.coords.accuracy) + ' m).';
                    btnUbic.disabled = false;
                    actualizarLinkUbic();
                },
                function (err) {
                    const msgs = { 1: 'Permiso denegado.', 2: 'Ubicación no disponible.', 3: 'Tiempo de espera agotado.' };
                    estadoUbic.textContent = 'No se pudo obtener la ubicación: ' + (msgs[err.code] || err.message);
                    btnUbic.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
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

    // --- Rail de navegación + scroll interno del formulario (solo desktop) ---
    const railItems = Array.prototype.slice.call(document.querySelectorAll('.nt-rail-item'));
    const layout = document.querySelector('.nt-layout');
    const cont   = document.querySelector('.nt-col'); // el que scrollea
    if (railItems.length && layout && cont) {
        const secciones = railItems
            .map(function (it) { return document.getElementById(it.getAttribute('data-target')); })
            .filter(Boolean);

        function isDesktop() { return window.matchMedia('(min-width: 992px)').matches; }

        // Acota la altura del layout para que .nt-col scrollee y el rail quede fijo.
        function ajustarAltura() {
            if (!isDesktop()) { layout.style.height = ''; return; }
            const top = layout.getBoundingClientRect().top; // distancia desde el tope del viewport
            const h = window.innerHeight - top - 16;
            layout.style.height = Math.max(320, h) + 'px';
        }

        // Resalta la sección visible según el scroll del formulario.
        function marcarActiva() {
            let actual = secciones[0];
            secciones.forEach(function (el) {
                if (el.offsetTop - cont.scrollTop <= 24) actual = el;
            });
            railItems.forEach(function (it) {
                it.classList.toggle('active', actual && it.getAttribute('data-target') === actual.id);
            });
        }

        // Click: scroll suave a la sección dentro del contenedor del formulario.
        railItems.forEach(function (it) {
            it.addEventListener('click', function () {
                const el = document.getElementById(it.getAttribute('data-target'));
                if (!el) return;
                if (isDesktop()) {
                    cont.scrollTo({ top: Math.max(0, el.offsetTop - 8), behavior: 'smooth' });
                } else {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        cont.addEventListener('scroll', marcarActiva, { passive: true });
        window.addEventListener('resize', function () { ajustarAltura(); marcarActiva(); }, { passive: true });
        window.addEventListener('load', ajustarAltura);
        ajustarAltura();
        marcarActiva();
    }
})();
</script>
