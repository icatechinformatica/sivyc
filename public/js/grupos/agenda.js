
; (function () {
    function getConfig() { return window.GrupoAgenda || {}; }
    function getToken() { return (window.registroBladeVars && window.registroBladeVars.csrfToken) || document.querySelector('meta[name="csrf-token"]')?.content; }

    let calendar = null;

    // Utilidades de formato de tiempo
    function decimalAHora(dec) {
        if (!isFinite(dec)) dec = 0;
        if (dec < 0) dec = 0;
        const totalMin = Math.round(dec * 60);
        const h = Math.floor(totalMin / 60);
        const m = totalMin % 60;
        const hh = String(h).padStart(2, '0');
        const mm = String(m).padStart(2, '0');
        return `${hh}:${mm}`;
    }

    function horaADecimal(hhmm) {
        if (typeof hhmm !== 'string') return 0;
        const parts = hhmm.split(':');
        if (parts.length < 2) return 0;
        const h = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10);
        const hh = isFinite(h) ? h : 0;
        const mm = isFinite(m) ? m : 0;
        return hh + (mm / 60);
    }

    function minutosEntre(fechaInicio, fechaFin) {
        if (!fechaInicio) return 0;
        const start = (fechaInicio instanceof Date) ? fechaInicio : new Date(fechaInicio);
        const end = (fechaFin instanceof Date) ? fechaFin : (fechaFin ? new Date(fechaFin) : start);
        const diffMs = end - start;
        if (!isFinite(diffMs) || diffMs <= 0) return 0;
        return Math.round(diffMs / 60000);
    }

    // Actualiza los indicadores del panel lateral de la agenda
    function actualizarIndicadoresAgenda() {
        try {
            const cal = (window.GrupoAgenda && window.GrupoAgenda.calendar) || calendar;
            if (!cal) return;

            const eventos = cal.getEvents();
            const minutosTotales = eventos.reduce((acc, evt) => {
                return acc + minutosEntre(evt.start, evt.end || evt.start);
            }, 0);
            const horasTotales = minutosTotales / 60;

            const $horasTotales = document.getElementById('fc-horas-totales');
            const $diasHoras = document.getElementById('fc-dias-horas');
            const $horasRestantes = document.getElementById('fc-horas-restantes');
            const $horasMaximas = document.getElementById('fc-horas-maximas');

            // Horas máximas del curso: preferir config, si no, inferir del texto (HH:MM)
            let maxHoras = parseFloat(getConfig().maxHoras);
            if (!isFinite(maxHoras) && $horasMaximas && $horasMaximas.textContent) {
                maxHoras = horaADecimal(($horasMaximas.textContent || '').trim());
            }

            if ($horasTotales) $horasTotales.textContent = decimalAHora(horasTotales);
            if ($diasHoras) $diasHoras.textContent = String(eventos.length);

            if ($horasRestantes && isFinite(maxHoras)) {
                const restantes = Math.max(0, maxHoras - horasTotales);
                $horasRestantes.textContent = decimalAHora(restantes);
            }
        } catch (_) { /* noop */ }
    }

    // Abrir modal
    $(document).on('click', '#btn-agenda', function () {
        $('#modalFullCalendar').modal('show');
    });

    // Redirección segura del botón Guardar
    $(document).on('click', '#btn-guardar', function (e) {
        const url = $(this).data('redirect');
        if (url) window.location.href = url;
    });

    // Inicializar al mostrar modal
    document.addEventListener('DOMContentLoaded', function () {
        $('#modalFullCalendar').on('shown.bs.modal', function () {
            if (calendar) return;
            const cfg = getConfig();
            const grupoId = cfg.grupoId;
            const routes = cfg.routes || {};
            const calendarEl = document.getElementById('calendar');
            // Restaurar colores guardados
            const savedBg = localStorage.getItem('agenda_event_color');
            const savedText = localStorage.getItem('agenda_event_text_color');
            if (savedBg) { const el = document.getElementById('color_fondo'); if (el) el.value = savedBg; }
            if (savedText) { const el = document.getElementById('color_texto'); if (el) el.value = savedText; }

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
                locale: 'es',
                firstDay: 0, // domingo
                height: 'auto',
                buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día' },
                selectable: true,
                selectMirror: true,
                editable: true,
                eventOverlap: true,
                // Mostrar eventos como bloques en mes para poder colorear fondo
                views: {
                    dayGridMonth: { eventDisplay: 'block' }
                },
                // eventDidMount sin estilos inline para permitir cambios dinámicos por CSS vars
                eventDidMount: function(info) { /* no-op */ },
        // Refrescar indicadores al ajustar el conjunto de eventos
        eventsSet: function() { actualizarIndicadoresAgenda(); },
                // Cargar eventos desde backend
                events: function (fetchInfo, success, failure) {
                    if (!grupoId || !routes.index) { success([]); return; }
                    $.ajax({
                        url: routes.index(grupoId),
                        method: 'GET',
            success: function (data) { success(data || []); },
                        error: function () { failure('No se pudo cargar la agenda'); }
                    });
                },
                // Crear evento por selección (permite 1 día a varios días con drag)
                select: function (arg) {
                    if (!grupoId) { calendar.unselect(); return; }
                    const start = arg.startStr;
                    const end = arg.endStr; // end exclusivo en FullCalendar
                    $.ajax({
                        url: routes.store(grupoId),
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': getToken() },
                        data: { start, end },
                        success: function (evt) {
                            calendar.addEvent({ id: evt.id, title: evt.title || 'Sesión', start: evt.start, end: evt.end });
                            // Aplicar colores actuales al nuevo evento
                            if (typeof agendaColores === 'function') agendaColores();
                            actualizarIndicadoresAgenda();
                        },
                        error: function (xhr) {
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo crear el evento';
                            alert(msg);
                        },
                        complete: function () { calendar.unselect(); }
                    });
                },
                // Eliminar con click
                eventClick: function (info) {
                    const evt = info.event;
                    if (!confirm('¿Eliminar este evento?')) return;
                    const id = evt.id; if (!id) { evt.remove(); return; }
                    $.ajax({
                        url: (routes.destroy && routes.destroy(getConfig().grupoId, id)),
                        method: 'POST', // usar POST con spoofing si no está permitido DELETE
                        data: { _method: 'DELETE' },
                        headers: { 'X-CSRF-TOKEN': getToken() },
                        success: function () { evt.remove(); actualizarIndicadoresAgenda(); },
                        error: function () { alert('No se pudo eliminar'); }
                    });
                },
                // Drag & drop
                eventDrop: function(info){ persistMoveOrResize(info); actualizarIndicadoresAgenda(); },
                eventResize: function(info){ persistMoveOrResize(info); actualizarIndicadoresAgenda(); },
                // Cambios directos sobre props del evento
                eventAdd: function(){ actualizarIndicadoresAgenda(); },
                eventChange: function(){ actualizarIndicadoresAgenda(); },
                eventRemove: function(){ actualizarIndicadoresAgenda(); },
            });

            calendar.render();

            // Exponer la instancia para uso externo y aplicar colores persistidos
            window.GrupoAgenda = window.GrupoAgenda || {};
            window.GrupoAgenda.calendar = calendar;
            if (savedBg) calendar.setOption('eventColor', savedBg);
            if (savedText) calendar.setOption('eventTextColor', savedText);

            // Establecer variables CSS en el contenedor para colores dinámicos
            try {
                if (calendarEl) {
                    if (savedBg) calendarEl.style.setProperty('--agenda-event-bg', savedBg);
                    if (savedText) calendarEl.style.setProperty('--agenda-event-text', savedText);
                }
            } catch (_) { /* noop */ }

            // Inicializar indicadores con el estado actual (si hay eventos precargados)
            actualizarIndicadoresAgenda();
        });
    });

    function persistMoveOrResize(info) {
        const routes = getConfig().routes || {};
        const grupoId = getConfig().grupoId;
        const evt = info.event;
        const id = evt.id;
        if (!grupoId || !id) return;
        $.ajax({
            url: routes.update(grupoId, id),
            method: 'POST', // usar POST con spoofing para PUT
            data: { _method: 'PUT', start: evt.start.toISOString(), end: (evt.end ? evt.end.toISOString() : evt.start.toISOString()) },
            headers: { 'X-CSRF-TOKEN': getToken() },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo actualizar';
                alert(msg);
                info.revert();
                actualizarIndicadoresAgenda();
            }
        });
    }
})();


