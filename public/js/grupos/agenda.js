
; (function () {
    function getConfig() { return window.GrupoAgenda || {}; }
    function esEditable() { try { return !!getConfig().editable; } catch (_) { return true; } }
    function getToken() { return (window.registroBladeVars && window.registroBladeVars.csrfToken) || document.querySelector('meta[name="csrf-token"]')?.content; }

    let calendar = null;
    let selectedRange = null; // { start, end, startStr, endStr } rango seleccionado (end exclusivo)
    // Flag para navegar automáticamente a la primera fecha del grupo solo una vez
    let gotoInicialHecho = false;

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

    // Horas máximas del curso como número decimal (p.ej. 40)
    function obtenerMaxHoras() {
        let maxHoras = parseFloat(getConfig().maxHoras);
        if (!isFinite(maxHoras)) {
            const $horasMaximas = document.getElementById('fc-horas-maximas');
            if ($horasMaximas && $horasMaximas.textContent) {
                maxHoras = horaADecimal(($horasMaximas.textContent || '').trim());
            }
        }
        return isFinite(maxHoras) ? maxHoras : NaN;
    }

    // Cálculo de minutos de un evento múltiples días según franja diaria [start.hora, end.hora]
    function minutosEvento(evt) {
        const s = evt.start;
        const e = evt.end || evt.start;
        if (!(s instanceof Date) || !(e instanceof Date)) return 0;
        const MS_PER_DAY = 24 * 60 * 60 * 1000;
        const ds = new Date(s.getFullYear(), s.getMonth(), s.getDate());
        const de = new Date(e.getFullYear(), e.getMonth(), e.getDate());
        const dias = Math.max(1, Math.round((de - ds) / MS_PER_DAY) + 1);
        const minsDia = Math.max(0, (e.getHours() * 60 + e.getMinutes()) - (s.getHours() * 60 + s.getMinutes()));
        return dias * minsDia;
    }

    function minutosTotalesAgenda(cal) {
        const eventos = (cal?.getEvents?.() || []).filter(function (e) { return !esConector(e); });
        return eventos.reduce((acc, evt) => acc + minutosEvento(evt), 0);
    }

    // Minutos que aportaría una selección al aplicar horario HH:MM por día, desde start hasta endExclusive
    function minutosDeSeleccion(startDate, endDateExclusive, horaInicio, horaFin) {
        try {
            const lastDay = new Date(endDateExclusive);
            lastDay.setDate(lastDay.getDate() - 1);
            if (lastDay < startDate) return 0;
            const minsDia = Math.max(0, (parseInt(horaFin.slice(0,2))*60 + parseInt(horaFin.slice(3,5))) - (parseInt(horaInicio.slice(0,2))*60 + parseInt(horaInicio.slice(3,5))));
            const MS_PER_DAY = 24 * 60 * 60 * 1000;
            const ds = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
            const de = new Date(lastDay.getFullYear(), lastDay.getMonth(), lastDay.getDate());
            const dias = Math.max(1, Math.round((de - ds) / MS_PER_DAY) + 1);
            return minsDia * dias;
        } catch (_) { return 0; }
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

    // Parser seguro para fechas tipo 'YYYY-MM-DD' en zona horaria local (evita parseo UTC)
    function parseFechaLocal(input) {
        if (input instanceof Date) {
            return new Date(input.getFullYear(), input.getMonth(), input.getDate());
        }
        if (typeof input === 'string') {
            const base = input.split('T')[0];
            const parts = base.split('-');
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10);
            const d = parseInt(parts[2], 10);
            if (isFinite(y) && isFinite(m) && isFinite(d)) {
                return new Date(y, m - 1, d);
            }
        }
        // fallback: fecha inválida
        return new Date(NaN);
    }


    // Actualiza los indicadores del panel lateral de la agenda
    function actualizarIndicadoresAgenda() {
        try {
            const cal = (window.GrupoAgenda && window.GrupoAgenda.calendar) || calendar;
            if (!cal) return;

            const eventos = cal.getEvents();

            // Para eventos que abarcan varios días, NO debemos tomar la diferencia directa end - start
            // (eso contaría noches completas). En su lugar: minutosPorDía x númeroDeDías (inclusive).
            const MS_PER_DAY = 24 * 60 * 60 * 1000;
            const diffDiasInclusivo = function (s, e) {
                const ds = new Date(s.getFullYear(), s.getMonth(), s.getDate());
                const de = new Date(e.getFullYear(), e.getMonth(), e.getDate());
                const d = Math.round((de - ds) / MS_PER_DAY) + 1;
                return Math.max(1, d);
            };
            const minutosPorDia = function (s, e) {
                // Usa horas:minutos de start y end como franja diaria
                const ini = (s.getHours() * 60) + s.getMinutes();
                const fin = (e.getHours() * 60) + e.getMinutes();
                return Math.max(0, fin - ini);
            };

            const acum = eventos.reduce((acc, evt) => {
                const s = evt.start;
                const e = evt.end || evt.start;
                if (!(s instanceof Date) || !(e instanceof Date)) return acc;
                const minsDia = minutosPorDia(s, e);
                const dias = diffDiasInclusivo(s, e);
                return {
                    minutos: acc.minutos + (minsDia * dias),
                    dias: acc.dias + dias
                };
            }, { minutos: 0, dias: 0 });

            const horasTotales = acum.minutos / 60;

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
            // Mostrar días totales reales (suma de días de cada evento)
            if ($diasHoras) $diasHoras.textContent = String(acum.dias);

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
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                locale: 'es',
                firstDay: 0, // domingo
                height: 'auto',
                buttonText: { today: 'Hoy' },
                selectable: esEditable(),
                selectMirror: true,
                editable: esEditable(),
                eventOverlap: true,
                // Mostrar eventos como bloques en mes para poder colorear fondo
                views: {
                    dayGridMonth: { eventDisplay: 'block' }
                },
                // eventDidMount sin estilos inline para permitir cambios dinámicos por CSS vars
                eventDidMount: function (info) { /* no-op */ },
                // Refrescar indicadores al ajustar el conjunto de eventos
                eventsSet: function () {
                    actualizarIndicadoresAgenda();
                    // Ir a la primera fecha (start más antiguo) solo la primera vez que haya eventos
                    if (!gotoInicialHecho && calendar) {
                        try {
                            const evs = calendar.getEvents();
                            if (evs && evs.length) {
                                let primerInicio = null;
                                evs.forEach(function (e) {
                                    const s = e.start;
                                    if (s instanceof Date && !isNaN(s)) {
                                        if (!primerInicio || s < primerInicio) primerInicio = s;
                                    }
                                });
                                if (primerInicio) {
                                    calendar.gotoDate(primerInicio);
                                    gotoInicialHecho = true;
                                }
                            }
                        } catch (_) { /* noop */ }
                    }
                },
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
                // Capturar selección de días; no crear evento inmediatamente
                select: function (arg) {
                    if (!esEditable()) { calendar.unselect(); return; }
                    if (!grupoId) { calendar.unselect(); return; }
                    // Guardar tanto los Date nativos como sus strings
                    selectedRange = { start: arg.start, end: arg.end, startStr: arg.startStr, endStr: arg.endStr };
                    try { document.getElementById('btn-agenda-aplicar-seleccion').disabled = false; } catch (_) { }
                },
                // Seleccionar evento: solo mostrar detalles, no eliminar
                eventClick: function (info) {
                    const evt = info.event;
                    try {
                        // Marcar selección visual opcional
                        calendar.getEvents().forEach(e => e.setProp('classNames', (e === evt) ? ['fc-selected'] : []));
                    } catch (_) { }
                    // Mostrar detalles del periodo
                    try {
                        const det = document.getElementById('fc-seleccion-detalle');
                        const btnDel = document.getElementById('btn-agenda-eliminar-periodo');
                        if (det) {
                            const s = evt.start; const e = evt.end || evt.start;
                            const pad = (n) => String(n).padStart(2, '0');
                            const dias = ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb'];
                            const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
                            const fmtFecha = (d) => `${dias[d.getDay()]} ${pad(d.getDate())} ${meses[d.getMonth()]} ${d.getFullYear()}`;
                            const fmtHora = (d) => `${pad(d.getHours())}:${pad(d.getMinutes())}`;
                            const mismoDia = s.getFullYear()===e.getFullYear() && s.getMonth()===e.getMonth() && s.getDate()===e.getDate();
                            if (mismoDia) {
                                det.textContent = `${fmtFecha(s)}, ${fmtHora(s)} a ${fmtHora(e)}`;
                            } else {
                                // Variante amigable: fechas y luego franja horaria
                                det.textContent = `${fmtFecha(s)} a ${fmtFecha(e)} de ${fmtHora(s)} a ${fmtHora(e)}`;
                                // Alternativa (si se prefiere con coma antes de horas en ambos extremos):
                                // det.textContent = `${fmtFecha(s)}, ${fmtHora(s)} a ${fmtFecha(e)}, ${fmtHora(e)}`;
                            }
                        }
                        if (btnDel) {
                            // Mantener deshabilitado en modo solo lectura
                            if (!esEditable()) {
                                btnDel.disabled = true;
                                btnDel.dataset.agendaId = '';
                            } else {
                                btnDel.disabled = !evt.id;
                                btnDel.dataset.agendaId = evt.id || '';
                            }
                        }
                    } catch (_) { /* noop */ }
                },
                // Drag & drop con validación de horas máximas
                eventDrop: function (info) {
                    if (!esEditable()) { info.revert(); return; }
                    const maxHoras = obtenerMaxHoras();
                    if (isFinite(maxHoras)) {
                        const minutos = minutosTotalesAgenda(calendar);
                        if (minutos > maxHoras * 60 + 0.5) { // tolerancia mínima
                            alert('La modificación excede las horas máximas del curso.');
                            info.revert();
                            actualizarIndicadoresAgenda();
                            return;
                        }
                    }
                    persistMoveOrResize(info);
                    actualizarIndicadoresAgenda();
                },
                eventResize: function (info) {
                    if (!esEditable()) { info.revert(); return; }
                    const maxHoras = obtenerMaxHoras();
                    if (isFinite(maxHoras)) {
                        const minutos = minutosTotalesAgenda(calendar);
                        if (minutos > maxHoras * 60 + 0.5) {
                            alert('La modificación excede las horas máximas del curso.');
                            info.revert();
                            actualizarIndicadoresAgenda();
                            return;
                        }
                    }
                    persistMoveOrResize(info);
                    actualizarIndicadoresAgenda();
                },
                // Cambios directos sobre props del evento
                eventAdd: function () { actualizarIndicadoresAgenda(); },
                eventChange: function () { actualizarIndicadoresAgenda(); },
                eventRemove: function () { actualizarIndicadoresAgenda(); }
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

            // Inicializar conectores e indicadores con el estado actual (si hay eventos precargados)
            setTimeout(function () { actualizarIndicadoresAgenda(); }, 0);
        });

        // Botón: aplicar horario al rango seleccionado
        $(document).on('click', '#btn-agenda-aplicar-seleccion', function () {
            if (!esEditable()) return; // bloquear creación en modo solo lectura
            const cfg = getConfig();
            const routes = cfg.routes || {};
            const grupoId = cfg.grupoId;
            if (!grupoId || !routes.store) return;
            if (!selectedRange) { alert('Primero selecciona uno o varios días en el calendario.'); return; }

            const horaInicio = document.getElementById('agenda_hora_inicio')?.value || '08:00';
            const horaFin = document.getElementById('agenda_hora_fin')?.value || '10:00';

            const incluirHoraAlimentos = document.getElementById('alimentos')?.checked ? 1 : 0;
            // Validaciones básicas
            if (!/^\d{2}:\d{2}$/.test(horaInicio) || !/^\d{2}:\d{2}$/.test(horaFin)) { alert('Formato de hora inválido.'); return; }
            if (horaInicio >= horaFin) { alert('La hora fin debe ser mayor que la hora inicio.'); return; }

            // Construir lista de días desde inicio hasta fin (exclusivo) de forma local y segura
            const startDate = parseFechaLocal(selectedRange.start || selectedRange.startStr);
            const endDateExclusive = parseFechaLocal(selectedRange.end || selectedRange.endStr);
            console.log('Rango seleccionado:', startDate, ' a ', endDateExclusive);
            // Convertir selección a un único periodo [primer_día HH:MM, último_día HH:MM]
            if (!(startDate instanceof Date) || !(endDateExclusive instanceof Date) || isNaN(startDate) || isNaN(endDateExclusive)) {
                alert('Rango de fechas inválido.'); return;
            }
            // último día = end exclusivo - 1 día
            const lastDay = new Date(endDateExclusive);
            lastDay.setDate(lastDay.getDate() - 1);
            if (lastDay < startDate) { alert('No hay días seleccionados.'); return; }

            // Validación de horas máximas antes de enviar
            const maxHoras = obtenerMaxHoras();
            if (isFinite(maxHoras)) {
                const minutosActuales = minutosTotalesAgenda(calendar);
                const minutosNuevos = minutosDeSeleccion(startDate, endDateExclusive, horaInicio, horaFin);
                if (minutosActuales + minutosNuevos > maxHoras * 60 + 0.5) {
                    const restante = Math.max(0, maxHoras * 60 - minutosActuales);
                    alert('La selección excede las horas máximas del curso. Restantes: ' + decimalAHora(restante/60));
                    return;
                }
            }
            const y1 = startDate.getFullYear();
            const m1 = String(startDate.getMonth() + 1).padStart(2, '0');
            const d1 = String(startDate.getDate()).padStart(2, '0');
            const y2 = lastDay.getFullYear();
            const m2 = String(lastDay.getMonth() + 1).padStart(2, '0');
            const d2 = String(lastDay.getDate()).padStart(2, '0');
            const startISO = `${y1}-${m1}-${d1}T${horaInicio}:00`;
            const endISO = `${y2}-${m2}-${d2}T${horaFin}:00`;

            // Deshabilitar botón mientras procesa
            const $btn = $('#btn-agenda-aplicar-seleccion');
            $btn.prop('disabled', true).text('Aplicando...');

            // Enviar un solo registro por periodo
            const payload = { start: startISO, end: endISO, hora_alimentos: incluirHoraAlimentos };
            $.ajax({
                url: routes.store(grupoId),
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getToken() },
                data: payload,
                success: function (evt) {
                    try {
                        const start = evt?.start || startISO;
                        const end = evt?.end || endISO;
                        const id = evt?.id || evt?.agenda_id || undefined;
                        calendar.addEvent({ id, title: evt?.title || 'Sesión', start, end });
                        if (typeof agendaColores === 'function') agendaColores();
                    } catch (_) { /* noop */ }
                },
                error: function (xhr) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo crear el periodo';
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Aplicar a días seleccionados');
                    selectedRange = null;
                    if (calendar) calendar.unselect();
                    setTimeout(function () { actualizarIndicadoresAgenda(); }, 0);
                }
            });
        });
    });

    // Botón eliminar periodo seleccionado
    $(document).on('click', '#btn-agenda-eliminar-periodo', function () {
        if (!esEditable()) return; // bloquear eliminación en modo solo lectura
        const routes = getConfig().routes || {};
        const grupoId = getConfig().grupoId;
        const btn = this;
        const agendaId = btn.dataset.agendaId;
        if (!grupoId || !agendaId) return;
        if (!confirm('¿Eliminar el periodo seleccionado?')) return;
        $.ajax({
            url: routes.destroy(grupoId, agendaId),
            method: 'POST',
            data: { _method: 'DELETE' },
            headers: { 'X-CSRF-TOKEN': getToken() },
            success: function () {
                try {
                    const ev = (window.GrupoAgenda?.calendar || calendar)?.getEventById(agendaId);
                    if (ev) ev.remove();
                    const det = document.getElementById('fc-seleccion-detalle');
                    if (det) det.textContent = 'Ninguno';
                    btn.disabled = true;
                    btn.dataset.agendaId = '';
                    actualizarIndicadoresAgenda();
                    setTimeout(function () { /* ya no se usan conectores */ }, 0);
                } catch (_) { /* noop */ }
            },
            error: function () { alert('No se pudo eliminar'); }
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
        cal.getEvents().forEach(function (evt) {
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