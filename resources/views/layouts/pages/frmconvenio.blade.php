@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
 <!--empieza aquí-->

 <div class="container g-pt-50">
    <form method="POST">
        <div style="text-align: right;width:63%">
            <label for="tituloagregar_convenio"><h1>Agregar Convenio</h1></label>
         </div>
         <hr style="border-color:dimgray">
      <div class="form-row">
        <!-- Organismo -->
        <div class="form-group col-md-6">
           <label for="organismo" class="control-label">Organismo </label>
          <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="organismo" name="organismo" placeholder="organismo">
        </div>
        <!--Organismo Fin-->
        <!-- Direccion -->
        <div class="form-group col-md-6">
          <label for="direccion" class="control-label">Direccion</label>
          <input type="text" class="form-control" id="direccion" name="direccion" placeholder="direccion">
        </div>
        <!-- Direccion FIN-->
      </div>

      <div class="form-row">
        <!-- Telefono -->
        <div class="form-group col-md-6">
          <label for="telefono" class="control-label">Telefono </label>
          <input type="text" class="form-control" onkeypress="return solonumeros(event)" id="telefono" name="telefono" placeholder="telefono">
        </div>
        <!--Telefono Fin-->
        <!-- Tipo de sector -->
        <div class="form-group col-md-6">
            <label for="sector">Tipo de Sector</label>
            <select class="form-control" id="sector">
              <option>Publico</option>
              <option>Privado</option>
            </select>
        </div>
        <!-- Fin Sector-->
      </div>

      <div class="form-row">
        <!-- fecha inicial -->
        <div class="form-group col-md-6">
            <div class='input-group date' id='datetimepicker6'>
                <input type='text' class="form-control datepicker" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <!--Fecha inicial END-->
        <!-- Fecha conclusion -->
        <div class="form-group col-md-6">
            <label for="sector">Tipo de Sector</label>
            <select class="form-control" id="sector">
              <option>Publico</option>
              <option>Privado</option>
            </select>
        </div>
        <!-- Fecha conclusion END-->
      </div>

      <div class="form-group col-md-6">
        <label for="date-picker-example">Firma de Convenio</label>
        <input placeholder="Selected date" type="text" id="date-picker-example" class="form-control datepicker">
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="remitente" class="control-label">Adjunto Convenio</label>
          <input type="file" id="myFile" name="myFile" class="form-control" accept="application/pdf">
        </div>

        <div class="form-group col-md-6">
            <label for="sector">Estatus</label>
            <select class="form-control" id="sector">
              <option>Activo</option>
              <option>Terminado</option>
            </select>
        </div>


      </div>
      <br>
    </form>
 </div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script type="text/javascript">
    $( function() {
        $( ".datepicker" ).datepicker();
    } );
 </script>

@stop