function agendaColores() {
    const bgEl = document.getElementById('color_fondo');
    const txEl = document.getElementById('color_texto');
    if (!bgEl || !txEl) return;

    const color_fondo = bgEl.value;
    const color_texto = txEl.value;

    // Persistir en LocalStorage
    try {
        localStorage.setItem('agenda_event_color', color_fondo || '');
        localStorage.setItem('agenda_event_text_color', color_texto || '');
    } catch (_) { /* noop */ }

    // Usar la instancia real del calendario si existe
    const cal = (window.GrupoAgenda && window.GrupoAgenda.calendar) || (typeof calendar !== 'undefined' ? calendar : null);
    if (!cal) return; // aún no creado

    cal.setOption('eventColor', color_fondo);
    cal.setOption('eventTextColor', color_texto);

    // Actualizar variables CSS en el contenedor del calendario para reflejar cambios inmediatos
    try {
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            calendarEl.style.setProperty('--agenda-event-bg', color_fondo);
            calendarEl.style.setProperty('--agenda-event-text', color_texto);
        }
    } catch (_) { /* noop */ }

    // Forzar actualización de eventos existentes
    try {
        cal.getEvents().forEach(function(evt) {
            // Estas props afectan al render del evento actual
            evt.setProp('backgroundColor', color_fondo);
            evt.setProp('borderColor', color_fondo);
            evt.setProp('textColor', color_texto);
        });
    } catch (_) { /* noop */ }
}

// Escuchar cambios en los selectores de color
$(document).on('change', '.select-color', function () {
    agendaColores();
});