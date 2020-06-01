$(function(){
    //boton valsupre rechazar
    $("#rechazarPago").click(function(e){
        e.preventDefault();
        $('#rechazar_pago').prop("class", "form-row");
        $('#btn_rechazar').prop("class", "form-row");
        //$('#observaciones').rules('add',  { required: true });
    });

    /**
     * documento de modal
    */
   $('#validarModel').on('show.bs.modal', function(event){
       var button = $(event.relatedTarget);
       var id = button.data('id');
       $('#validarForm').attr("action", "/pago/validacion" + "/" + id);
   });

   //Modal en Contratos
   $('#myModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id);
        if(id['4'] == 'Finalizado' || id['4'] == 'Verificando_Pago' || id['4'] == "Pago_Verificado")
        {
            $('#sol_pdf').attr("class", "btn btn-danger");
            $('#sol_pdf').attr("href", "/contrato/solicitud-pago/pdf/" + id['0']);
            $('#contrato_pdf').attr("class", "btn btn-danger");
            $('#contrato_pdf').attr("href", "/contrato/" + id['1']);
            $('#docs_pdf').attr("class", "btn btn-danger");
            $('#docs_pdf').attr("href", id['2']);
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            $('#valsupre_pdf').attr("href","/supre/validacion/pdf/" + id['3']);
        }
        else if(id['4'] == 'Pago_Rechazado' || id['4'] == 'Validado')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger disabled");
            $('#docs_pdf').attr("class", "btn btn-danger disabled");
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            $('#valsupre_pdf').attr("href","/supre/validacion/pdf/" + id['3']);
        }
        else if(id['4'] == 'Contratado')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger");
            $('#contrato_pdf').attr("href", "/contrato/" + id['1']);
            $('#docs_pdf').attr("class", "btn btn-danger disabled");
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            $('#valsupre_pdf').attr("href","/supre/validacion/pdf/" + id['3']);
        }
        else if(id['4'] == 'Validando_Contrato')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger disabled");
            $('#docs_pdf').attr("class", "btn btn-danger disabled");
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            $('#valsupre_pdf').attr("href","/supre/validacion/pdf/" + id['3']);
        }
    });

    //Modal en Supre
    $('#supreModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id);
        if(id['1'] == 'En_Proceso' || id['1'] == 'Rechazado')
        {
            $('#supre_pdf').attr("class", "btn btn-danger");
            $('#supre_pdf').attr("href", "/supre/pdf/" + id['0']);
            $('#anexo_pdf').attr("class", "btn btn-danger");
            $('#anexo_pdf').attr("href", "/supre/tabla-pdf/" + id['0']);
            $('#valsupre_pdf').attr("class", "btn btn-danger disabled");
        }
        else if(id['1'] == 'Validado')
        {
            $('#supre_pdf').attr("class", "btn btn-danger");
            $('#supre_pdf').attr("href", "/supre/pdf/" + id['0']);
            $('#anexo_pdf').attr("class", "btn btn-danger");
            $('#anexo_pdf').attr("href", "/supre/tabla-pdf/" + id['0']);
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            $('#valsupre_pdf').attr("href","/supre/validacion/pdf/" + id['0']);
        }
    });
   /**
    * modificacion de los input a uppercase
    */
   $("input[type=text], textarea, select").keyup(function () {
        $(this).val($(this).val().toUpperCase());
    });
});
