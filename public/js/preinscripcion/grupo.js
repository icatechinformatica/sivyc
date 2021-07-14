$(document).ready(function(){
    $("#tipo" ).change(function(){
        cmb_curso();
    });
     $("#unidad" ).change(function(){
        cmb_curso();
    });  
      
    function cmb_curso(){ 
        var tipo =$('#tipo').val();
        var unidad =$('#unidad').val(); 
        $("#id_curso").empty();                            
        if(tipo && unidad){
            $.ajax({
                type: "GET",
                url: "grupo/cmbcursos",
                data:{tipo:tipo,unidad:unidad, _token:"{{csrf_token()}}"},
                contentType: "application/json",              
                dataType: "json",
                success: function (data) {// console.log(data); 
                    $.each(data, function () {                                    
                        //$("#id_curso").append('<option value="" selected="selected">SELECCIONAR</option>');                                    
                        $("#id_curso").append('<option value="'+this['id']+'">'+this['nombre_curso']+'</option>');
                    });
                }
            });                        
        }
                        
    };
 

    if( $('#cerss_ok').is(':checked')){
            $('#cerss').prop('disabled', false);
            document.getElementById("resultado").innerText = "";
            document.getElementById("resultado").classList.add("white");
            $( ".no-cerss" ).hide();
            $( ".cerss" ).show();  
    }
    
    $("#cerss_ok").click(function(){
        if( $('#cerss_ok').prop('checked') ){
            $('#cerss').prop('disabled', false);                        
           // $("#etiqueta").text("EXPEDIENTE No.");
            document.getElementById("resultado").innerText = "";
            document.getElementById("resultado").classList.add("white");
            $( ".no-cerss" ).hide();
            $( ".cerss" ).show();  
        }else {
            $('#cerss').prop('disabled', 'disabled');                        
           // $("#etiqueta").text("CURP");
            resultado.classList.remove("white");
            $( ".no-cerss" ).show();   
            $( ".cerss" ).hide();                     
        }                
    });
   
  }); 
   

             
            
    function validarInput(input) {
        if( !$('#cerss_ok').prop('checked') ){                        
            var curp = input.value.toUpperCase(),
            resultado = document.getElementById("resultado"),valido = "No válido";
                        
            if (curpValida(curp)) {
                valido = "Válido";
                resultado.classList.add("ok");
            } else {
                resultado.classList.remove("ok");
            }
            resultado.innerText = "  Formato: " + valido;
                    
        }
    }
                
    function curpValida(curp) {                    
        var re = /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0\d|1[0-2])(?:[0-2]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/,
        validado = curp.match(re);                	
        if (!validado) return false;                    
                    
        function digitoVerificador(curp17) {                    
            var diccionario  = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ", lngSuma      = 0.0, lngDigito    = 0.0;
            for(var i=0; i<17; i++) lngSuma= lngSuma + diccionario.indexOf(curp17.charAt(i)) * (18 - i);
            lngDigito = 10 - lngSuma % 10;
            if(lngDigito == 10) return 0;
            return lngDigito;
        }
        if (validado[2] != digitoVerificador(validado[1])) return false;
                        
        return true; 
    }
    
    