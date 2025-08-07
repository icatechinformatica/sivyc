
document.addEventListener('DOMContentLoaded', function () {
    let calendar;
    $('#modalFullCalendar').on('shown.bs.modal', function () {
        if (!calendar) {
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                height: 500
            });
            calendar.render();
        }
    });
});

$(document).on('click', '#btn-guardar', function () {
    window.location.href = "{!! route('grupos.asignar.alumnos') !!}";
});

// Mostrar el modal al hacer clic en el botón AGENDA
$(document).on('click', '#btn-agenda', function () {
    $('#modalFullCalendar').modal('show');
});

document.addEventListener('DOMContentLoaded', function () {
    let calendar;
    $('#modalFullCalendar').on('shown.bs.modal', function () {
        if (!calendar) {
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                views: {
                    dayGridMonth: {
                        titleFormat: {
                            year: 'numeric',
                            month: 'long'
                        }
                    },
                    timeGridWeek: {
                        titleFormat: {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }
                    },
                    timeGridDay: {
                        titleFormat: {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            weekday: 'long'
                        }
                    }
                },
                locale: 'es',
                firstDay: 7,
                height: 'auto',
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                events: [{
                    title: 'Reunión de trabajo',
                    start: '2024-01-15',
                    color: '#007bff'
                },
                {
                    title: 'Cita médica',
                    start: '2024-01-20T10:00:00',
                    end: '2024-01-20T11:00:00',
                    color: '#28a745'
                },
                {
                    title: 'Evento de todo el día',
                    start: '2024-01-25',
                    allDay: true,
                    color: '#ffc107'
                }
                ],
                selectable: true,
                selectMirror: true,
                select: function (arg) {
                    var title = prompt('Título del evento:');
                    if (title) {
                        calendar.addEvent({
                            title: title,
                            start: arg.start,
                            end: arg.end,
                            allDay: arg.allDay
                        });
                    }
                    calendar.unselect();
                },
                eventClick: function (arg) {
                    if (confirm('¿Eliminar evento "' + arg.event.title + '"?')) {
                        arg.event.remove();
                    }
                },
                editable: true,
                dayCellDidMount: function (arg) {
                    arg.el.addEventListener('mouseenter', function () {
                        arg.el.style.backgroundColor = '#f8f9fa';
                        arg.el.style.cursor = 'pointer';
                    });
                    arg.el.addEventListener('mouseleave', function () {
                        arg.el.style.backgroundColor = '';
                        arg.el.style.cursor = '';
                    });
                },
                eventMouseEnter: function (info) {
                    info.el.style.transform = 'scale(1.05)';
                    info.el.style.transition = 'transform 0.2s';
                    info.el.style.zIndex = '999';
                    info.el.title = `${info.event.title}\nInicio: ${info.event.start.toLocaleString()}`;
                },
                eventMouseLeave: function (info) {
                    info.el.style.transform = 'scale(1)';
                    info.el.style.zIndex = '';
                },
                dateClick: function (arg) {
                    arg.dayEl.style.backgroundColor = '#e3f2fd';
                    setTimeout(function () {
                        arg.dayEl.style.backgroundColor = '';
                    }, 1000);
                },
                eventDrop: function (info) {
                    alert(`Evento "${info.event.title}" movido a ${info.event.start.toLocaleDateString()}`);
                },
                eventResize: function (info) {
                    alert(`Evento "${info.event.title}" redimensionado`);
                },
                datesSet: function (info) {
                    // Puedes agregar lógica aquí si necesitas reaccionar al cambio de vista
                }
            });
            calendar.render();
        }
    });
});
