@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
 <!--empieza aquí-->
 <div class="container g-pt-50">
   <form method="POST">
      <div class="form-row">
        <!-- Unidad -->
        <div class="form-group col-md-6">
          
          <label for="unidad" class="control-label">Unidad de Capacitacion </label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="unidad" name="unidad"  placeholder="unidad">
          
        </div>
        <!--Unidad Fin--> 
        <!-- Memorandum No. ICATECH -->
        <div class="form-group col-md-6">
          <label for="mamorandum" class="control-label">Memorandum No. </label>
          <input type="text" class="form-control" id="memorandum" name="memorandum" placeholder="ICATECH/0000/000/2020">
        </div>
        <!-- Memorandum No. ICATECH FIN-->
        
      </div>
      <div class="form-group">
        <label for="fecha" class="control-label">Fecha</label>
        <input class="form-control" name="fecha" type="date" value="2020-01-01" id="example-date-input">
      </div>
      <div class="form-row">
        <div class="form-group col-md-6"> <!-- Destinatario -->
          <label for="destino" class="control-label">Destinatario</label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="destino" name="destino" placeholder="Nombre">
        </div>    
  
        <div class="form-group col-md-6"> <!-- Puesto-->
          <label for="puesto" class="control-label">Puesto</label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="puesto" name="puesto" placeholder="Puesto">
        </div>

      </div>
      
      <div class="field_wrapper">
        <div class="form-row">
          <!--Folios-->
          <div class="form-group col-md-4"> <!-- Valida -->
            <label for="remitente" class="control-label">Folios</label>
            <input type="text" class="form-control" name="folios[]">
            <a href="javascript:void(0);" class="add_button" title="agregar campo"><img src="/img/agregar.png"></a>
          </div>
          <!--Folios END-->
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6"> <!--  -->
          <label for="remitente" class="control-label">Remitente</label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre">
        </div> 
      </div>
      <div class="form-row">
        <div class="form-group col-md-6"> <!-- copia 1 -->
          <label for="remitente" class="control-label">Copia Para</label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre, Puesto">
        </div>
        
        <div class="form-group col-md-6"> <!--  -->
          <label for="remitente" class="control-label">Copia Para</label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre, Puesto">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6"> <!-- Valida -->
          <label for="remitente" class="control-label">Valida</label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre, Puesto">
        </div>
        
        <div class="form-group col-md-6"> <!--Elabora-->
          <label for="remitente" class="control-label">Elabora</label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre, Puesto">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-12">
          <label for="remitente" class="control-label">Elabora</label>
          <input type="file" id="myFile" name="myFile" class="form-control" accept="application/pdf">
        </div>
      </div> 
      <div class="mt-3">
        <button type="submit" class="btn btn-primary">Enviar</button>
      </div>
      <br>
    </form>    
 </div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script type="text/javascript">
     $(document).ready(function(){
       var maxField = 50;
       var addButton = $('.add_button'); //Add button selector
       var wrapper = $('.field_wrapper'); //Input field wrapper
       var fieldHTML = '<div class="form-row"><div class="form-group col-md-4"><label for="remitente" class="control-label">Folio</label><input type="text" name="folios[]"  class="form-control" value=""/><a href="javascript:void(0);" class="remove_button"><img src="/img/quitar.png"/></a></div></div>'; //New input field html
       var x = 1; //Initial field counter is 1

         //Once add button is clicked
         $(addButton).click(function(){
           //Check maximum number of input fields
           if(x < maxField){ 
               x++; //Increment field counter
               $(wrapper).append(fieldHTML); //Add field html
           }
         });
       //Once remove button is clicked
         $(wrapper).on('click', '.remove_button', function(e){
           e.preventDefault();
           $(this).parent('div').remove(); //Remove field html
           x--; //Decrement field counter
       });
     });
     // Add the following code if you want the name of the file appear on select
     $(".custom-file-input").on("change", function() {
       var fileName = $(this).val().split("\\").pop();
       $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
     });

     function soloLetras(e) {
       key = e.keyCode || e.which;
       tecla = String.fromCharCode(key).toLowerCase();
       letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
       especiales = [8, 37, 39, 46];
   
       tecla_especial = false
       for(var i in especiales) {
           if(key == especiales[i]) {
               tecla_especial = true;
               break;
           }
       }
   
       if(letras.indexOf(tecla) == -1 && !tecla_especial)
           return false;
   }
   
   function limpia() {
       var val = document.getElementById("miInput").value;
       var tam = val.length;
       for(i = 0; i < tam; i++) {
           if(!isNaN(val[i]))
               document.getElementById("miInput").value = '';
       }
   }
 </script>
      
@stop
  
  