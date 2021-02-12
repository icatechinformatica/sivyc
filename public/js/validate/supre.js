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
            console.log('hola');
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
