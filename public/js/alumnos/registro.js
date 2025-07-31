function inicializarNavegacionSecciones() {
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

    const navItems = document.querySelectorAll('.step-progress-nav .list-group-item');
    const sectionEls = secciones.map(id => document.getElementById(id));

    function mostrarSeccionPorIndice(idx) {
        sectionEls.forEach((seccion, i) => {
            if (seccion) {
                seccion.style.display = (i === idx) ? '' : 'none';
            }
        });
        // Actualizar clases de navegación
        navItems.forEach((item, i) => {
            const seccionId = secciones[i];
            item.classList.remove('active');
            const circle = item.querySelector('.step-circle');
            if (circle) {
                circle.setAttribute('data-status', 'restante');
            }
            if (estadosCaptura[seccionId] && estadosCaptura[seccionId].estado === true) {
                if (circle) circle.setAttribute('data-status', 'terminado');
            }
            if (i === idx) {
                item.classList.add('active');
                if (circle) circle.setAttribute('data-status', 'actual');
            }
        });
    }

    // Guardar el índice de la sección actual (la primera no terminada)
    let idxActual = secciones.findIndex(id => estadosCaptura[id] && estadosCaptura[id].estado === false);
    if (idxActual === -1) idxActual = 0;

    // Mostrar la sección actual al cargar
    mostrarSeccionPorIndice(idxActual);

    // Permitir navegación solo a secciones terminadas o la actual
    navItems.forEach((item, i) => {
        item.addEventListener('click', function () {
            const seccionId = secciones[i];
            // Solo permitir si es terminada o la actual
            if ((estadosCaptura[seccionId] && estadosCaptura[seccionId].estado === true) || i === idxActual) {
                mostrarSeccionPorIndice(i);
            }
        });
    });
}

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

// ! Validaciones - DATOS PERSONALES
const datos_personales = $('#validar-datos-personales'); // * Btn para validar datos personales
datos_personales.on('click', function () {
    if ($('#form-alumno').valid()) {
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
        formData.append('id_estado_civil', $('#estado_civil').val());
        formData.append('id_funcionario_realizo', id_usuario_captura);
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

// ! Ajax para guardar los datos del formulario (ahora soporta archivos)
const guardarSeccion = (formData) => {
    $.ajax({
        url: '/alumnos/guardar/seccion/alumno',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
            if(response.success) {
                const notyf = new Notyf({
                    position: { x: 'right', y: 'top' },
                    dismissible: true,
                    duration: 0
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
var chk = document.getElementById('empleado_aspirante');
var datos = document.getElementById('datos-empleo');
if (chk && datos) {
    function toggleDatosEmpleo() {
        datos.classList.toggle('d-none', !chk.checked);
    }
    chk.addEventListener('change', toggleDatosEmpleo);
    toggleDatosEmpleo();
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
