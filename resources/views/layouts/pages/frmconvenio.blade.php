@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
 <!--empieza aquí-->

 <div class="container g-pt-50">
    <form method="POST"  enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div style="text-align: center">
            <label for="tituloagregar_convenio"><h1>Agregar Convenio</h1></label>
         </div>
         <hr style="border-color:dimgray">
        <div class="form-row">
            <!-- Organismo -->
            <div class="form-group col-md-6">
                <label for="institucion" class="control-label">Institución</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="institucion" name="institucion" placeholder="Institución">
            </div>
            <!--Organismo Fin-->
            <!-- Direccion -->
            <div class="form-group col-md-6">
                <label for="tipo" class="control-label">Tipo</label>
                <input type="text" class="form-control" id="tipo" name="tipo" placeholder="Tipo">
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
                <label for="fecha_firma" class="control-label">Telefono </label>
                <input type='text' id="from" name="fecha_firma" class="form-control datepicker" />
            </div>
            <!--Fecha inicial END-->
            <!-- Fecha conclusion -->
            <div class="form-group col-md-6">
                <label for="fecha_firma" class="control-label">Telefono </label>
                <input type='text' id="from" name="fecha_firma" class="form-control datepicker" />
            </div>
            <!-- Fecha conclusion END-->
        </div>

        <div class="form-group col-md-6">
            <label for="date-picker-example">Firma de Convenio</label>
            <input placeholder="Selected date" type="text" id="to" class="form-control datepicker">
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
 <script>
    $( function() {
      var dateFormat = "mm/dd/yy",
        from = $( "#from" )
          .datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1
          })
          .on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
        to = $( "#to" ).datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1
        })
        .on( "change", function() {
          from.datepicker( "option", "maxDate", getDate( this ) );
        });

      function getDate( element ) {
        var date;
        try {
          date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
          date = null;
        }

        return date;
      }
    } );
</script>

@stop
