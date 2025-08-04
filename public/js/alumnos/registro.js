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
        formData.append('entidad_de_nacimiento', $('#entidad_de_nacimiento').val());
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
    console.log($('#domicilio').val());
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
        formData.append('seccion', 'grupos_vulnerables');
        // Obtener todos los grupos seleccionados
        const gruposSeleccionados = [];
        $('input[name="grupos_vulnerables[]"]:checked').each(function () {
            gruposSeleccionados.push($(this).val());
        });
        formData.append('grupos_vulnerables', JSON.stringify(gruposSeleccionados));
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

// ! Validaciones - EMPLEADO
const validar_empleado = $('#validar-empleo'); // * Btn para validar empleado
validar_empleado.on('click', function () {
    console.log('Validando sección empleado');
    const id_usuario_captura = $('#id_usuario_captura').val();
    const checkEmpleado = $('#empleado_aspirante').is(':checked');
    if (checkEmpleado) {
        if ($('#form-empleado').valid()) {
            const formData = new FormData();
            formData.append('curp', $('#curp').val());
            formData.append('seccion', 'empleado');
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
        formData.append('seccion', 'empleado');
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

const secciones = [
    'datos-personales',
    'domicilio',
    'contacto',
    'grupos-vulnerables',
    'capacitacion',
    'empleado',
    'cerss'
];

// Simulación de obtener los datos de captura
const estadosCaptura = {
    'datos-personales': { estado: true },
    'domicilio': { estado: true },
    'contacto': { estado: true },
    'grupos-vulnerables': { estado: true },
    'capacitacion': { estado: true },
    'empleado': { estado: true },
    'cerss': { estado: true }
};

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

function guardarDatosCurpEnCampos(data) {
    $('#nombre_s').val(data.nombre_s_);
    $('#primer_apellido').val(data.primer_apellido);
    $('#segundo_apellido').val(data.segundo_apellido);
    $('#entidad_de_nacimiento').val(data.entidad_de_nacimiento);
    $('#fecha_de_nacimiento').val(data.fecha_de_nacimiento);

    // * Sección de selects
    $('#sexo_select').val(data.sexo === 'MUJER' ? '2' : data.sexo === 'HOMBRE' ? '1' : '');
    $('#sexo_input').val(data.sexo === 'MUJER' ? 'FEMENINO' : data.sexo === 'HOMBRE' ? 'MASCULINO' : '');

    $('#nacionalidad_select').val(data.nacionalidad === 'MEXICO' ? '1' : '');
    $('#nacionalidad_input').val(data.nacionalidad === 'MEXICO' ? 'MEXICANA' : '');
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
    $.ajax({
        url: '/alumnos/guardar/seccion/alumno',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
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
            }
        },
        error: function (error) {
            console.error('Error al guardar la sección:', error);
        }
    });
}

// ! Despliega la captura de datos en la seccion de EMPLEADO
var chkEmpleado = document.getElementById('empleado_aspirante');
var datos = document.getElementById('datos-empleo');
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

// Iniciar con 'sin_grupo_vulnerable' checked
const sinGrupo = document.getElementById('grupo_vulnerable_sin_grupo');
if (sinGrupo) sinGrupo.checked = true;

grupos_vulnerables.forEach(grupo => {
    grupo.addEventListener('change', function () {
        if (this.value === 'sin_grupo_vulnerable') {
            grupos_vulnerables.forEach(g => {
                if (g !== this) g.checked = false;
            });
        } else {
            if (sinGrupo) sinGrupo.checked = false;
        }
    });
});

const seccionesPasos = document.querySelectorAll('.step-progress-nav li');
seccionesPasos.forEach(paso => {
    paso.addEventListener('click', function () {
        mostrarSeccion(paso);
    });
});

function moverSiguienteSeccion() {

}

function mostrarSeccion(paso) {
    const seccion = paso.getAttribute('data-step');
    const secciones = document.querySelectorAll('.step-section');
    secciones.forEach(sec => {
        sec.classList.add('d-none');
    });

    const seccionMostrar = document.getElementById(seccion);
    if (seccionMostrar) {
        seccionMostrar.classList.remove('d-none');
    }

    cambiarPaso(paso);
}

function cambiarPaso(paso) {
    const todosPasos = document.querySelectorAll('.step-progress-nav li');
    todosPasos.forEach(p => {
        p.classList.remove('active');
        const circulo = p.querySelector('.step-circle');
        circulo.setAttribute('data-status', 'completado');
    });
    const circulo = paso.querySelector('.step-circle');
    paso.classList.add('active');
    circulo.setAttribute('data-status', 'actual');
    console.log('cambiarPaso', circulo);
}