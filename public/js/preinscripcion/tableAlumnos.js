function  eliminar(id, route){   
   var fila = "#"+id;   
    if(confirm("Est\u00E1 seguro de ejecutar la acci\u00F3n?")==true){        
        
        $.ajax({
            url: route,
            data: {id : id},         
            type:  'GET',
            dataType : 'text',
            success: function(response) {
             console.log(response);                            
                if(response==true){                                      
                    alert("La eliminaci\u00F3n ha sido efectuada!!") ;
                    $(fila).remove(); 
                    //document.getElementById('tblAlumnos').render();
                }else{
                    
                    alert("Error, instrucci\u00F3n no valida.");
                }   
            },
            statusCode: {
                404: function() {
                alert('Página no encontrada');
                }
            },
            error:function(x,xs,xt){                            
                alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
            }
        });
    
 
    } else{
        $('#textURL').val("OPERACI\u00D3N CANCELADA");
    }
}

function fileValidationpdf() {
    var fileInput = document.getElementById('customFile');
    var filePath = fileInput.value;
    var fileSize = fileInput.files[0].size;
    var allowedExtensions = /(.pdf)$/i;
    if (!allowedExtensions.exec(filePath)) {
        alert('Por favor solo cargar archivos pdf');
        fileInput.value = '';
        return false;
    } else {
        if (fileSize > 5000000) {
            alert('Por favor el archivo debe pesar menos de 5MB');
            fileInput.value = '';
            return false;
        }
        //Image preview
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '"/>';
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
}

function rem(id){
    var id = id;
    $('#curpo').val(id);
    $('#exampleModal').modal('show')
}