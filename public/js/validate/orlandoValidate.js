$(function(){
    //metodo
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#search_").click(function(e){
        e.preventDefault();
        $.ajax({
            type:'POST',
            url:'/pago/fill',
            data: { numero_contrato: $('#numero_contrato').val()},
            success: function(data){
                nombre = data.nombre + " " + data.apellido_paterno + " " + data.apellido_materno
                $('#numero_control').val(data.id)
                $('#nombre_instructor').val(nombre)
            },
        });
    });
    // Boton modificar en pagosmod
    $("#mod_").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#numero_control').prop("disabled", false)
                $('#nombre_curso').prop("disabled", false)
                $('#nombre_instructor').prop("disabled", false)
                $('#numero_contrato').prop("disabled",false)
                $('#clave_grupo').prop("disabled",false)
                $('#unidad_cap').prop("disabled",false)
                $('#tipo_pago').prop("disabled",false)
                $('#monto_pago').prop("disabled",false)
                $('#iva').prop("disabled",false)
                $('#numero_pago').prop("disabled",false)
                $('#fecha_pago').prop("disabled",false)
                $('#concepto').prop("disabled",false)
                $('#nombre_solicita').prop("disabled",false)
                $('#nombre_autoriza').prop("disabled",false)
                $('#reacd02').prop("disabled",false)
            }
        });
    });
    // Boton modificar en verinstructor
    $("#mod_instructor").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#nombre').prop("disabled", false)
                $('#apellido_paterno').prop("disabled", false)
                $('#apellido_materno').prop("disabled", false)
                $('#curp').prop("disabled",false)
                $('#rfc').prop("disabled",false)
                $('#sexo').prop("disabled",false)
                $('#estado_civil').prop("disabled",false)
                $('#fecha_nacimiento').prop("disabled",false)
                $('#lugar_nacimiento').prop("disabled",false)
                $('#lugar_residencia').prop("disabled",false)
                $('#domicilio').prop("disabled",false)
                $('#telefono').prop("disabled",false)
                $('#correo').prop("disabled",false)
                $('#banco').prop("disabled",false)
                $('#clabe').prop("disabled",false)
                $('#numero_cuenta').prop("disabled",false)
                $('#exp_laboral').prop("disabled",false)
                $('#exp_docente').prop("disabled",false)
                $('#cursos_recibidos').prop("disabled",false)
                $('#cursos_conocer').prop("disabled",false)
                $('#cursos_impartidos').prop("disabled",false)
                $('#capacitado_icatech').prop("disabled",false)
                $('#cursos_recicatech').prop("disabled",false)
                $('#cv').prop("disabled",false)
                $('#numero_control').prop("disabled",false)
                $('#tipo_honorario').prop("disabled",false)
                $('#registro_agente').prop("disabled",false)
                $('#uncap_validacion').prop("disabled",false)
                $('#memo_validacion').prop("disabled",false)
                $('#memo_mod').prop("disabled",false)
                $('#observacion').prop("disabled",false)
            }
        });
    });

});
