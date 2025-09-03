// ! Depende de window.gruposStepVars y window.registroBladeVars
(function () {
    if (!window.gruposStepVars) return;

    const ORDEN = window.gruposStepVars.ordenSecciones || [
        'info_general', 'ubicacion', 'organismo', 'opciones', 'agenda', 'alumnos'
    ];

    // Última sección finalizada como string (puede ser null/undefined si ninguna)
    const ultimaSeccion = window.gruposStepVars.ultimaSeccion;

    // Genera estructura como en alumnos: { seccion: { estado: bool } }
    function generarEstadosCaptura() {
        const estados = {};
        // Determinar índice de la última sección finalizada
        let ultimaIndex = -1;
        if (ultimaSeccion) {
            ultimaIndex = ORDEN.indexOf(ultimaSeccion);
        }
        ORDEN.forEach((sec, idx) => {
            estados[sec] = { estado: idx <= ultimaIndex && ultimaIndex !== -1 };
        });
        return estados;
    }

    let estadosCaptura = generarEstadosCaptura();
    window.estadosCapturaGrupos = estadosCaptura;

    const navItems = document.querySelectorAll('.step-progress-nav .list-group-item');
    // Paso seleccionado manualmente (data-step)
    let pasoSeleccionado = null;

    function aplicarEstadosPasos() {
        // encontrar la primera NO completada
        let siguienteIndex = -1;
        for (let i = 0; i < ORDEN.length; i++) {
            if (!estadosCaptura[ORDEN[i]] || !estadosCaptura[ORDEN[i]].estado) {
                siguienteIndex = i; break;
            }
        }

        // Reset visual de todos los items (móvil y lateral)
        navItems.forEach((item) => {
            item.classList.remove('completed', 'current', 'disabled', 'active', 'actual');
            const circle = item.querySelector('.step-circle');
            if (circle) circle.removeAttribute('data-status');
        });

    // Aplicar estado por cada item según su data-step
        navItems.forEach((item) => {
            const sec = item.getAttribute('data-step');
            if (!sec) return;
            const idx = ORDEN.indexOf(sec);
            const circle = item.querySelector('.step-circle');
            const completada = idx > -1 && !!(estadosCaptura[sec] && estadosCaptura[sec].estado);

            // Caso: selección manual prevalece (sin importar estado)
            if (pasoSeleccionado && pasoSeleccionado === sec) {
                item.classList.add('active', 'actual');
                if (circle) circle.setAttribute('data-status','actual');
                item.style.pointerEvents='auto'; item.style.opacity='1';
                return; // saltar resto
            }

            if (completada) {
                item.classList.add('completed');
                if (circle) circle.setAttribute('data-status', 'terminado');
                item.style.pointerEvents = 'auto'; item.style.opacity = '1';
            } else if (idx === siguienteIndex) {
                // Es la primera pendiente
                if (!pasoSeleccionado) {
                    // Caso normal: se muestra como actual
                    item.classList.add('current', 'active', 'actual');
                    if (circle) circle.setAttribute('data-status', 'actual');
                } else {
                    // Hay otra sección seleccionada; dejamos clic habilitado sin marcarla verde
                    if (circle) circle.setAttribute('data-status', 'restante');
                }
                item.style.pointerEvents = 'auto';
                item.style.opacity = '1';
            } else {
                item.classList.add('disabled');
                // Mostrar visual de 'restante' (no capturada) mientras no esté seleccionada
                if (circle) circle.setAttribute('data-status', 'restante');
                item.style.pointerEvents = 'none'; item.style.opacity = '0.5';
            }
        });

        // Si no hay ningún activo explícito, marcar el primero visible como activo para el centrado móvil
        const anyActive = document.querySelector('.step-progress-nav li.active');
        if (!anyActive) {
            const first = document.querySelector('.step-progress-nav li');
            if (first) first.classList.add('active');
        }
    }

    function ocultarTodas() {
        const seccionesDom = document.querySelectorAll('.step-section');
        seccionesDom.forEach(sec => {
            // Asegurar ocultar por estilo directo para sobreescribir inline inicial
            sec.style.display = 'none';
            sec.classList.remove('d-none'); // limpiamos para evitar doble gestión
        });
    }

    function mostrarSeccion(id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = ''; // restaura display por defecto
            el.classList.remove('d-none');
        }
        // En móviles, intentar centrar el círculo activo
        try {
            const cont = document.querySelector('#step-progress .step-progress-nav') || document.querySelector('.step-progress-nav');
            if (cont) {
                const activo = document.querySelector('.step-progress-nav li.active');
                if (activo) {
                    const rect = activo.getBoundingClientRect();
                    const contRect = cont.getBoundingClientRect();
                    const scrollLeft = cont.scrollLeft + (rect.left - contRect.left) - (contRect.width / 2) + (rect.width / 2);
                    cont.scrollTo({ left: scrollLeft, behavior: 'smooth' });
                }
            }
        } catch (e) { /* noop */ }
    }

    function mostrarSeccionActual() {
        ocultarTodas();
        const activo = document.querySelector('.step-progress-nav li.active');
        let idMostrar = ORDEN[0];
        if (activo) { idMostrar = activo.getAttribute('data-step'); }
        else {
            for (let i = 0; i < ORDEN.length; i++) {
                if (!estadosCaptura[ORDEN[i]] || !estadosCaptura[ORDEN[i]].estado) { idMostrar = ORDEN[i]; break; }
                if (i === ORDEN.length - 1) idMostrar = ORDEN[i];
            }
        }
        mostrarSeccion(idMostrar);
        // console.log('[Stepbar][Init] Sección mostrada:', idMostrar);
    }

    function moverSiguienteSeccion(seccionActual) {
        const idx = ORDEN.indexOf(seccionActual);
        if (idx === -1) return;
        estadosCaptura[seccionActual] = { estado: true };
        // Reset selección manual para permitir que la lógica automática enfoque la siguiente
        pasoSeleccionado = null;
        aplicarEstadosPasos();
        const siguiente = ORDEN[idx + 1];
        if (siguiente) {
            ocultarTodas();
            mostrarSeccion(siguiente);
            // console.log('[Stepbar] Cambio automático a sección:', siguiente, '(desde', seccionActual, ')');
        } else {
            // fin
            const notyf = window.Notyf ? new Notyf({ position: { x: 'right', y: 'top' }, duration: 5000 }) : null;
            if (notyf) { notyf.open({ type: 'success', className: 'notyf-success', message: '¡Registro de grupo completado!' }); }
            // console.log('[Stepbar] Registro finalizado. Última sección completada:', seccionActual);
        }
    }

    // Exponer para uso desde registro.js cuando se guarda
    window.moverSiguienteSeccionGrupo = moverSiguienteSeccion;

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            if (item.classList.contains('disabled')) return;
            const seccion = item.getAttribute('data-step');
            pasoSeleccionado = seccion; // recordamos selección manual
            ocultarTodas();
            mostrarSeccion(seccion);
            aplicarEstadosPasos();
            // console.log('[Stepbar] Cambio manual a sección:', seccion);
        });
    });

    // Inicializar
    aplicarEstadosPasos();
    mostrarSeccionActual();

})();
