$(function(){
    /**
     * documento de modal
    */
   $('#validarModel').on('show.bs.modal', function(event){
       var button = $(event.relatedTarget);
       var id = button.data('id');
       $('#validarForm').attr("action", "/pago/validacion" + "/" + id);
   });

   //Modal Subir Valsupre Firmado
   $('#DocModal').on('show.bs.modal', function(event){
    var button = $(event.relatedTarget);
    var id = button.data('id');
    $('#idinsmod').val(id);
    });

    //Modal Reemplazar Valsupre Firmado
   $('#DocModal2').on('show.bs.modal', function(event){
    var button = $(event.relatedTarget);
    var id = button.data('id');
    document.getElementById('idinsmod2').value = id;
    //$('idinsmod').val(id);
    });

    //Modal Subir Supre Firmado
   $('#DocSupreModal').on('show.bs.modal', function(event){
    var button = $(event.relatedTarget);
    var id = button.data('id');
    $('#idsupmod').val(id);
    });

    //Modal Reemplazar Supre Firmado
   $('#DocSupreModal2').on('show.bs.modal', function(event){
    var button = $(event.relatedTarget);
    var id = button.data('id');
    document.getElementById('idsupmod2').value = id;
    //$('idinsmod').val(id);
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
            if(id['5'] != "")
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger");
                $('#valsupre_pdf').attr("href", id['5']);
            }
            else
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger disabled");
            }
        }
        else if(id['4'] == 'Pago_Rechazado')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger");
            $('#contrato_pdf').attr("href", "/contrato/" + id['1']);
            $('#docs_pdf').attr("class", "btn btn-danger");
            $('#docs_pdf').attr("href", id['2']);
            if(id['5'] != "")
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger");
                $('#valsupre_pdf').attr("href", id['5']);
            }
            else
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger disabled");
            }
        }
        else if(id['4'] == 'Validado')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger disabled");
            $('#docs_pdf').attr("class", "btn btn-danger disabled");
            if(id['5'] != "")
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger");
                $('#valsupre_pdf').attr("href", id['5']);
            }
            else
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger disabled");
            }
        }
        else if(id['4'] == 'Contrato_Rechazado')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger disabled");
            $('#docs_pdf').attr("class", "btn btn-danger disabled");
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            if(id['5'] != "")
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger");
                $('#valsupre_pdf').attr("href", id['5']);
            }
            else
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger disabled");
            }
        }
        else if(id['4'] == 'Contratado')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger");
            $('#contrato_pdf').attr("href", "/contrato/" + id['1']);
            $('#docs_pdf').attr("class", "btn btn-danger disabled");
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            if(id['5'] != "")
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger");
                $('#valsupre_pdf').attr("href", id['5']);
            }
            else
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger disabled");
            }
        }
        else if(id['4'] == 'Validando_Contrato')
        {
            $('#sol_pdf').attr("class", "btn btn-danger disabled");
            $('#contrato_pdf').attr("class", "btn btn-danger");
            $('#contrato_pdf').attr("href", "/contrato/" + id['1']);
            $('#docs_pdf').attr("class", "btn btn-danger disabled");
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            if(id['5'] != "")
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger");
                $('#valsupre_pdf').attr("href", id['5']);
            }
            else
            {
                $('#valsupre_pdf').attr("class", "btn btn-danger disabled");
            }
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
            if(id['3'] != "")
            {
                $('#supre2_pdf').attr("class", "btn btn-danger");
                $('#supre2_pdf').attr("href", id['3']);
            }
            if(id['3'] == "")
            {
                $('#supre2_pdf').attr("class", "btn btn-danger disabled");
            }
            $('#valsupre2_pdf').attr("class", "btn btn-danger disabled");
        }
        else if(id['1'] == 'Validado')
        {
            $('#supre_pdf').attr("class", "btn btn-danger");
            $('#supre_pdf').attr("href", "/supre/pdf/" + id['0']);
            $('#anexo_pdf').attr("class", "btn btn-danger");
            $('#anexo_pdf').attr("href", "/supre/tabla-pdf/" + id['0']);
            $('#valsupre_pdf').attr("class", "btn btn-danger");
            $('#valsupre_pdf').attr("href","/supre/validacion/pdf/" + id['0']);
            if(id['3'] != "")
            {
                $('#supre2_pdf').attr("class", "btn btn-danger");
                $('#supre2_pdf').attr("href", id['3']);
            }
            if(id['3'] == "")
            {
                $('#supre2_pdf').attr("class", "btn btn-danger disabled");
            }
            if(id['2'] != "")
            {
                $('#valsupre2_pdf').attr("class", "btn btn-danger");
                $('#valsupre2_pdf').attr("href", id['2']);
            }
            if(id['2'] == "")
            {
                $('#valsupre2_pdf').attr("class", "btn btn-danger disabled");
            }
        }
    });

    //Modal de reincio de supre en supre
    $('#restartModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#confirm_restart').attr("href","/supre/reiniciar/" + id);
    });

    //Modal de reincio de Contrato
    $('#restartModalContrato').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#confirm_restart').attr("href","/contrato/reiniciar/" + id);
    });

    $('#recepcionModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        // $('#confirm_recepcion').attr("href","/recepcion/" + id);
        document.getElementById('idf').value = id;
    });

    //Modal de reincio de Pago
    $('#restartModalPago').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#confirm_restart2').attr("href","/pago/reiniciar/" + id);
    });

    //Modal de cancelacion de folio
    $('#cancelModalFolio').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        document.getElementById('idf').value = id;
    });

    //Modal de modificacion de folio validado de supre
    $('#modfolioModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var valor = button.data('id');
        var datos = {valor: valor};
        var url = '/supre/busqueda/folios/modal';
        var request = $.ajax
        ({
            url: url,
            method: 'POST',
            data: datos,
            dataType: 'json'
        });

        request.done(( respuesta) =>
        {
            const $select = document.querySelector("#folios")

            for (let i = $select.options.length; i >= 0; i--)
            {
                $select.remove(i);
            }

            respuesta.forEach( function(valor, indice, array)
            {
                console.log("En el índice " + indice + " hay este valor: " + valor['id_folios']);
                const option = document.createElement('option');
                option.value = valor['id_folios'];
                option.text = valor['folio_validacion'];
                $select.appendChild(option)
            });
            //console.log(respuesta['1']['id_folios']);
            /*const $select = document.querySelector("#folios");
            option.value = valor;
            option.text = valor;
            $select.appendChild(option);*/
        });

        //$('#confirm_restart').attr("href","/supre/reiniciar/" + id);
    });

});
