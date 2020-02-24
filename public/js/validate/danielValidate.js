$(function(){
    // metodos
    $('#table-one').filterTable('#myInput');

    $.validator.addMethod("CURP", function (value, element) {
        if (value !== '') {
            var patt = new RegExp("^[A-Z][A,E,I,O,U,X][A-Z]{2}[0-9]{2}[0-1][0-9][0-3][0-9][M,H][A-Z]{2}[B,C,D,F,G,H,J,K,L,M,N,Ñ,P,Q,R,S,T,V,W,X,Y,Z]{3}[0-9,A-Z][0-9]$");
            return patt.test(value);
        } else {
            return false;
        }
    }, "Ingrese una CURP valida");

    $('#formsid').validate({
        rules: {
            nocontrol:{
                required: true
            },
            fecha:{
                required: true,
                date: true
            },
            nosolicitud: {
                required: true,
                digits: true
            },
            nombreaspirante: {
                required: true,
                minlength: 3
            },
            apaternoaspirante: {
                required: true
            },
            amaternoaspirante: {
                required: true
            },
            curpaspirante: {
                required: true,
                CURP: true
            }
        },
        messages: {
            nocontrol: {
                required: 'Por favor ingresa el número de control'
            },
            fecha: {
                required: 'Por favor ingresa la fecha',
                date: 'Formato de fecha no valido'
            },
            nosolicitud: {
                required: 'Por favor ingresa el número de solicitud',
                digits: 'Sólo se acceptan números'
            },
            nombreaspirante: {
                required: 'Por favor ingrese su nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apaternoaspirante: {
                required: 'Por favor ingrese su apellido'
            },
            amaternoaspirante: {
                required: 'Por favor ingrese su apellido'
            },
            curpaspirante: {
                required: 'Por favor Ingresé la curp',
            }
        }
    });
});
