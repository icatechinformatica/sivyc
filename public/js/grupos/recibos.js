$(document).ready(function(){

    $("#buscar").click(function(){
        $('#frm').attr('action', $("#buscar").val());
        $('#frm').attr('target', '_self');
        $('#frm').submit(); 
    });

    $("#asignar" ).click(function(){ 
        if(confirm("Esta seguro de signar el número de recibo?")==true){ 
            $('#frm').attr('action', $("#asignar" ).val()); 
            $('#frm').attr('target', '_self');
            $('#frm').submit(); 
        }
    }); 

    $("#modificar").click(function(){ 
        if(confirm("Esta seguro de modificar?")==true){ 
            $('#frm').attr('action', $("#modificar").val());
            $('#frm').attr('target', '_self');
            $('#frm').submit(); 
        }
    }); 

    $("#pdfRecibo").click(function(){                     
        let url = $("#pdfRecibo").val();
        window.open(url, "_blank");
     });

     $("#movimiento").change(function(){
        $("#inputFile").hide();
        $("#aceptar").hide();
        $("#motivo").hide();
        switch($("#movimiento" ).val()){
            case "SUBIR":
                $("#inputFile").show("slow");                                                        
                $("#aceptar").text("SUBIR");
                $("#aceptar").show("slow");
            break;
            case "ESTATUS":                            
                $("#status_recibo").show("slow");
                $("#aceptar").text("ACEPTAR");
                $("#aceptar").show("slow");
            break;  
            case "DESHACER":                            
                $("#aceptar").text("ACEPTAR");
                $("#aceptar").show("slow");
            break;
            case "SOPORTE":
                $("#aceptar").text("ENVIAR");
                $("#motivo").show("slow");
                $("#aceptar").show("slow");
            break;
            case "CANCELAR":
                $("#aceptar").text("ACEPTAR");
                $("#motivo").show("slow");
                $("#aceptar").show("slow");
            break;
        }
    });
             
    $("#aceptar").click(function(){
            if(confirm("Esta seguro de ejecutar la acción?")==true){ 
                $('#frm').attr('action', $("#aceptar").val()); 
                $('#frm').attr('target', '_self');
                $('#frm').submit(); 
            }
    });
    
    $("#enviar").click(function(){ 
        if(confirm("Esta seguro de enviar el recibo de pago?")==true){ 
            $('#frm').attr('action', $("#enviar").val()); 
            $('#frm').attr('target', '_self');
            $('#frm').submit(); 
        }
    }); 

    $("#cantidad").on("blur", function() {
        $("#importe").val($("#cantidad").val()*$("#precio_unitario").val());
    });

});     
 
