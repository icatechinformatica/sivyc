// Utilidades para obtener valores soportando IDs nuevos y antiguos
function firstNonEmpty(selectorList) {
    for (const sel of selectorList) {
        const $el = $(sel);
        if ($el.length) {
            const val = $el.val();
            if (val !== undefined && val !== null && String(val).trim() !== '') {
                return val;
            }
        }
    }
    return null;
}

function getSelectedUnitsArray() {
    // IDs nuevos: #unidades_necesitan (posible multiselect)
    const $multi = $('#unidades_necesitan');
    if ($multi.length) {
        const vals = $multi.val();
        if (Array.isArray(vals) && vals.length > 0) {
            return vals.filter(v => v !== '' && v !== null);
        }
        if (typeof vals === 'string' && vals) {
            return [vals];
        }
    }
    // ID antiguo: #unidad_accion_movil (single)
    const single = firstNonEmpty(['#unidad_accion_movil']);
    return single ? [single] : [];
}

function getFiltroParams() {
    // Mapear IDs nuevos a los parámetros esperados por el backend
    // id_imparticion <= id_tipo_curso (nuevo) | #imparticion (antiguo)
    const id_imparticion = firstNonEmpty(['#id_tipo_curso', '#imparticion']);
    // id_modalidad <= id_modalidad_curso (nuevo) | #modalidad (antiguo)
    const id_modalidad = firstNonEmpty(['#id_modalidad_curso', '#modalidad']);
    // id_servicio <= id_categoria_formacion (nuevo) | #servicio (antiguo)
    const id_servicio = firstNonEmpty(['#id_categoria_formacion', '#servicio']);
    // id_unidad|es <= #unidades_necesitan[] (nuevo, puede ser arreglo) | #unidad_accion_movil (antiguo)
    const unidades = getSelectedUnitsArray();

    return { id_imparticion, id_modalidad, id_servicio, unidades };
}

function setCursosLoading($select, texto = 'Cargando cursos...') {
    $select.prop('disabled', true).html(`<option value="">${texto}</option>`);
}

function resetCursos($select, texto = 'SELECCIONA CURSO') {
    $select.prop('disabled', false).html(`<option value="">${texto}</option>`);
}

function fillCursos($select, cursos, selectedValue) {
    resetCursos($select);
    cursos.forEach(c => {
        const value = c.id_curso;
        const label = c.curso;
        const selected = selectedValue && String(selectedValue) === String(value) ? ' selected' : '';
        $select.append(`<option value="${value}"${selected}>${label}</option>`);
    });
}

// Cargar cursos dinámicamente a partir de los filtros
function cargarCursosDesdeFiltros() {
    const $cursoSelect = $('#curso').length ? $('#curso') : $('#id_curso');
    if (!$cursoSelect.length) return; // No hay select de cursos en la vista

    const { id_imparticion, id_modalidad, id_servicio, unidades } = getFiltroParams();

    // Validaciones mínimas
    if (!id_imparticion || !id_modalidad || !id_servicio || !Array.isArray(unidades) || unidades.length === 0) {
        resetCursos($cursoSelect, 'Seleccione filtros');
        return;
    }

    setCursosLoading($cursoSelect);

    // Realizar una petición por unidad y unir resultados sin duplicados
    const requests = unidades.map((id_unidad) => $.ajax({
        url: '/grupos/cursos/disponibles',
        method: 'POST',
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': (window.registroBladeVars && window.registroBladeVars.csrfToken) ? window.registroBladeVars.csrfToken : $('meta[name="csrf-token"]').attr('content') },
        data: { id_imparticion, id_modalidad, id_servicio, id_unidad }
    }));

    $.when.apply($, requests)
        .done(function () {
            // Normalizar arreglo de respuestas para 1 o muchas unidades
            const responses = (requests.length === 1)
                ? [arguments[0]] // [data, textStatus, jqXHR]
                : Array.from(arguments).map(a => a[0]);

            // Unir y deduplicar por id
            const mapa = new Map();
            responses.forEach(data => {
                const lista = Array.isArray(data) ? data : (data && data.data ? data.data : []);
                lista.forEach(c => {
                    const id = c.id ?? c.id_curso ?? c.value;
                    if (id !== undefined && !mapa.has(id)) mapa.set(id, c);
                });
            });

            const cursos = Array.from(mapa.values());
            if (cursos.length === 0) {
                resetCursos($cursoSelect, 'Sin cursos disponibles');
                return;
            }

            // Mantener selección previa si existía
            const previo = $cursoSelect.val();
            fillCursos($cursoSelect, cursos, previo);
            $cursoSelect.prop('disabled', false);
        })
        .fail(function (xhr) {
            console.error('Error al obtener cursos:', xhr?.responseText || xhr?.statusText || xhr);
            resetCursos($cursoSelect, 'Error al cargar');
        });
}

