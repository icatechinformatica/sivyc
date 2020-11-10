$( function() {
     $('#frmInstructor').validate({
        rules: {
            file_photo:{ 
                    required: true,extension: "jpg|jpeg|png",filesize: 2000000 
            },
            nombre: {
                    required: true, minlength: 3
            },
            apellidoPaterno: { 
                    required: true 
            },
            apellidoMaterno: {
                    required: true 
            },
            fecha_contrato: { 
                    required: true 
            },
            fecha_padron: { 
                    required: true 
            },
            monto_honorarios: { 
                    required: true,digits: true
            },
            nombre_curso: { 
                    required: true 
            },            
            fecha_autorizacion: { 
                    required: true 
            },
            modalidad_curso: { 
                    required: true 
            },
            inicio_curso: { 
                    required: true 
            },
            termino_curso: { 
                    required: true 
            },
            total_mujeres: { 
                    required: true,digits: true
            },
            total_hombres: { 
                    required: true,digits: true 
            },
            hini_curso: { 
                    required: true 
            },
            hfin_curso: { 
                required: true 
            },
            horas_diarias: { 
                required: true,digits: true 
            },
            horas_curso: { 
                required: true,digits: true
            },
            tipo_curso: { 
                required: true 
            },
            lugar_curso: { 
                required: true 
            },
            'file_data[]': {
                required: true,
                extension: "jpg|jpeg|png",
                filesize: 2000000
            }
        },
        messages: {
             file_photo: { 
                    required: 'Por favor ingrese su fotograf\u00EDa', 
                    extension: "jpg|jpeg|png|gif",
                    filesize: "El archivo debe ser menor de 2 MB"
            },
            nombre: { 
                    required: 'Por favor ingrese su nombre', 
                    minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios") 
            },
            apellidoPaterno: { 
                    required: 'Por favor ingrese su apellido paterno' 
            },
            apellidoMaterno: { 
                    required: 'Por favor ingrese su apellido materno' 
            },
            fecha_contrato: { 
                    required: 'Por favor ingrese la fecha de firma de contrato'
            },
            fecha_padron: { 
                    required: 'Por favor ingrese la fecha de inscripci\u00F3n al padr\u00F3n'
            },
            monto_honorarios: { 
                    required: 'Por favor ingrese el monto de honorarios',
                    digits: 'S\u00F3lo se aceptan n\u00FAmeros'                   
            },
            nombre_curso: { 
                    required: 'Por favor ingrese el nombre del curso' 
            },            
            fecha_autorizacion: { 
                    required: 'Por favor ingrese la fecha de autorizaci\u00F3n'
            },
            modalidad_curso: { 
                    required: 'Por favor ingrese la modalidad' 
            },
            inicio_curso: { 
                    required: 'Por favor ingrese la fecha de inicio'
            },
            termino_curso: { 
                    required: 'Por favor ingrese la fecha de termino'
            },
            total_mujeres: { 
                    required: 'Por favor ingrese el total de mujeres',
                    digits: 'S\u00F3lo se aceptan n\u00FAmeros'
            },
            total_hombres: { 
                    required: 'Por favor ingrese el total de hombres',
                    digits: 'S\u00F3lo se aceptan n\u00FAmeros'
            },
            hini_curso: { 
                    required: 'Por favor ingrese la hora de inicio' 
            },
            hfin_curso: { 
                    required: 'Por favor ingrese la hora de termino' 
            },
            horas_diarias: { 
                    required: 'Por favor ingrese las horas diarias',
                    digits: 'S\u00F3lo se aceptan n\u00FAmeros' 
            },
            horas_curso: { 
                    required: 'Por favor ingrese el total de horas',
                    digits: 'S\u00F3lo se aceptan n\u00FAmeros'
            },
            tipo_curso: { 
                    required: 'Por favor ingrese el tipo de curso' 
            },
            lugar_curso: { 
                    required: 'Por favor ingrese el lugar' 
            },
            'file_data[]':{
               required : "Por favor suba las capturas de pantallas.",
               extension:"S\u00F3lo se permiten archivos jpg, jpge y png.",
               filesize:"El tama\u00F1o del archivo debe ser inferior a 2MB."
               
            }
        }
     });
     
  
     $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy'
    }).attr('readonly', 'true').
      keypress(function(event){
        if(event.keyCode == 8){
            event.preventDefault();
        }
      });
    
     
} );