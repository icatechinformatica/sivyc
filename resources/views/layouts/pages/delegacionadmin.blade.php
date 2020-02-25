@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
<style>
    a.add_button, a.remove_button {
        position: absolute;
        left: 20px;
        top: 30px;
        z-index: 999;
        height: 34px;
        width: 34px;
      }
</style>
 <!--empieza aquí-->
 <div class="container g-pt-50">
   <form action="{{ route('addsupre') }}" method="POST">
       @csrf
       <div style="text-align: right;width:72%">
           <label for="tituloSupre1"><h1>Suficiencia Presupuestal Fase 1</h1></label>
        </div>
        <hr style="border-color:dimgray">
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
        <table class="table table-bordered" id="dynamicTable">
            <tr>
                <th>Folio</th>
                <th>Clave Curso</th>
                <th>Importe total</th>
                <th>Iva</th>
                <th>Acción</th>
            </tr>
            <tr>
                <td><input type="text" name="addmore[0][folio]" placeholder="folio" class="form-control" /></td>
                <td><input type="text" name="addmore[0][clavecurso]" placeholder="clave curso" class="form-control" /></td>
                <td><input type="text" name="addmore[0][importe]" placeholder="importe total" class="form-control" /></td>
                <td><input type="text" name="addmore[0][iva]" placeholder="Iva" class="form-control" /></td>
                <td><button type="button" name="add" id="add" class="btn btn-success">Agregar</button></td>
            </tr>
        </table>

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

