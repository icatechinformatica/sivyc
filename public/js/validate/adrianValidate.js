$(function(){
    var i = 0;

    $("#add").click(function(){

        ++i;
        $("#dynamicTable").append('<tr><td><input type="text" name="addmore['+i+'][folio]" id="addmore['+i+'][folio]" placeholder="folio" class="form-control" /></td><td><input type="text" name="addmore['+i+'][numeropresupuesto]" id="addmore['+i+'][numeropresupuesto]" placeholder="Numero Presupuesto" class="form-control" disabled value="12101" /></td><td><input type="text" name="addmore['+i+'][clavecurso]" id="addmore['+i+'][clavecurso]" placeholder="Clave curso" class="form-control" /></td><td><input type="text" name="addmore['+i+'][importe]" id="addmore['+i+'][importe]" placeholder="importe total" class="form-control" readonly /><footer name="addmore['+i+'][aviso]" id="addmore['+i+'][aviso]" style="color: red"></footer></td><td><input type="text" name="addmore['+i+'][iva]" id="addmore['+i+'][iva]" placeholder="Iva" class="form-control" readonly /></td><td><input type="text" name="addmore['+i+'][comentario]" id="addmore['+i+'][comentario]" placeholder="comentario" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Eliminar</button></td></tr>');
        //
    });

    $(document).on('click', '.remove-tr', function(){
         $(this).parents('tr').remove();
    });

    $( document ).on('input', function(){
        $('input').on('input', function(event){
            id = this.id;
            x = id.substring(8,10);
            comp = x.substring(1);
            if(comp == ']')
            {
                x = id.substring(8,9);
                console.log('enntro');
            }
            console.log(x);
            if (id == 'addmore['+x+'][clavecurso]') {
                var valor = (document.getElementById(id).value).toUpperCase();
                var datos = {valor: valor};
                var url = '/supre/busqueda/curso';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request.done(( respuesta) =>
                {
                    console.log(respuesta);
                    if (respuesta == 'N/A') {
                        document.getElementById('addmore['+x+'][importe]').value = null;
                        document.getElementById('addmore['+x+'][iva]').value = null;
                        document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Clave de Curso Invalida';
                    } else {
                        if(!respuesta.hasOwnProperty('error')){
                            console.log('respuesta= ')
                            console.log(respuesta)
                            iva = respuesta * 0.16;
                            iva = parseFloat(iva).toFixed(2);
                            total = respuesta*1.16
                            total = parseFloat(total).toFixed(2);

                            document.getElementById('addmore['+x+'][importe]').value = total;

                            document.getElementById('addmore['+x+'][iva]').value = iva;

                            document.getElementById('addmore['+x+'][aviso]').innerHTML = null;
                        }else{

                            //Puedes mostrar un mensaje de error en algún div del DOM
                        }
                    }
                });

            request.fail(( jqXHR, textStatus ) =>
            {
                alert( "Hubo un error: " + textStatus );
            });

            } else {

            }
        });
    });
});
