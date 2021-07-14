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