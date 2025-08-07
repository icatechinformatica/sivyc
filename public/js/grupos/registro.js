// ! Seccion Informacion general
$("#guardar_info_general").on('click', function (e) {
    e.preventDefault();
    if ($("#info_general_form").valid()) {
        alert("Información general guardada correctamente.");
    }
});

// ! Ubicación
$("#guardar_ubicacion").on('click', function (e) {
    e.preventDefault();
    if ($("#ubicacion_form").valid()) {
        alert("Ubicación guardada correctamente.");
    }
});

// ! Organismo Publico
$("#guardar_organismo").on('click', function (e) {
    e.preventDefault();
    if ($("#organismo_form").valid()) {
        alert("Organismo guardado correctamente.");
    }
});

// ! Opciones adicionales
$("#guardar_opciones").on('click', function (e) {
    e.preventDefault();
    if ($("#opciones_form").valid()) {
        alert("Opciones adicionales guardadas correctamente.");
    }
});

// ! Filtro dinámico de localidades por municipio
$('#municipio-select').on('change', function () {
    const municipioId = $(this).val();
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
                        localidadSelect.append(`<option value="${localidad.id}">${localidad.localidad}</option>`);
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
});

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

const medio_virtual = $('#medio_virtual');
const enlace_virtual = $('#enlace_virtual');

$('#imparticion').on('change', function () {
    if (this.value == 2) {
        medio_virtual.prop('disabled', false);
        enlace_virtual.prop('disabled', false);
    }else {
        medio_virtual.prop('disabled', true);
        enlace_virtual.prop('disabled', true);
        medio_virtual.val('');
        enlace_virtual.val('');
    }
});
