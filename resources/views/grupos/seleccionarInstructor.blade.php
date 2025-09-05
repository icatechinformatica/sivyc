{{-- Modal de Búsqueda de Instructores --}}
<div class="modal fade" id="modalSeleccionarInstructor" tabindex="-1" role="dialog"
    aria-labelledby="modalSeleccionarInstructorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSeleccionarInstructorLabel">
                    <i class="fa fa-graduation-cap mr-2"></i>Seleccionar Instructor
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Buscador --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="buscarInstructor" placeholder="Buscar por nombre del instructor..." autocomplete="off" style="text-transform: uppercase;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="limpiarBusqueda">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mensaje de carga --}}
                <div id="cargandoInstructores" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Buscando instructores...</p>
                </div>

                {{-- Sin resultados --}}
                <div id="sinResultados" class="text-center py-4" style="display: none;">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No se encontraron instructores con ese nombre</p>
                </div>

                {{-- Lista de instructores --}}
                <div id="listaInstructores" class="row">
                    {{-- Los instructores se cargarán dinámicamente aquí --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript para el modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    let instructorSeleccionado = null;

    // Referencias a elementos del DOM
    const modal = $('#modalSeleccionarInstructor');
    const btnInstructor = $('#btn-instructor');
    const buscarInput = $('#buscarInstructor');
    const limpiarBtn = $('#limpiarBusqueda');
    const listaContainer = $('#listaInstructores');
    const cargandoDiv = $('#cargandoInstructores');
    const sinResultadosDiv = $('#sinResultados');

    // Abrir modal cuando se hace clic en el botón instructor
    btnInstructor.on('click', function() {
        modal.modal('show');
        cargarInstructores();
    });

    // Función para obtener las iniciales del nombre
    function obtenerIniciales(nombre) {
        return nombre.split(' ').map(palabra => palabra.charAt(0).toUpperCase()).join('').substring(0, 2);
    }

    // Función para cargar instructores desde el backend
    function cargarInstructores(filtro = '') {
        console.log('Cargando instructores con filtro:', filtro);
        mostrarCargando(true);
        
        // Hacer petición AJAX al backend
        $.get('{{ route("grupos.instructores.buscar") }}', {
            busqueda: filtro,
            limite: 20
        })
        .done(function(response) {
            console.log('Respuesta recibida:', response);
            if (response.success && response.data) {
                console.log('Instructores encontrados:', response.data.length);
                mostrarInstructores(response.data);
            } else {
                console.error('Error en respuesta:', response.message || 'Respuesta inválida');
                mostrarError('No se pudieron cargar los instructores.');
            }
            mostrarCargando(false);
        })
        .fail(function(xhr, status, error) {
            console.error('Error AJAX:', {xhr, status, error});
            console.error('Respuesta del servidor:', xhr.responseText);
            mostrarError('Error de conexión. Inténtalo de nuevo.');
            mostrarCargando(false);
        });
    }

    // Función para mostrar mensaje de error
    function mostrarError(mensaje) {
        listaContainer.empty();
        listaContainer.html(`
            <div class="col-12">
                <div class="alert alert-warning text-center" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p class="mb-0">${mensaje}</p>
                </div>
            </div>
        `);
        sinResultadosDiv.hide();
    }

    // Función para mostrar el indicador de carga
    function mostrarCargando(mostrar) {
        if (mostrar) {
            cargandoDiv.show();
            listaContainer.hide();
            sinResultadosDiv.hide();
        } else {
            cargandoDiv.hide();
            listaContainer.show();
        }
    }

    // Función para mostrar la lista de instructores
    function mostrarInstructores(instructores) {
        listaContainer.empty();
        
        if (instructores.length === 0) {
            listaContainer.hide();
            sinResultadosDiv.show();
            return;
        }

        sinResultadosDiv.hide();
        
        instructores.forEach(instructor => {
            const iniciales = obtenerIniciales(instructor.nombre);
            const item = `
                <div class="col-12 mb-2">
                    <div class="instructor-item d-flex align-items-center p-3 border rounded" data-instructor-id="${instructor.id}">
                        <div class="instructor-avatar-small mr-3">
                            ${iniciales}
                        </div>
                        <div class="instructor-info flex-grow-1">
                            <h6 class="mb-1 font-weight-bold">${instructor.nombre}</h6>
                            <div class="d-flex flex-wrap">
                                <small class="text-muted mr-3">
                                    <i class="fa fa-briefcase mr-1"></i>${instructor.especialidad}
                                </small>
                                <small class="text-muted mr-3">
                                    <i class="fa fa-clock mr-1"></i>${instructor.experiencia}
                                </small>
                                <small class="text-muted">
                                    <i class="fa fa-envelope mr-1"></i>${instructor.email}
                                </small>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm btn-seleccionar-instructor ml-3" 
                                data-instructor='${JSON.stringify(instructor)}'>
                            <i class="fa fa-check mr-1"></i>Seleccionar
                        </button>
                    </div>
                </div>
            `;
            listaContainer.append(item);
        });
    }

    // Event listener para búsqueda en tiempo real
    let timeoutBusqueda;
    buscarInput.on('input', function() {
        clearTimeout(timeoutBusqueda);
        const filtro = $(this).val().trim();
        
        timeoutBusqueda = setTimeout(() => {
            cargarInstructores(filtro);
        }, 300);
    });

    // Limpiar búsqueda
    limpiarBtn.on('click', function() {
        buscarInput.val('');
        cargarInstructores();
    });

    // Seleccionar instructor
    $(document).on('click', '.btn-seleccionar-instructor', function(e) {
        e.stopPropagation();
        
        const instructor = JSON.parse($(this).attr('data-instructor'));
        instructorSeleccionado = instructor;
        
        // Marcar como seleccionado visualmente
        $('.instructor-item').removeClass('seleccionado');
        $(this).closest('.instructor-item').addClass('seleccionado');
        
        // Actualizar el botón principal
        actualizarBotonInstructor(instructor);
        
        // Cerrar modal después de un breve delay
        setTimeout(() => {
            modal.modal('hide');
        }, 500);
    });

    // Función para actualizar el botón principal del instructor
    function actualizarBotonInstructor(instructor) {
        const nuevoTexto = `
            <div class="d-flex align-items-center">
                <i class="fa fa-graduation-cap mr-3 fa-lg"></i>
                <div class="d-flex flex-column align-items-start text-left">
                    <span class="font-weight-bold mb-1">${instructor.nombre}</span>
                    <small class="text-muted">${instructor.especialidad}</small>
                </div>
                <i class="fa fa-check-circle ml-auto text-success fa-lg"></i>
            </div>
        `;
        
        btnInstructor.html(nuevoTexto)
                   .removeClass('btn-instructor')
                   .addClass('btn-success btn-instructor-actualizado')
                   .css({
                       'text-align': 'left',
                       'white-space': 'normal',
                       'padding': '15px 20px'
                   });
        
        // Remover campos anteriores y agregar nuevo campo oculto
        $('#form-instructor input[name="instructor_id"]').remove();
        $('#form-instructor').append(`<input type="hidden" name="instructor_id" value="${instructor.id}">`);
        
        // Mostrar notificación de éxito
        mostrarNotificacionExito(instructor.nombre);
        
        console.log('Instructor seleccionado:', instructor);
    }

    // Función para mostrar notificación de éxito
    function mostrarNotificacionExito(nombreInstructor) {
        // Crear y mostrar toast/notificación
        const toast = $(`
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                <i class="fa fa-check-circle mr-2"></i>
                <strong>¡Instructor seleccionado!</strong><br>
                <small>${nombreInstructor}</small>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        
        $('body').append(toast);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            toast.fadeOut(400, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Limpiar búsqueda al abrir el modal
    modal.on('shown.bs.modal', function() {
        buscarInput.val('').focus();
        // Resetear selección visual
        $('.instructor-item').removeClass('seleccionado');
    });

    // Funcionalidad de teclas
    buscarInput.on('keydown', function(e) {
        if (e.key === 'Escape') {
            modal.modal('hide');
        }
    });

    // Mejorar accesibilidad
    modal.on('hidden.bs.modal', function() {
        btnInstructor.focus();
    });
});
</script>

