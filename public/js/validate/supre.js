$(function(){
    var i = 0;
    var timeoutId = null;
    var errorCount = 0;  // Contador de errores para detectar problema recurrente
    var lastErrorTime = null;

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
                console.log('entro - Debounce activado')
                
                // DEBOUNCE: Cancelar timeout anterior
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                
                // Esperar 800ms después de que el usuario deje de escribir
                timeoutId = setTimeout(function() {
                    var valor = (document.getElementById(id).value).toUpperCase();
                    
                    // Validar que la clave no esté vacía
                    if (valor.trim() === '') {
                        document.getElementById('addmore['+x+'][importe]').value = null;
                        document.getElementById('addmore['+x+'][iva]').value = null;
                        document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Ingrese una clave de curso';
                        return;
                    }
                    
                    var datos = {valor: valor};
                    var url = "/supre/busqueda/curso";
                    var request = $.ajax
                    ({
                        url: url,
                        method: 'POST',
                        data: datos,
                        dataType: 'json',
                        timeout: 10000 // Timeout de 10 segundos
                    });

                    request.done(( respuesta) => {
                        console.log(respuesta);
                        // Reset error counter en respuesta exitosa
                        errorCount = 0;

                        if (respuesta === 'N/A') {
                            // No hay coincidencias
                            document.getElementById('addmore['+x+'][importe]').value = null;
                            document.getElementById('addmore['+x+'][iva]').value = null;
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = 'No se encontraron coincidencias para la clave';
                            return;
                        }

                        if (respuesta && respuesta.error) {
                            // El servidor devolvió un error controlado
                            console.error('Respuesta con error desde el servidor:', respuesta);
                            var mensaje = respuesta.message ? respuesta.message : 'Error interno al consultar curso';
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = mensaje;
                            return;
                        }

                        // Caso exitoso: procesar respuesta
                        try {
                            var iva = respuesta[0] * 0.16;
                            iva = parseFloat(iva).toFixed(2);
                            if(respuesta[1] == 'HONORARIOS' || respuesta[1] == 'HONORARIOS Y ASIMILADOS A SALARIOS') {
                                console.log(respuesta);
                                if(respuesta['tabuladorConIva'] == true) {
                                    total = respuesta[0];
                                    document.getElementById('tdiva').style.display = 'none';
                                    document.getElementById('thiva').style.display = 'none';
                                } else {
                                    total = respuesta[0]*1.16;
                                    document.getElementById('tdiva').style.display = 'table-cell';
                                    document.getElementById('thiva').style.display = 'table-cell';
                                }

                                document.getElementById('addmore['+x+'][iva]').value = iva;
                            } else {
                                total = respuesta[0];
                                document.getElementById('addmore['+x+'][iva]').value = '0.00';
                            }
                            total = parseFloat(total).toFixed(2);

                            document.getElementById('addmore['+x+'][importe]').value = total;
                            document.getElementById('no_recibo').value = respuesta['folio_recibo'] || '';
                            document.getElementById('fecha_expedicion').value = respuesta['fecha_expedicion'] || '';
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = null;
                            console.log('✅ Consulta exitosa - Sistema funcionando correctamente');
                        } catch (err) {
                            console.error('Error procesando respuesta del servidor:', err, respuesta);
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Respuesta inválida del servidor';
                        }
                    });

                    request.fail(( jqXHR, textStatus ) =>
                    {
                        console.error("Error en AJAX: " + textStatus);
                        
                        // Contar errores para detectar problema recurrente
                        var ahora = Date.now();
                        if (lastErrorTime && (ahora - lastErrorTime) < 5000) {
                            errorCount++;
                        } else {
                            errorCount = 1;
                        }
                        lastErrorTime = ahora;
                        
                        // Si hay 3+ errores en 5 segundos = PROBLEMA CRÍTICO
                        if (errorCount >= 3) {
                            alert('🚨 ALERTA CRÍTICA RECURRENTE 🚨\n\n' + 
                                  'Se han detectado ' + errorCount + ' errores en corto tiempo.\n\n' +
                                  'Esto indica: "FATAL: lo siento, ya tenemos demasiados clientes"\n\n' +
                                  'ACCIÓN INMEDIATA:\n' +
                                  '1. Contactar al administrador del servidor\n' +
                                  '2. Revisar logs de PostgreSQL\n' +
                                  '3. Posible que el índice no esté creado');
                            errorCount = 0; // Reset contador
                        }
                        
                        if (textStatus === 'timeout') {
                            document.getElementById('addmore['+x+'][importe]').value = null;
                            document.getElementById('addmore['+x+'][iva]').value = null;
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Timeout - intente de nuevo';
                            alert('⚠️ ALERTA: Timeout en búsqueda de curso\n\nProblema: La consulta tardó más de 10 segundos\nClave buscada: ' + valor + '\n\nVerificar servidor de base de datos');
                        } else if (textStatus === 'error' || jqXHR.status === 0) {
                            document.getElementById('addmore['+x+'][importe]').value = null;
                            document.getElementById('addmore['+x+'][iva]').value = null;
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Error en la conexión - intente de nuevo';
                            alert('🔴 ALERTA: Error de conexión a servidor\n\nProblema: ' + textStatus + '\nEstado HTTP: ' + jqXHR.status + '\n\nVerificar si el servidor está caído o hay problemas de conexión a BD');
                        } else {
                            document.getElementById('addmore['+x+'][importe]').value = null;
                            document.getElementById('addmore['+x+'][iva]').value = null;
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Error en la consulta';
                            alert('❌ Error: ' + textStatus + '\nDetalles: ' + jqXHR.responseText);
                        }
                    });

                }, 800); // Esperar 800ms después de que deje de escribir

            } else {

            }
        });
    });

});
