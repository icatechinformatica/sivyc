$( function() {
        $('#frmAlumno').validate({
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
            edad: {
                    required: true,digits: true
            },
            escolaridad: {
                    required: true 
            },            
            fecha_inscripcion: { 
                    required: true 
            },
            documentos: {
                    required: true 
            },            
            curso: { 
                    required: true 
            },           
            fecha_autorizacion: { 
                required: true
            },           
            tipo: { 
                    required: true 
            },
            lugar: { 
                    required: true 
            },
            cuota: { 
                    required: true,digits: true
            },            
            fecha_inicio: { 
                    required: true 
            },
            fecha_termino: { 
                    required: true 
            },
            hinicio: { 
                    required: true 
            },
            hfin: { 
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
            edad: { 
                    required: 'Por favor ingrese su edad',
                    digits: 'S\u00F3lo se aceptan n\u00FAmeros'
            },
            escolaridad: { 
                    required: 'Por favor ingrese su escolaridad' 
            },            
            fecha_inscripcion: { 
                    required: 'Por favor ingrese la fecha de inscripci\u00F3n'
            },
            documentos: { 
                    required: 'Por favor ingrese que documentos proporcion\u00F3.' 
            },            
            curso: { 
                    required: 'Por favor ingrese el nombre del curso' 
            },            
            fecha_autorizacion: { 
                    required: 'Por favor ingrese la fecha de autorizaci\u00F3n'
            },            
            tipo: { 
                    required: 'Por favor ingrese el tipo de curso' 
            },
            lugar: { 
                    required: 'Por favor ingrese el lugar' 
            },
            cuota: { 
                    required: 'Por favor ingrese la cuota de recuperaci\u00F3n pagada',
                    digits: 'S\u00F3lo se aceptan n\u00FAmeros' 
            },
            fecha_inicio: { 
                    required: 'Por favor ingrese la fecha de inicio'
            },
            fecha_termino: { 
                    required: 'Por favor ingrese la fecha de termino',
                    date: 'Formato de fecha no v\u00E1lido' 
            },
            hinicio: { 
                    required: 'Por favor ingrese la hora de inicio' 
            },
            hfin: { 
                    required: 'Por favor ingrese la hora de termino' 
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