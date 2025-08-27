// ! Validaciones - DATOS PERSONALES
const datos_personales = $('#validar-datos-personales'); // * Btn para validar datos personales
datos_personales.on('click', function () {
    if ($('#form-datos-personales').valid()) {
        const id_usuario_captura = $('#id_usuario_captura').val();
        const formData = new FormData();
        formData.append('seccion', 'datos_personales');
        formData.append('nombre', $('#nombre_s').val());
        formData.append('apellido_paterno', $('#primer_apellido').val());
        formData.append('curp', $('#curp').val());
        formData.append('apellido_materno', $('#segundo_apellido').val());
        formData.append('fecha_de_nacimiento', $('#fecha_de_nacimiento').val());
        formData.append('id_entidad_nacimiento', $('#entidad_de_nacimiento_select').val());
        formData.append('id_sexo', $('#sexo_select').val());
        formData.append('id_nacionalidad', $('#nacionalidad_select').val());
        formData.append('id_estado_civil', $('#estado_civil_select').val());
        formData.append('id_usuario_realizo', id_usuario_captura);
        // Adjuntar archivo si existe
        const fileInput = $('#documento_curp')[0];
        if (fileInput && fileInput.files.length > 0) {
            formData.append('documento_curp', fileInput.files[0]);
        }
        formData.append('fecha_documento_curp', $('#fecha_documento_curp').val());

        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Validaciones - DOMICILIO
const validar_domicilio = $('#validar-domicilio'); // * Btn para validar domicilio
validar_domicilio.on('click', function () {
    if ($('#form-domicilio').valid()) {
        const id_usuario_captura = $('#id_usuario_captura').val();
        const formData = new FormData();
        formData.append('curp', $('#curp').val());
        formData.append('seccion', 'domicilio');
        formData.append('id_pais', $('#pais_select').val());
        formData.append('id_estado', $('#estado_select').val());
        formData.append('id_municipio', $('#municipio_select').val());
        formData.append('domicilio', $('#domicilio').val());
        formData.append('clave_localidad', $('#localidad').val());
        formData.append('cp', $('#codigo_postal').val());
        formData.append('colonia', $('#colonia').val());
        formData.append('id_usuario_realizo', id_usuario_captura);
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Validaciones - CONTACTO
const validar_contacto = $('#validar-contacto'); // * Btn para validar contacto
validar_contacto.on('click', function () {
    if ($('#form-contacto').valid()) {
        const id_usuario_captura = $('#id_usuario_captura').val();
        const formData = new FormData();
        formData.append('curp', $('#curp').val());
        formData.append('seccion', 'contacto');
        formData.append('telefono_casa', $('#telefono_casa').val());
        formData.append('telefono_celular', $('#telefono_celular').val());
        formData.append('correo_electronico', $('#correo_electronico').val());
        formData.append('facebook', $('#facebook').val());
        formData.append('autoriza_bolsa_trabajo', $('#autoriza_bolsa_trabajo').is(':checked') ? 1 : 0);
        formData.append('id_usuario_realizo', id_usuario_captura);
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Validaciones - GRUPOS VULNERABLES
const validar_grupos_vulnerables = $('#validar-grupos-vulnerables'); // * Btn para validar grupos vulnerables
validar_grupos_vulnerables.on('click', function () {
    if ($('#form-grupos-vulnerables').valid()) {
        const id_usuario_captura = $('#id_usuario_captura').val();
        const formData = new FormData();
        formData.append('curp', $('#curp').val());
        formData.append('seccion', 'grupos_vulnerables');

        // Verificar si se marcó el checkbox general de pertenece a grupo vulnerable
        const perteneceGrupoVulnerable = $('#pertenece_a_grupo_vulnerable').is(':checked');
        if (perteneceGrupoVulnerable) {
            formData.append('pertenece_a_grupo_vulnerable', '1');
        }

        // Obtener los grupos vulnerables específicos seleccionados
        const gruposSeleccionados = [];
        $('input[name="grupos_vulnerables[]"]:checked').each(function () {
            gruposSeleccionados.push($(this).val());
        });

        // Solo enviar grupos vulnerables si hay alguno seleccionado
        if (gruposSeleccionados.length > 0) {
            gruposSeleccionados.forEach(function (grupo, index) {
                formData.append(`grupos_vulnerables[${index}]`, grupo);
            });
        }

        formData.append('id_usuario_realizo', id_usuario_captura);
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Validaciones - CAPACITACION
const validar_capacitacion = $('#validar-capacitacion'); // * Btn para validar capacitación
validar_capacitacion.on('click', function () {
    if ($('#form-capacitacion').valid()) {
        const id_usuario_captura = $('#id_usuario_captura').val();
        const formData = new FormData();

        formData.append('seccion', 'capacitacion');
        formData.append('ultimo_grado_estudios', $('#ultimo_grado_estudios').val());
        const fileInput = $('#documento_ultimo_grado')[0];
        if (fileInput && fileInput.files.length > 0) {
            formData.append('documento_ultimo_grado', fileInput.files[0]);
        }
        formData.append('fecha_documento_ultimo_grado', $('#fecha_documento_ultimo_grado').val());
        formData.append('medio_enterado_sistema', $('#medio_enterado_sistema').val());
        formData.append('motivo_eleccion_capacitacion', $('#motivo_eleccion_capacitacion').val());
        formData.append('medio_confirmacion', $('#medio_confirmacion').val());
        formData.append('id_usuario_realizo', id_usuario_captura);
        formData.append('curp', $('#curp').val());
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Validaciones - LABORAL
const validar_laboral = $('#validar-empleo'); // * Btn para validar laboral
validar_laboral.on('click', function () {
    const id_usuario_captura = $('#id_usuario_captura').val();
    const checkEmpleado = $('#empleado_aspirante').is(':checked');
    if (checkEmpleado) {
        if ($('#form-laboral').valid()) {
            const formData = new FormData();
            formData.append('curp', $('#curp').val());
            formData.append('seccion', 'laboral');
            formData.append('empleado_aspirante', 1);
            formData.append('nombre_empresa', $('#empresa_trabaja').val());
            formData.append('puesto_trabajo', $('#puesto_trabajo').val());
            formData.append('antiguedad', $('#antiguedad_trabajo').val());
            formData.append('direccion_trabajo', $('#direccion_trabajo').val());
            formData.append('id_usuario_realizo', id_usuario_captura);
            formData.append('_token', registroBladeVars.csrfToken);
            guardarSeccion(formData);
        }
    } else {
        // Solo enviar el check en false
        const formData = new FormData();
        formData.append('curp', $('#curp').val());
        formData.append('seccion', 'laboral');
        formData.append('empleado_aspirante', 0);
        formData.append('id_usuario_realizo', id_usuario_captura);
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

// ! Validaciones - CERSS
const validar_cerss = $('#validar-cerss'); // * Btn para validar CERSS
validar_cerss.on('click', function () {
    const id_usuario_captura = $('#id_usuario_captura').val();
    const checkCerss = $('#aspirante_cerss').is(':checked');
    if (checkCerss) {
        if ($('#form-cerss').valid()) {
            const formData = new FormData();
            formData.append('curp', $('#curp').val());
            formData.append('seccion', 'cerss');
            formData.append('aspirante_cerss', 1);
            formData.append('numero_expediente', $('#numero_expediente').val());
            formData.append('id_usuario_realizo', id_usuario_captura);
            formData.append('_token', registroBladeVars.csrfToken);

            // Adjuntar archivo si existe
            const fileInput = $('#documento_ficha_cerss')[0];
            if (fileInput && fileInput.files.length > 0) {
                formData.append('documento_ficha_cerss', fileInput.files[0]);
            }
            guardarSeccion(formData);
        }
    } else {
        // Solo enviar el check en false
        const formData = new FormData();
        formData.append('curp', $('#curp').val());
        formData.append('seccion', 'cerss');
        formData.append('aspirante_cerss', 0);
        formData.append('id_usuario_realizo', id_usuario_captura);
        formData.append('_token', registroBladeVars.csrfToken);
        guardarSeccion(formData);
    }
});

const curp = $('#curp').val();
const esNuevoRegistro = $('#esNuevoRegistro').val() === 'true' ? true : false;
if (curp && esNuevoRegistro) {
    const datosGuardados = localStorage.getItem('curp_datos_' + curp);
    const curpActual = localStorage.getItem('curp_actual');
    if (datosGuardados && curpActual === curp) {
        try {
            const data = JSON.parse(datosGuardados);
            guardarDatosCurpEnCampos(data);
            deshabilitarCampos();
            $('#spinner-curp').addClass('d-none');
        } catch (e) {
            obtenerDatosCurp(curp);
        }
    } else {
        obtenerDatosCurp(curp);
    }
} else if (!esNuevoRegistro) {
    deshabilitarCampos();
}

// =====================
// Domicilio: Selects dinámicos País -> Estado -> Municipio
// =====================
const $pais = $('#pais_select');
const $estado = $('#estado_select');
const $municipio = $('#municipio_select');

// Heurística ligera: nombre que identifica México en tu catálogo
function esPaisMexicoNombre(nombre) {
    if (!nombre) return false;
    const n = nombre.toString().trim().toUpperCase();
    return n === 'MEXICO' || n === 'MÉXICO' || n === 'MEXICO (MX)' || n === 'MEX' || n.includes('MEXIC');
}

function actualizarValidacionDomicilioPorPais() {
    const paisTexto = $pais.find('option:selected').text();
    const esMexico = esPaisMexicoNombre(paisTexto);

    // Habilitar/Deshabilitar selects
    $estado.prop('disabled', !esMexico);
    $municipio.prop('disabled', !esMexico);

    // Limpiar opciones si no es México
    if (!esMexico) {
        $estado.val('');
        $municipio.val('');
    } else {
        // Si es México y hay país seleccionado, intentar cargar estados si aún no hay
        if ($pais.val() && $estado.find('option').length <= 1) {
            cargarEstados($pais.val());
        }
    }

    // Ajustar reglas de validación dinamicamente
    try {
        const va = $('#form-domicilio').validate();
        va.settings.rules.estado_select = { required: esMexico };
        va.settings.rules.municipio_select = { required: esMexico };
        // Forzar revalidación de campos afectados
        $estado.valid && $estado.valid();
        $municipio.valid && $municipio.valid();
    } catch (e) { /* si aún no está cargado el validador, se ignorará */ }
}

function cargarEstados(paisId, preselect = null) {
    if (!paisId) return;
    $estado.html('<option value="">Cargando estados...</option>');
    $municipio.html('<option value="">-- Selecciona un municipio --</option>');
    $.ajax({
        url: registroBladeVars.routeEstadosPorPais,
        type: 'POST',
        data: { pais_id: paisId, _token: registroBladeVars.csrfToken },
        success: function (res) {
            $estado.empty().append('<option value="">-- Selecciona un estado --</option>');
            if (Array.isArray(res)) {
                res.forEach(function (e) {
                    // Soportar diferentes nombres de propiedades
                    const id = (e && (e.id ?? e.id_estado ?? e.cve_estado ?? e.clave ?? e.codigo));
                    const texto = (e && (e.nombre ?? e.estado ?? e.entidad ?? e.nombre_estado ?? ''));
                    if (id != null) {
                        $estado.append('<option value="' + id + '">' + texto + '</option>');
                    }
                });
            }
        const selectedEstado = preselect || $estado.attr('data-selected-estado') || $estado.data('selectedEstado');
            if (selectedEstado) {
                $estado.val(String(selectedEstado));
                if ($estado.val()) {
            const muniSel = $municipio.attr('data-selected-municipio') || $municipio.data('selectedMunicipio') || null;
            cargarMunicipios($estado.val(), muniSel);
                }
            }
        },
        error: function () {
            $estado.html('<option value="">No se pudieron cargar estados</option>');
        }
    });
}

function cargarMunicipios(estadoId, preselect = null) {
    if (!estadoId) return;
    $municipio.html('<option value="">Cargando municipios...</option>');
    $.ajax({
        url: registroBladeVars.routeMunicipiosPorEstado,
        type: 'POST',
        data: { estado_id: estadoId, _token: registroBladeVars.csrfToken },
        success: function (res) {
            $municipio.empty().append('<option value="">-- Selecciona un municipio --</option>');
            if (Array.isArray(res)) {
                res.forEach(function (m) {
                    const id = m.id;
                    const texto = m.muni;
                    if (id != null) {
                        $municipio.append('<option value="' + id + '">' + texto + '</option>');
                    }
                });
            }
            const selectedMunicipio = preselect || $municipio.attr('data-selected-municipio') || $municipio.data('selectedMunicipio');
            if (selectedMunicipio) {
                $municipio.val(String(selectedMunicipio));
            }
        },
        error: function () {
            $municipio.html('<option value="">No se pudieron cargar municipios</option>');
        }
    });
}

// Eventos de cambio
$pais.on('change', function () {
    actualizarValidacionDomicilioPorPais();
    const paisId = $(this).val();
    const paisTexto = $(this).find('option:selected').text();
    if (paisId && esPaisMexicoNombre(paisTexto)) {
        cargarEstados(paisId);
    } else {
        $estado.empty().append('<option value="">-- Selecciona un estado --</option>');
        $municipio.empty().append('<option value="">-- Selecciona un municipio --</option>');
    }
});

$estado.on('change', function () {
    const estadoId = $(this).val();
    if (estadoId) {
        cargarMunicipios(estadoId);
    } else {
        $municipio.empty().append('<option value="">-- Selecciona un municipio --</option>');
    }
});

// Inicialización al cargar la página (edición o creación)
$(function () {
    // Establecer validación inicial según país actual
    actualizarValidacionDomicilioPorPais();

    // Si ya hay un país seleccionado y es México, y faltan opciones de estado, cargarlas
    const paisIdInit = $pais.val();
    const paisTextoInit = $pais.find('option:selected').text();
    const esMexicoInit = esPaisMexicoNombre(paisTextoInit);
    if (paisIdInit && esMexicoInit) {
        // Si ya tenemos estados renderizados (edición), no recargar; pero sí cargar municipios si ya hay estado
        const estadoSel = $estado.attr('data-selected-estado') || $estado.data('selectedEstado') || $estado.val();
        if ($estado.find('option').length <= 1) {
            cargarEstados(paisIdInit, estadoSel || null);
        } else if (estadoSel) {
            const muniSelInit = $municipio.attr('data-selected-municipio') || $municipio.data('selectedMunicipio') || null;
            cargarMunicipios(estadoSel, muniSelInit);
        }
    }
});

// Utilidad: normaliza texto (quita acentos, mayúsculas, espacios extra)
function normalizaTexto(str) {
    return (str || '')
        .toString()
        .trim()
        .toUpperCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

// Utilidad: selecciona una opción de un <select> comparando por texto visible
function seleccionarOpcionPorTexto(selectSelector, textoBuscado) {
    const select = $(selectSelector);
    if (!select || select.length === 0) return;

    const buscado = normalizaTexto(textoBuscado);
    if (!buscado) return false;

    let valorEncontrado = null;
    select.find('option').each(function () {
        const $opt = $(this);
        const txt = normalizaTexto($opt.text());
        // También considerar atributos alternos si existen
        const dataNombre = normalizaTexto($opt.attr('data-nombre'));
        const dataSigla = normalizaTexto($opt.attr('data-sigla'));
        if (txt === buscado || (dataNombre && dataNombre === buscado) || (dataSigla && dataSigla === buscado)) {
            valorEncontrado = $opt.val();
            return false; // break
        }
    });

    if (valorEncontrado !== null) {
        select.val(valorEncontrado).trigger('change');
        return true;
    }
    // Intento de coincidencia parcial como respaldo (por si hay pequeñas diferencias)
    select.find('option').each(function () {
        if (valorEncontrado !== null) return;
        const $opt = $(this);
        const txt = normalizaTexto($opt.text());
        if (txt.includes(buscado) || buscado.includes(txt)) {
            valorEncontrado = $opt.val();
        }
    });
    if (valorEncontrado !== null) {
        select.val(valorEncontrado).trigger('change');
        return true;
    }
    console.warn('No se encontró coincidencia para', textoBuscado, 'en', selectSelector);
    return false;
}

function guardarDatosCurpEnCampos(data) {
    $('#nombre_s').val(data.nombre_s_);
    $('#primer_apellido').val(data.primer_apellido);
    $('#segundo_apellido').val(data.segundo_apellido);
    $('#fecha_de_nacimiento').val(data.fecha_de_nacimiento);

    // * Sección de selects
    $('#sexo_select').val(data.sexo === 'MUJER' ? '2' : data.sexo === 'HOMBRE' ? '1' : '');
    $('#sexo_input').val(data.sexo === 'MUJER' ? 'FEMENINO' : data.sexo === 'HOMBRE' ? 'MASCULINO' : '');

    $('#nacionalidad_select').val(data.nacionalidad === 'MEXICO' ? '1' : '');
    $('#nacionalidad_input').val(data.nacionalidad === 'MEXICO' ? 'MEXICANA' : '');

    // Nuevo: seleccionar entidad de nacimiento en el select por coincidencia de texto
    seleccionarOpcionPorTexto('#entidad_de_nacimiento_select', data.entidad_de_nacimiento);
    $('#entidad_de_nacimiento').val(data.entidad_de_nacimiento);
}

function deshabilitarCampos() {
    $('#nombre_s').prop('readonly', true);
    $('#primer_apellido').prop('readonly', true);
    $('#segundo_apellido').prop('readonly', true);
    $('#entidad_de_nacimiento').prop('readonly', true);
    $('#fecha_de_nacimiento').prop('readonly', true);

    // * Sección de selects
    $('#sexo_select').prop('disabled', true);
    $('#sexo_select').hide();
    $('#sexo_input').prop('readonly', true);

    $('#nacionalidad_select').prop('disabled', true);
    $('#nacionalidad_select').hide();
    $('#nacionalidad_input').prop('readonly', true);

    $('#entidad_de_nacimiento_select').hide();
    $('#entidad_de_nacimiento').prop('readonly', true);
}


// ! Obtener del Microservicio NodeJS
function obtenerDatosCurp(curp) {
    $.ajax({
        url: registroBladeVars.routeCurp.replace(':encodecurp', encodeURIComponent(btoa(curp))),
        method: 'POST',
        data: {
            curp: curp,
            _token: registroBladeVars.csrfToken
        },
        success: function (response) {
            if (response.success) {
                const notyf = new Notyf({
                    position: { x: 'right', y: 'top' },
                    dismissible: true,
                    duration: 0
                });

                if (response.data.error) {
                    notyf.open(
                        {
                            type: 'warning', className: 'notyf-warning',
                            message: 'Ha sucedido un error al obtener los datos de la CURP, puede proseguir con el registro manualmente.'
                        }
                    );
                    return;
                }
                guardarDatosCurpEnCampos(response.data);
                localStorage.setItem('curp_datos_' + curp, JSON.stringify(response.data));
                localStorage.setItem('curp_actual', curp);
                deshabilitarCampos();
                $('#spinner-curp').removeClass('d-none');
                notyf.open(
                    {
                        type: 'success', className: 'notyf-success',
                        message: 'Los datos de la CURP se han obtenido correctamente.'
                    }
                );
            } else {
                alert('No se encontraron datos para la CURP proporcionada.');
            }
        },
        error: function () {
            alert('Error al obtener datos de la CURP.');
        },
        complete: function () {
            $('#spinner-curp').addClass('d-none');
        }
    });
}


// ! Ajax para guardar los datos del formulario
const guardarSeccion = (formData) => {
    const seccionActual = formData.get('seccion');

    $.ajax({
        url: '/alumnos/guardar/seccion/alumno',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
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

                // Convertir nombre de sección del backend al frontend
                const mapeoSecciones = {
                    'datos_personales': 'datos-personales',
                    'domicilio': 'domicilio_section',
                    'contacto': 'contacto',
                    'grupos_vulnerables': 'grupos-vulnerables',
                    'capacitacion': 'capacitacion',
                    'laboral': 'laboral',
                    'cerss': 'cerss'
                };

                const seccionFrontend = mapeoSecciones[seccionActual] || seccionActual;

                if(seccionActual === 'cerss') {
                    window.location.reload();
                }
                // Mover a la siguiente sección
                moverSiguienteSeccion(seccionFrontend);
            }
        },
        error: function (error) {
            console.error('Error al guardar la sección:', error);
        }
    });
}

// ! Despliega la captura de datos en la seccion de EMPLEADO
var chkEmpleado = document.getElementById('empleado_aspirante');
var datos = document.getElementById('datos-laboral');
if (chkEmpleado && datos) {
    function toggleDatosEmpleo() {
        datos.classList.toggle('d-none', !chkEmpleado.checked);
    }
    chkEmpleado.addEventListener('change', toggleDatosEmpleo);
    toggleDatosEmpleo();
}

// ! Despliega la captura de datos en la sección de CERSS
var chkCerss = document.getElementById('aspirante_cerss');
var datosCerss = document.getElementById('datos-cerss');
if (chkCerss && datosCerss) {
    function toggleDatosCerss() {
        datosCerss.classList.toggle('d-none', !chkCerss.checked);
    }
    chkCerss.addEventListener('change', toggleDatosCerss);
    toggleDatosCerss();
}

const grupos_vulnerables = Array.from(document.getElementsByName('grupos_vulnerables[]'));
const perteneceGrupo = document.getElementById('pertenece_a_grupo_vulnerable');

// Si se selecciona perteneceGrupo, deselecciona todos los grupos vulnerables
if (perteneceGrupo) {
    perteneceGrupo.addEventListener('change', function () {
        if (this.checked) {
            grupos_vulnerables.forEach(g => g.checked = false);
        }
    });
}

grupos_vulnerables.forEach(grupo => {
    grupo.addEventListener('change', function () {
        if (this.checked) {
            if (perteneceGrupo) perteneceGrupo.checked = false;
        }
    });
});

const seccionesPasos = document.querySelectorAll('.step-progress-nav li');
seccionesPasos.forEach(paso => {
    paso.addEventListener('click', function () {
        mostrarSeccion(paso);
    });
});

function mostrarSeccion(paso) {
    // Verificar si el paso está habilitado
    if (paso.classList.contains('disabled')) {
        console.log('Este paso está bloqueado');
        return;
    }

    const seccion = paso.getAttribute('data-step');

    const secciones = document.querySelectorAll('.step-section');
    secciones.forEach(sec => {
        sec.classList.add('d-none');
    });

    let seccionMostrar = document.getElementById(seccion);

    // Manejar caso especial de domicilio
    if (seccion === 'domicilio_section') {
        seccionMostrar = document.getElementById('domicilio_section');
    } else if (seccion === 'domicilio' && !seccionMostrar) {
        seccionMostrar = document.getElementById('domicilio_section');
    }

    if (seccionMostrar) {
        seccionMostrar.classList.remove('d-none');
    } else {
        console.error('No se pudo encontrar el elemento con ID:', seccion);
    }

    cambiarPaso(paso);
}

function cambiarPaso(paso) {
    // Solo cambiar el estado activo, manteniendo los demás estados
    const todosPasos = document.querySelectorAll('.step-progress-nav li');
    todosPasos.forEach(p => {
        p.classList.remove('active');

        // Quitar data-status="actual" de TODOS los círculos
        const circulo = p.querySelector('.step-circle');
        if (circulo.getAttribute('data-status') === 'actual') {
            // Si era actual, determinar su nuevo estado basado en las clases del paso
            if (p.classList.contains('completed')) {
                circulo.setAttribute('data-status', 'terminado');
            } else if (p.classList.contains('disabled')) {
                circulo.setAttribute('data-status', 'pendiente');
            } else {
                // Fallback para otros casos
                circulo.setAttribute('data-status', 'restante');
            }
        }
    });

    // Marcar el paso actual como activo solo si no está disabled
    if (!paso.classList.contains('disabled')) {
        paso.classList.add('active');
        const circulo = paso.querySelector('.step-circle');
        // SIEMPRE establecer como actual el paso seleccionado
        circulo.setAttribute('data-status', 'actual');
    }
}

const secciones = [
    'datos-personales',
    'domicilio_section',
    'contacto',
    'grupos-vulnerables',
    'capacitacion',
    'laboral',
    'cerss'
];

function generarEstadosCaptura(seccionesFinalizadas) {
    const estadosCaptura = {};

    // Si no hay secciones finalizadas, todas están en false
    if (!seccionesFinalizadas || Object.keys(seccionesFinalizadas).length === 0) {
        secciones.forEach(seccion => {
            estadosCaptura[seccion] = { estado: false };
        });
        return estadosCaptura;
    }

    // Mapeo de nombres de backend a frontend
    const mapeoSecciones = {
        'datos_personales': 'datos-personales',
        'domicilio': 'domicilio_section',
        'contacto': 'contacto',
        'grupos_vulnerables': 'grupos-vulnerables',
        'capacitacion': 'capacitacion',
        'laboral': 'laboral',
        'cerss': 'cerss'
    };

    // Busca la última sección marcada como finalizada
    let ultimaFinalizadaIndex = -1;
    for (let i = 0; i < secciones.length; i++) {
        const seccionFrontend = secciones[i];

        // Buscar la sección correspondiente en el backend
        const seccionBackend = Object.keys(mapeoSecciones).find(
            key => mapeoSecciones[key] === seccionFrontend
        );

        if (
            seccionBackend &&
            seccionesFinalizadas[seccionBackend] &&
            seccionesFinalizadas[seccionBackend].finalizada === true
        ) {
            ultimaFinalizadaIndex = i;
        }
    }

    // Marca como true hasta la última finalizada (inclusive), false las demás
    secciones.forEach((seccion, index) => {
        estadosCaptura[seccion] = {
            estado: index <= ultimaFinalizadaIndex
        };
    });

    return estadosCaptura;
}

// Parsear el JSON string a objeto JavaScript
let seccionesFinalizadas = {};
try {
    if (typeof registroBladeVars.ultimaSeccionGuardada === 'string') {
        seccionesFinalizadas = JSON.parse(registroBladeVars.ultimaSeccionGuardada);
    } else {
        seccionesFinalizadas = registroBladeVars.ultimaSeccionGuardada || {};
    }
} catch (e) {
    console.error('Error al parsear ultimaSeccionGuardada:', e);
    seccionesFinalizadas = {};
}

const estadosCaptura = generarEstadosCaptura(seccionesFinalizadas);

// Variable global para mantener el estado actualizado
window.estadosCaptura = estadosCaptura;

// Aplicar estados a los pasos del formulario PRIMERO
aplicarEstadosPasos(estadosCaptura);

// DESPUÉS mostrar la sección actual
mostrarSeccionActual(estadosCaptura);

// Función para aplicar estados a los pasos de navegación
function aplicarEstadosPasos(estadosCaptura) {
    const pasos = document.querySelectorAll('.step-progress-nav li');

    // Encontrar la siguiente sección disponible (primera con estado false)
    let siguienteSeccionIndex = -1;
    for (let i = 0; i < secciones.length; i++) {
        if (!estadosCaptura[secciones[i]]?.estado) {
            siguienteSeccionIndex = i;
            break;
        }
    }

    // PRIMERO: Limpiar todos los estados
    pasos.forEach((paso) => {
        paso.classList.remove('completed', 'current', 'disabled', 'active');
        const circulo = paso.querySelector('.step-circle');
        circulo && circulo.removeAttribute('data-status');
    });

    // SEGUNDO: Aplicar los nuevos estados mapeando por data-step
    pasos.forEach((paso) => {
        const dataStep = paso.getAttribute('data-step');
        if (!dataStep) return;
        const idx = secciones.indexOf(dataStep);
        const circulo = paso.querySelector('.step-circle');
        const isEnabled = estadosCaptura[dataStep]?.estado || false;

        if (isEnabled) {
            // Esta sección está completada/terminada
            paso.classList.add('completed');
            circulo && circulo.setAttribute('data-status', 'terminado');

            // Habilitar click para poder regresar
            paso.style.pointerEvents = 'auto';
            paso.style.opacity = '1';
        } else if (idx === siguienteSeccionIndex) {
            // Esta es la siguiente sección disponible (actual)
            paso.classList.add('current', 'active');
            circulo && circulo.setAttribute('data-status', 'actual');

            // Habilitar click
            paso.style.pointerEvents = 'auto';
            paso.style.opacity = '1';
        } else {
            // Paso bloqueado/pendiente
            paso.classList.add('disabled');
            circulo && circulo.setAttribute('data-status', 'pendiente');

            // Deshabilitar click
            paso.style.pointerEvents = 'none';
            paso.style.opacity = '0.5';
        }
    });

    // Si todas las secciones están completadas, marcar la última como actual en ambas barras
    const todasCompletadas = secciones.every(seccion => estadosCaptura[seccion]?.estado);
    if (todasCompletadas) {
        const ultimaSeccion = secciones[secciones.length - 1];
        pasos.forEach((paso) => {
            const dataStep = paso.getAttribute('data-step');
            if (dataStep === ultimaSeccion) {
                const circulo = paso.querySelector('.step-circle');
                paso.classList.remove('completed');
                paso.classList.add('current', 'active');
                circulo && circulo.setAttribute('data-status', 'actual');
            }
        });
    }
}

function mostrarSeccionActual(estadosCaptura) {
    // Ocultar todas las secciones
    const todasLasSecciones = document.querySelectorAll('.step-section');
    todasLasSecciones.forEach(sec => {
        sec.classList.add('d-none');
    });

    // Encontrar el paso que está marcado como activo (actual)
    const pasoActivo = document.querySelector('.step-progress-nav li.active');
    let seccionAMostrar = secciones[0]; // Por defecto la primera

    if (pasoActivo) {
        // Si hay un paso activo, usar su data-step
        const dataStep = pasoActivo.getAttribute('data-step');
        seccionAMostrar = dataStep;
    } else {
        // Fallback: encontrar la primera sección no completada
        for (let i = 0; i < secciones.length; i++) {
            const seccion = secciones[i];
            if (!estadosCaptura[seccion]?.estado) {
                seccionAMostrar = seccion;
                break;
            }
            // Si llegamos al final y todas están completadas, mostrar la última
            if (i === secciones.length - 1) {
                seccionAMostrar = seccion;
            }
        }
    }

    // Mostrar la sección correspondiente
    let seccionElemento = document.getElementById(seccionAMostrar);

    // Manejar caso especial de domicilio
    if (seccionAMostrar === 'domicilio_section') {
        seccionElemento = document.getElementById('domicilio_section');
    } else if (seccionAMostrar === 'domicilio' && !seccionElemento) {
        seccionElemento = document.getElementById('domicilio_section');
    }


    if (seccionElemento) {
        seccionElemento.classList.remove('d-none');
    } else {
        todasLasSecciones.forEach(sec => {
            console.log('- ID:', sec.id, 'Classes:', sec.className);
        });
    }
}

// Función para mover a la siguiente sección después de guardar
function moverSiguienteSeccion(seccionActual) {
    const indiceActual = secciones.indexOf(seccionActual);

    if (indiceActual < secciones.length - 1) {
        const siguienteSeccion = secciones[indiceActual + 1];

        // Actualizar estados globales
        // Marcar la sección actual como completada
        window.estadosCaptura[seccionActual] = { estado: true };

        // Aplicar nuevos estados (esto calculará automáticamente cuál debe ser "actual")
        aplicarEstadosPasos(window.estadosCaptura);

        // Mostrar siguiente sección
        const todasLasSecciones = document.querySelectorAll('.step-section');
        todasLasSecciones.forEach(sec => sec.classList.add('d-none'));

        let siguienteElemento = document.getElementById(siguienteSeccion);

        // Manejar caso especial de domicilio
        if (siguienteSeccion === 'domicilio' && !siguienteElemento) {
            siguienteElemento = document.getElementById('domicilio_section');
        }

        if (siguienteElemento) {
            siguienteElemento.classList.remove('d-none');
        } else {
            console.error('No se encontró el elemento para la siguiente sección:', siguienteSeccion);
        }
    } else {
        // Si es la última sección, marcarla como completada también
        window.estadosCaptura[seccionActual] = { estado: true };
        aplicarEstadosPasos(window.estadosCaptura);

        const notyf = new Notyf({
            position: { x: 'right', y: 'top' },
            duration: 5000,
        });
        notyf.open({
            type: 'success',
            className: 'notyf-success',
            message: '¡Registro completado exitosamente!'
        });
    }
}