// Enlazar cambios en los filtros (IDs nuevos) y retrocompatibilidad (IDs antiguos)
$(document).on('change', '#id_tipo_curso, #id_modalidad_curso, #id_categoria_formacion, #unidades_necesitan', cargarCursosDesdeFiltros);
$(document).on('change', '#imparticion, #modalidad, #servicio, #unidad_accion_movil', cargarCursosDesdeFiltros);

// Auto-carga inicial si ya hay valores preseleccionados
$(document).ready(function () {
    try { cargarCursosDesdeFiltros(); } catch (e) { /* noop */ }
});

// ! Seccion Informacion general
$("#guardar_info_general").on('click', function (e) {
    e.preventDefault();
    if ($("#info_general_form").valid()) {
        const formData = new FormData();
        if ($('#id_grupo').val()) {
            formData.append('id_grupo', $('#id_grupo').val());
        }
        formData.append('seccion', 'info_general');
        // Leer desde IDs nuevos y mantener compatibilidad
        const id_imparticion = firstNonEmpty(['#id_tipo_curso', '#imparticion']);
        const id_modalidad = firstNonEmpty(['#id_modalidad_curso', '#modalidad']);
        const id_servicio = firstNonEmpty(['#id_categoria_formacion', '#servicio']);
        const unidadesSel = getSelectedUnitsArray();
        const id_unidad = unidadesSel.length ? unidadesSel[0] : '';
        const id_curso = firstNonEmpty(['#curso', '#id_curso']);

        formData.append('id_tipo_curso', id_imparticion ?? '');
        formData.append('id_modalidad_curso', id_modalidad ?? '');
        formData.append('id_unidad', id_unidad ?? '');
        formData.append('id_categoria_formacion', id_servicio ?? '');
        formData.append('id_curso', id_curso ?? '');
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
    const id_imparticion_opt = firstNonEmpty(['#id_tipo_curso', '#imparticion']);
    formData.append('id_imparticion', id_imparticion_opt);
    if (String(id_imparticion_opt) == '2') {
            formData.append('medio_virtual', $('#medio_virtual').val());
            formData.append('enlace_virtual', $('#enlace_virtual').val());
        }
        guardarSeccion(formData);
    }
});

// ! Agenda 
$("#guardar_agenda").on('click', function (e) {
    const formData = new FormData();
    if ($('#id_grupo').val()) {
        formData.append('id_grupo', $('#id_grupo').val());
    }
    formData.append('seccion', 'agenda');
    formData.append('_token', registroBladeVars.csrfToken);
    guardarSeccion(formData);
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

// ! Filtro dinámico de municipios por unidad (cuando cambia Unidad/Acción móvil)
$('#unidad_accion_movil').on('change', function () {
    const unidadNombre = $(this).find('option:selected').text().trim();
    const municipioSelect = $('#municipio-select');
    const localidadSelect = $('#localidad-select');

    // Resetear localidad
    localidadSelect.html('<option value="">SELECCIONAR MUNICIPIO PRIMERO</option>').prop('disabled', true);

    if (!unidadNombre) {
        municipioSelect.html('<option value="">SELECCIONAR</option>');
        return;
    }

    municipioSelect.prop('disabled', true).html('<option value="">Cargando municipios...</option>');
    $.ajax({
        url: '/grupos/municipios',
        method: 'GET',
        data: { unidad: unidadNombre },
        dataType: 'json',
        success: function (data) {
            municipioSelect.html('<option value="">SELECCIONAR</option>');
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function (mun) {
                    municipioSelect.append('<option value="' + mun.id + '">' + (mun.muni || mun.nombre || mun.descripcion || ('Municipio #' + mun.id)) + '</option>');
                });
            } else {
                municipioSelect.html('<option value="">Sin municipios</option>');
            }
            municipioSelect.prop('disabled', false);
        },
        error: function (xhr) {
            console.error('Error obteniendo municipios:', xhr.responseText || xhr.statusText);
            municipioSelect.html('<option value="">Error al cargar</option>').prop('disabled', false);
        }
    });
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
obtenerImparticion($('#imparticion').val());

$('#imparticion').on('change', function () {
    obtenerImparticion(this.value);
});

function obtenerImparticion(imparticion) {
    const medio_virtual = $('#medio_virtual');
    const enlace_virtual = $('#enlace_virtual');

    if (imparticion == 2) {
        console.log('Impartición a distancia seleccionada');
        medio_virtual.prop('disabled', false);
        enlace_virtual.prop('disabled', false);
    } else {
        console.log('Impartición a presencial seleccionada');
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
            if (response.mensaje) {
                const notyf = new Notyf({
                    position: { x: 'right', y: 'top' },
                    duration: 3000,
                });
                notyf.open(
                    {
                        type: 'error', className: 'notyf-error',
                        message: response.mensaje
                    }
                );
            }
        },
        error: function (error) {
            console.error('Error al guardar la sección:', error);
        }
    });
}