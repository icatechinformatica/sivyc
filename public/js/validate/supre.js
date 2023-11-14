$(function(){
    var i = 0;

    $( document ).on('input', function(){
        $('input').on('input', function(event){
            id = this.id;
            x = id.substring(8,10);
            comp = x.substring(1);
            if(comp == ']')
            {
                x = id.substring(8,9);
            }
            if (id == 'addmore['+x+'][clavecurso]') {
                console.log('entro')
                var valor = (document.getElementById(id).value).toUpperCase();
                var datos = {valor: valor};
                var url = "/supre/busqueda/curso";
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
                            iva = respuesta[0] * 0.16;
                            iva = parseFloat(iva).toFixed(2);
                            if(respuesta[1] == 'HONORARIOS' || respuesta[1] == 'HONORARIOS Y ASIMILADOS A SALARIOS')
                            {
                                console.log(respuesta)
                                if(respuesta['tabuladorConIva'] == true) {
                                    total = respuesta[0];
                                    document.getElementById('tdiva').style.display = 'none';
                                    document.getElementById('thiva').style.display = 'none';
                                } else {
                                    total = respuesta[0]*1.16
                                    document.getElementById('tdiva').style.display = 'table-cell';
                                    document.getElementById('thiva').style.display = 'table-cell';
                                }

                                document.getElementById('addmore['+x+'][iva]').value = iva;
                            }
                            else
                            {
                                total = respuesta[0]
                                document.getElementById('addmore['+x+'][iva]').value = 0.00;
                            }
                            total = parseFloat(total).toFixed(2);

                            document.getElementById('addmore['+x+'][importe]').value = total;
                            document.getElementById('norecibo').value = respuesta['recibo'];
                            document.getElementById('movimiento_bancario').value = respuesta['movimiento_bancario'];
                            document.getElementById('fecha_movimiento_bancario').value = respuesta['fecha_movimiento_bancario'];
                            // document.getElementById('factura').value = respuesta['factura'];
                            // document.getElementById('fecha_factura').value = respuesta['fecha_factura'];
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = null;
                        }else{
                            console.log("ESto es una respuesta" + respuesta);
                            //Puedes mostrar un mensaje de error en algún div del DOM
                        }
                    }
                });

            request.fail(( jqXHR, textStatus ) =>
            {
                alert( "Hubo un error: " + jqXHR.responseText );
            });

            } else {

            }
        });
    });

   /* $( document ).on('input', function(){
        $('input').on('input', function(event){
            id = this.id;
            x = id.substring(8,10);
            comp = x.substring(1);
            if(comp == ']')
            {
                x = id.substring(8,9);
            }
            if (id == 'addmore['+x+'][folio]') {
                var valor = (document.getElementById(id).value).toUpperCase();
                var datos = {valor: valor};
                var url = '/supre/busqueda/folio';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                console.log('hola');
                request.done(( respuesta) =>
                {
                    console.log(respuesta);
                    if (respuesta != 'N/A') {
                        document.getElementById('addmore['+x+'][avisofolio]').innerHTML = 'Folio Existente';
                    } else {
                        if(!respuesta.hasOwnProperty('error')){
                            console.log('respuesta= ')
                            console.log(respuesta)
                            document.getElementById('addmore['+x+'][avisofolio]').innerHTML = null;
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
    });*/

});
