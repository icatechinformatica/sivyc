@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('title', 'Formulario de Convenio | Sivyc Icatech')

@section('content')
 <!--empieza aquí-->

 <div class="container g-pt-50">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div><br />
    @endif
    <form method="POST" action="{{ route('convenios.store') }}" id="conveniosFrm" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div style="text-align: center">
            <label for="tituloagregar_convenio"><h1>Agregar Convenio</h1></label>
         </div>
         <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="no_convenio" class="control-label">N° Contrato</label>
                <input type="text" class="form-control" id="no_convenio" name="no_convenio" placeholder="N° Convenio">
            </div>
        </div>
        <div class="form-row">
            <!-- Organismo -->
            <div class="form-group col-md-6">
                <label for="institucion" class="control-label">Institución</label>
                <input type="text" class="form-control" id="institucion" name="institucion" placeholder="Institución">
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
                <select class="form-control" id="sector" name="sector">
                    <option value="">----</option>
                    <option value="publico">Publico</option>
                    <option value="privado">Privado</option>
                </select>
            </div>
            <!-- Fin Sector-->
        </div>

        <div class="form-row">
            <!-- fecha inicial -->
            <div class="form-group col-md-6">
                <label for="fecha_firma" class="control-label">Fecha de Firma </label>
                <input type='text' id="fecha_firma" name="fecha_firma" class="form-control datepicker" />
            </div>
            <!--Fecha inicial END-->
            <!-- Fecha conclusion -->
            <div class="form-group col-md-6">
                <label for="fecha_termino" class="control-label">Fecha de Termino </label>
                <input type='text' id="fecha_termino" name="fecha_termino" class="form-control datepicker" />
            </div>
            <!-- Fecha conclusion END-->
        </div>

        <div class="form-row">
            <!--poblacion-->
            <div class="form-group col-md-6">
                <label for="poblacion" class="control-label">Población </label>
                <input type='text' id="poblacion" name="poblacion" class="form-control" />
            </div>
            <!--poblacion END-->
            <!--municipio-->
            <div class="form-group col-md-6">
                <label for="municipio" class="control-label">Municipio</label>
                <input type='text' id="municipio" name="municipio" class="form-control" />
            </div>
            <!--municipio END-->
        </div>

        <div class="form-row">
            <!--nombre_titular-->
            <div class="form-group col-md-6">
                <label for="nombre_titular" class="control-label">Nombre del Titular </label>
                <input type='text' id="from" name="nombre_titular" class="form-control" />
            </div>
            <!--nombre_titular END-->
            <!--nombre_enlace-->
            <div class="form-group col-md-6">
                <label for="nombre_enlace" class="control-label">Nombre Enlace </label>
                <input type='text' id="from" name="nombre_enlace" class="form-control" />
            </div>
            <!--nombre_enlace END-->
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="remitente" class="control-label">Adjunto Convenio</label>
                <input type="file" id="archivo_convenio" name="archivo_convenio" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="status">Estatus</label>
                <select class="form-control" id="status" name="status">
                    <option value="true">Activo</option>
                    <option value="false">Terminado</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="direccion" class="control-label">Dirección</label>
                <textarea name="direccion" class="form-control" id="direccion"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
    <br>
 </div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script>
    $( function() {
      var dateFormat = "mm/dd/yy",
        from = $( "#fecha_firma" )
          .datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1
          })
          .on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
        to = $( "#fecha_termino" ).datepicker({
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
