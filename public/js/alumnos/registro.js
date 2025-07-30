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

if (curp) {
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
}

function guardarDatosCurpEnCampos(data) {
    $('#nombre_s').val(data.nombre_s_);
    $('#primer_apellido').val(data.primer_apellido);
    $('#segundo_apellido').val(data.segundo_apellido);
    $('#entidad_de_nacimiento').val(data.entidad_de_nacimiento);
    $('#fecha_de_nacimiento').val(data.fecha_de_nacimiento);
    $('#sexo').val(data.sexo);
    $('#nacionalidad').val(data.nacionalidad);
}

function deshabilitarCampos() {
    $('#nombre_s').prop('readonly', true);
    $('#primer_apellido').prop('readonly', true);
    $('#segundo_apellido').prop('readonly', true);
    $('#entidad_de_nacimiento').prop('readonly', true);
    $('#fecha_de_nacimiento').prop('readonly', true);
    $('#sexo').prop('readonly', true);
    $('#nacionalidad').prop('readonly', true);
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
        const datos_personales = {
            finalizado: true,
            datos: {
                curp: $('#curp').val(),
                nombre_s: $('#nombre_s').val(),
                primer_apellido: $('#primer_apellido').val(),
                segundo_apellido: $('#segundo_apellido').val(),
                entidad_de_nacimiento: $('#entidad_de_nacimiento').val(),
                fecha_de_nacimiento: $('#fecha_de_nacimiento').val(),
                sexo: $('#sexo').val(),
                nacionalidad: $('#nacionalidad').val(),
                estado_civil: $('#estado_civil').val()
            }
        };
        const datosJson = JSON.stringify(datos_personales);
        guardarSeccion('datos_personales', datosJson);
    }
});

// ! Ajax para guardar los datos del formulario
const guardarSeccion = (seccion, datos) => {
    $.ajax({
        url: '/alumnos/guardar/seccion/alumno',
        method: 'POST',
        data: {
            seccion: seccion,
            datos: datos,
            _token: registroBladeVars.csrfToken
        },
        success: function (response) {
            console.log(response);
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
