@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
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
            <label for="tituloagregar_convenio"><h1>EDITAR CONVENIO</h1></label>
         </div>
         <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="no_convenio" class="control-label">N° CONVENIO</label>
                <input type="text" class="form-control" id="no_convenio" name="no_convenio" placeholder="N° CONVENIO" value="{{$convenios->no_convenio}}">
            </div>
            <!-- Organismo -->
            <div class="form-group col-md-6">
                <label for="institucion" class="control-label">INSTITUCIÓN</label>
                <input type="text" class="form-control" id="institucion" name="institucion" placeholder="Institución" value="{{$convenios->institucion}}">
            </div>
            <!--Organismo Fin-->
        </div>
        <div class="form-row">
            <!-- Telefono -->
            <div class="form-group col-md-4">
                <label for="telefono" class="control-label">TELÉFONO</label>
                <input type="text" class="form-control" onkeypress="return solonumeros(event)" id="telefono" name="telefono" placeholder="telefono" value="{{$convenios->telefono}}">
            </div>
            <!--Telefono Fin-->
            <!-- Tipo de sector -->
            <div class="form-group col-md-4">
                <label for="sector">TIPO SECTOR</label>
                <select class="form-control" id="tipo" name="tipo">
                    <option value="">--SELECCIONAR--</option>
                    <option {{ trim($convenios->tipo_sector) == "S" ? "selected" : "" }} value="S">S</option>
                    <option {{ trim($convenios->tipo_sector) == "E" ? "selected" : "" }} value="E">E</option>
                    <option {{ trim($convenios->tipo_sector) == "G" ? "selected" : "" }} value="G">G</option>
                </select>
            </div>
            <!-- Fin Sector-->
            <div class="form-group col-md-4">
                <label for="status">ESTATUS</label>
                <select class="form-control" id="status" name="status">
                    <option value="">--SELECCIONAR--</option>
                    <option {{ trim($convenios->status) == "true" ? "selected" : "" }} value="true">ACTIVO</option>
                    <option {{ trim($convenios->status) == "false" ? "selected" : "" }} value="false">TERMINADO</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <!-- fecha inicial -->
            <div class="form-group col-md-4">
                <label for="fecha_firma" class="control-label">FECHA DE LA FIRMA</label>
                <input type='text' id="fecha_firma" autocomplete="off" readonly="readonly" name="fecha_firma" value="{{$convenios->fecha_firma}}" class="form-control datepicker" />
            </div>
            <!--Fecha inicial END-->
            <!-- Fecha conclusion -->
            <div class="form-group col-md-4">
                <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label>
                <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly" name="fecha_termino" value="{{$convenios->fecha_vigencia}}" class="form-control datepicker" />
            </div>
            <!-- Fecha conclusion END-->
            <!--poblacion-->
            <div class="form-group col-md-4">
                <label for="poblacion" class="control-label">POBLACIÓN</label>
                <input type='text' id="poblacion" name="poblacion" class="form-control" value="{{$convenios->poblacion}}"/>
            </div>
            <!--poblacion END-->
        </div>

        <div class="form-row">
            <!--municipio-->
            <div class="form-group col-md-4">
                <label for="municipio" class="control-label">MUNICIPIO</label>
                <input type='text' id="poblacion" name="poblacion" class="form-control" value="{{$convenios->municipio}}"/>
            </div>
            <!--municipio END-->
            <!--nombre_titular-->
            <div class="form-group col-md-4">
                <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label>
                <input type='text' id="nombre_titular" name="nombre_titular" class="form-control" value="{{$convenios->nombre_titular}}"/>
            </div>
            <!--nombre_titular END-->
            <!--nombre_enlace-->
            <div class="form-group col-md-4">
                <label for="nombre_enlace" class="control-label">NOMBRE DEL ENLACE</label>
                <input type='text' id="nombre_enlace" name="nombre_enlace" class="form-control" value="{{$convenios->nombre_enlace}}"/>
            </div>
            <!--nombre_enlace END-->
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="status">ARCHIVO DE CONVENIO</label>
                <div class="custom-file">
                    <input type="file" id="archivo_convenio" name="archivo_convenio" accept="application/pdf" class="custom-file-input">
                    <label for="archivo_convenio" class="custom-file-label">ARCHIVO CONVENIO</label>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="direccion" class="control-label">DIRECCIÓN</label>
                <textarea name="direccion" class="form-control" id="direccion">{{$convenios->direccion}}</textarea>
            </div>
        </div>
        <!--botones de enviar y retroceder-->
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" >Guardar</button>
                </div>
            </div>
        </div>
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
