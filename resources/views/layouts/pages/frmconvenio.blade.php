@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('title', 'Formulario de Convenio | Sivyc Icatech')

@section('content')

    <div class="container g-pt-10">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <br />
        @endif

        <form method="POST" action="{{ route('convenios.store') }}" id="frmConvenio" enctype="multipart/form-data"
            autocomplete="off">
            @csrf

            {{-- titulo --}}
            <div style="text-align: center">
                <label for="tituloagregar_convenio">
                    <h1>NUEVO CONVENIO</h1>
                </label>
            </div>

            <div class="form-row">
                {{-- no convenio --}}
                <div class="form-group col-md-6">
                    <label for="no_convenio" class="control-label">N° CONVENIO</label>
                    <input type="text" class="form-control" id="no_convenio" name="no_convenio" placeholder="N° Convenio">
                </div>
                <!-- Organismo -->
                <div class="form-group col-md-6">
                    <label for="institucion" class="control-label">INSTITUCIÓN</label>
                    <input type="text" class="form-control" id="institucion" name="institucion" placeholder="Institución">
                </div>
            </div>
            <div class="form-row">
                <!--nombre_titular-->
                <div class="form-group col">
                    <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label>
                    <input type='text' id="nombre_titular" name="nombre_titular" class="form-control"
                        placeholder="nombre del titular">
                </div>
                <!-- Telefono -->
                <div class="form-group col">
                    <label for="telefono" class="control-label">TELÉFONO</label>
                    <input type="text" class="form-control" onkeypress="return solonumeros(event)" id="telefono"
                        name="telefono" placeholder="telefono">
                </div>
            </div>
            <div class="form-row">
                {{-- direccion --}}
                <div class="form-group col">
                    <label for="direccion" class="control-label">DIRECCIÓN</label>
                    <input type="text" class="form-control" name="direccion" id="direccion" placeholder="dirección">
                </div>
                <!--municipio-->
                <div class="form-group col-md-4">
                    <label for="area" class="control-label">MUNICIPIO</label>
                    <select name="municipio" id="municipio" class="custom-select">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->id }}">{{ $municipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
                <!--localidad-->
                <div class="form-group col">
                    <label for="poblacion" class="control-label">LOCALIDAD</label>
                    <input type='text' id="poblacion" name="poblacion" class="form-control">
                </div>
            </div>

            <hr>

            <div class="form-row">
                <!--tipo convenio-->
                <div class="form-group col">
                    <label for="tipo_convenio" class="control-label">TIPO DE CONVENIO</label>
                    <select name="tipo_convenio" id="tipo_convenio" class="custom-select">
                        <option value="">--SELECCIONAR--</option>
                        <option value="GENERAL">GENERAL</option>
                        <option value="ESPECIFICO">ESPECIFICO</option>
                    </select>
                </div>
                <!-- NOMBRE DE FIRMA -->
                <div class="form-group col">
                    <label for="nombre_firma" class="control-label">NOMBRE DE FIRMA</label>
                    <input type='text' id="nombre_firma" name="nombre_firma" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <!-- fecha inicial -->
                <div class="form-group col">
                    <label for="fecha_firma" class="control-label">FECHA DE LA FIRMA</label>
                    <input type='text' id="fecha_firma" autocomplete="off" readonly="readonly" name="fecha_firma"
                        class="form-control datepicker">
                </div>
                <!-- Fecha conclusion -->
                <div class="form-group col">
                    <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label>
                    <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly" name="fecha_termino"
                        class="form-control datepicker">
                </div>
            </div>
            <div class="form-row">
                <!--nombre_enlace-->
                <div class="form-group col">
                    <label for="nombre_enlace" class="control-label">NOMBRE DEL ENLACE</label>
                    <input type='text' id="nombre_enlace" name="nombre_enlace" class="form-control">
                </div>

                <!--telefono del enlace-->
                <div class="form-group col">
                    <label for="telefono_enlace" class="control-label">TELEFONO DEL ENLACE</label>
                    <input type='text' id="telefono_enlace" name="telefono_enlace" class="form-control">
                </div>
            </div>
            <!-- Fecha conclusion END-->
            <!--poblacion-->
            <div class="form-group col-md-4">
                <label for="poblacion" class="control-label">POBLACIÓN</label>
                <input type='text' id="poblacion" name="poblacion" class="form-control" />
            </div>
            <!--poblacion END-->
        </div>

        <div class="form-row">
            <!--municipio-->
            <div class="form-group col-md-4">
                <label for="municipio" class="control-label">MUNICIPIO</label>
                <input type='text' id="municipio" name="municipio" class="form-control" />
            </div>
            <!--municipio END-->
            <!--nombre_titular-->
            <div class="form-group col-md-4">
                <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label>
                <input type='text' id="nombre_titular" name="nombre_titular" class="form-control" />
            </div>
            <!--nombre_titular END-->
            <!--nombre_enlace-->
            <div class="form-group col-md-4">
                <label for="nombre_enlace" class="control-label">NOMBRE DEL ENLACE</label>
                <input type='text' id="nombre_enlace" name="nombre_enlace" class="form-control" />
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
                <textarea name="direccion" class="form-control" id="direccion"></textarea>
            </div>
        </div>
        <!--botones de enviar y retroceder-->
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
                @can('convenios.store')
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                @endcan
            </div>
        </div>
    </form>
    <br>
 </div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script>
    $( function() {
      var dateFormat = "dd-mm-yy",
        from = $( "#fecha_firma" )
          .datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'dd-mm-yy'
          })
          .on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
        to = $( "#fecha_termino" ).datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          dateFormat: 'dd-mm-yy'
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
