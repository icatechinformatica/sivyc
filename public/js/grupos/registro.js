// ! Seccion Informacion general
$("#guardar_info_general").on('click', function (e) {
    e.preventDefault();
    if ($("#info_general_form").valid()) {
        const formData = new FormData();
        if ($('#id_grupo').val()) {
            formData.append('id_grupo', $('#id_grupo').val());
        }
        formData.append('seccion', 'info_general');
        formData.append('id_imparticion', $('#imparticion').val());
        formData.append('id_modalidad', $('#modalidad').val());
        formData.append('id_unidad', $('#unidad_accion_movil').val());
        formData.append('id_servicio', $('#servicio').val());
        formData.append('id_curso', $('#curso').val());
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Ubicación
$("#guardar_ubicacion").on('click', function (e) {
    e.preventDefault();
    if ($("#ubicacion_form").valid()) {
        const formData = new FormData();
        if ($('#id_grupo').val()) {
            formData.append('id_grupo', $('#id_grupo').val());
        }
        formData.append('seccion', 'ubicacion');
        formData.append('id_municipio', $('#municipio-select').val());
        formData.append('id_localidad', $('#localidad-select').val());
        formData.append('nombre_lugar', $('#nombre_lugar').val());
        formData.append('calle_numero', $('#calle_numero').val());
        formData.append('colonia', $('#colonia').val());
        formData.append('codigo_postal', $('#codigo_postal').val());
        formData.append('referencias', $('#referencias').val());
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Organismo Publico
$("#guardar_organismo").on('click', function (e) {
    e.preventDefault();
    if ($("#organismo_form").valid()) {
        const formData = new FormData();
        if ($('#id_grupo').val()) {
            formData.append('id_grupo', $('#id_grupo').val());
        }
        formData.append('seccion', 'organismo');
        formData.append('id_organismo_publico', $('#organismo_publico').val());
        formData.append('organismo_representante', $('#nombre_representante').val());
        formData.append('organismo_telefono_representante', $('#telefono_representante').val());
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Opciones adicionales
$("#guardar_opciones").on('click', function (e) {
    e.preventDefault();
    if ($("#opciones_form").valid()) {
        const formData = new FormData();
        if ($('#id_grupo').val()) {
            formData.append('id_grupo', $('#id_grupo').val());
        }
        formData.append('seccion', 'opciones');
        formData.append('convenio_especifico', $('#convenio_especifico').val());
        formData.append('fecha_convenio', $('#fecha_convenio').val());
        formData.append('_token', registroBladeVars.csrfToken);
        formData.append('id_imparticion', $('#imparticion').val());
        if ($('#imparticion').val() == 2) {
            formData.append('medio_virtual', $('#medio_virtual').val());
            formData.append('enlace_virtual', $('#enlace_virtual').val());
        }
        guardarSeccion(formData);
    }
});

// ! Filtro dinámico de localidades por municipio
function cargarLocalidades(municipioId, localidadSeleccionada = null) {
    const localidadSelect = $('#localidad-select');

    if (municipioId) {
        // Mostrar loading
        localidadSelect.html('<option value="">Cargando...</option>').prop('disabled', true);

        // Hacer petición AJAX
        $.ajax({
            url: `/grupos/localidades/${municipioId}`,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                localidadSelect.html('<option value="">SELECCIONAR LOCALIDAD</option>');

                if (data.length > 0) {
                    $.each(data, function (index, localidad) {
                        const selected = localidadSeleccionada && localidad.id == localidadSeleccionada ? 'selected' : '';
                        localidadSelect.append(`<option value="${localidad.id}" ${selected}>${localidad.localidad}</option>`);
                    });
                    localidadSelect.prop('disabled', false);
                } else {
                    localidadSelect.html('<option value="">No hay localidades disponibles</option>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar localidades:', error);
                localidadSelect.html('<option value="">Error al cargar localidades</option>');
            }
        });
    } else {
        // Si no hay municipio seleccionado, resetear localidades
        localidadSelect.html('<option value="">SELECCIONAR MUNICIPIO PRIMERO</option>').prop('disabled', true);
    }
}

$('#municipio-select').on('change', function () {
    const municipioId = $(this).val();
    cargarLocalidades(municipioId);
});

// ! Inicializar localidades al cargar la página si hay municipio preseleccionado
const municipioPreseleccionado = $('#municipio-select').val();
const localidadPreseleccionada = $('#localidad-select').val();

// ! Relleno automático de representante al seleccionar organismo público
$('#organismo_publico').on('change', function () {
    const organismoId = $(this).val();
    const nombreRepresentante = $('#nombre_representante');
    const telefonoRepresentante = $('#telefono_representante');

    if (organismoId) {
        // Mostrar loading
        nombreRepresentante.val('Cargando...').prop('disabled', true);
        telefonoRepresentante.val('Cargando...').prop('disabled', true);

        // Hacer petición AJAX
        $.ajax({
            url: `/grupos/organismo/${organismoId}`,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                nombreRepresentante.val(data.nombre_titular || '').prop('disabled', false);
                telefonoRepresentante.val(data.telefono || '').prop('disabled', false);
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar información del organismo:', error);
                nombreRepresentante.val('').prop('disabled', false);
                telefonoRepresentante.val('').prop('disabled', false);
                alert('Error al cargar la información del representante. Por favor, intente nuevamente.');
            }
        });
    } else {
        // Si no hay organismo seleccionado, limpiar campos y habilitar edición manual
        nombreRepresentante.val('').prop('disabled', false);
        telefonoRepresentante.val('').prop('disabled', false);
    }
});

// ! Manejo de Imparticion a DISTANCIA: MEDIO VIRTUAL y ENLACE VIRTUAL
obtenerImparticion();

$('#imparticion').on('change', function () {
    obtenerImparticion();
});

function obtenerImparticion() {
    const medio_virtual = $('#medio_virtual');
    const enlace_virtual = $('#enlace_virtual');

    if (this.value == 2) {
        medio_virtual.prop('disabled', false);
        enlace_virtual.prop('disabled', false);
    } else {
        medio_virtual.prop('disabled', true);
        enlace_virtual.prop('disabled', true);
        medio_virtual.val('');
        enlace_virtual.val('');
    }
}


// ! Ajax para guardar los datos del formulario
const guardarSeccion = (formData) => {
    $.ajax({
        url: '/grupos/guardar/seccion/grupo',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
            if (response.success) {
                if (!$('#id_grupo').val()) {
                    window.location.href = window.location.pathname + '?id=' + response.grupo_id;
                }
                const notyf = new Notyf({
                    position: { x: 'right', y: 'top' },
                    duration: 3000,
                });
                notyf.open(
                    {
                        type: 'success', className: 'notyf-success',
                        message: 'Sección guardada correctamente.'
                    }
                );
                // Avanzar a la siguiente sección en la stepbar reutilizando lógica global
                const seccionActual = formData.get('seccion');
                if (typeof window.moverSiguienteSeccionGrupo === 'function') {
                    window.moverSiguienteSeccionGrupo(seccionActual);
                }
            }
        },
        error: function (error) {
            console.error('Error al guardar la sección:', error);
        }
    });
}