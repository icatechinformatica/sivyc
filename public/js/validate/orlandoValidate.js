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

});
