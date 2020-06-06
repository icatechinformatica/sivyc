$(function(){

    /**
    * validar el SID de modificación
     */
    $("#sid_registro_modificacion").validate({
        rules: {
            nombre_alum_mod: {
                required: true,
                minlength: 3
            },
            apellido_pat_mod: {
                required: true,
                minlength: 3
            },
            sexo_mod: {
                required: true
            },
            estado_mod: {
                required: true
            },
            municipio_mod: {
                required: true
            },
            estado_civil_mod: {
                required: true
            },
            discapacidad_mod: {
                required: true
            },
            dia_mod: {
                required: true
            },
            mes_mod: {
                required: true
            },
            anio: {
                required: true,
                maxlength: 4,
                number: true
            },
            medio_entero_mod: {
                required: true
            },
            motivos_eleccion_sistema_capacitacion: {
                required: true
            }
        },
        messages: {
            nombre_alum_mod: {
                required: 'Por favor ingrese su nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apellido_pat_mod: {
                required: 'Por favor ingrese su apellido'
            },
            sexo_mod: {
                required: 'Por favor Elegir su genero'
            },
            estado_mod: {
                required: 'Por favor, seleccione un estado'
            },
            municipio_mod: {
                required: 'Por favor, seleccione el municipio'
            },
            estado_civil_mod: {
                required: 'Por favor, seleccione su estado civil'
            },
            discapacidad_mod: {
                required: 'Por favor seleccione una opción'
            },
            ultimo_grado_estudios: {
                required: "Agregar último grado de estudios"
            },
            dia_mod: {
                required: "Por favor, seleccione el día"
            },
            mes_mod: {
                required: "Por favor, seleccione el mes"
            },
            anio_mod: {
                required: "Por favor, Ingrese el año",
                maxlength: "Sólo acepta 4 digitos",
                number: "Sólo se aceptan números"
            },
            medio_entero_mod: {
                required: "Por favor, seleccione una opción"
            },
            motivos_eleccion_sistema_capacitacion_mod: {
                required: "Por favor, seleccione una opción"
            }
        }
     });

     // escuchará los cambios del select de especialidades y enviará una petición Ajax para buscar los cursos de esa especialidad

    $("#motivos_eleccion_sistema_capacitacion_mod").change(function(){
        var selectedMerioEntero = $(this).children("option:selected").val();
        if (selectedMerioEntero != '0') {
            $("#sistema_capacitacion_especificar_mod").attr('disabled','disabled');
            $('#sistema_capacitacion_especificar_mod').val('');
        }
        else {
            $("#sistema_capacitacion_especificar_mod").removeAttr('disabled');
        }
    });
    /**
     * medio entero
     */
    $("#medio_entero_mod").change(function(){
        var selectedMerioEntero = $(this).children("option:selected").val();
        if (selectedMerioEntero != '0') {
            $("#medio_entero_especificar_mod").attr('disabled','disabled');
            $('#medio_entero_especificar_mod').val('');
        }
        else {
            $("#medio_entero_especificar_mod").removeAttr('disabled');
        }
    });
});
