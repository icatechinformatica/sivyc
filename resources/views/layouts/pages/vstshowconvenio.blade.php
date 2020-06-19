@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
 <!--empieza aquí-->

 <div class="container g-pt-50">
        <div style="text-align: center">
            <label for="tituloagregar_convenio"><h1>DETALLE DEL CONVENIO </h1></label>
         </div>
         <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="no_convenio" class="control-label">N° CONVENIO</label><br>
                <b>{{$convenios[0]->no_convenio}}</b>
            </div>
            <!-- Organismo -->
            <div class="form-group col-md-6">
                <label for="institucion" class="control-label">INSTITUCIÓN</label><br>
                <b>{{$convenios[0]->institucion}}</b>
            </div>
            <!--Organismo Fin-->
        </div>
        <div class="form-row">
            <!-- Telefono -->
            <div class="form-group col-md-4">
                <label for="telefono" class="control-label">TELÉFONO</label><br>
                <b>{{$convenios[0]->telefono}}</b>
            </div>
            <!--Telefono Fin-->
            <!-- Tipo de sector -->
            <div class="form-group col-md-4">
                <label for="sector">TIPO SECTOR</label><br>
                <b>{{ $convenios[0]->tipo_sector }}</b>
            </div>
            <!-- Fin Sector-->
            <div class="form-group col-md-4">
                <label for="status">ESTATUS</label><br>
                @if (trim($convenios[0]->status) == "true")
                    <b>ACTIVO</b>
                @else
                    <b>TERMINADO</b>
                @endif

            </div>
        </div>
        <div class="form-row">
            <!-- fecha inicial -->
            <div class="form-group col-md-4">
                <label for="fecha_firma" class="control-label">FECHA DE LA FIRMA</label><br>
                <b>{{$fecha_firma}}</b>
            </div>
            <!--Fecha inicial END-->
            <!-- Fecha conclusion -->
            <div class="form-group col-md-4">
                <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label><br>
                <b>{{ $fecha_vigencia }}</b>
            </div>
            <!-- Fecha conclusion END-->
            <!--poblacion-->
            <div class="form-group col-md-4">
                <label for="poblacion" class="control-label">POBLACIÓN</label><br>
                <b>{{$convenios[0]->poblacion}}</b>
            </div>
            <!--poblacion END-->
        </div>

        <div class="form-row">
            <!--municipio-->
            <div class="form-group col-md-4">
                <label for="municipio" class="control-label">MUNICIPIO</label><br>
                <b>{{$convenios[0]->municipio}}</b>
            </div>
            <!--municipio END-->
            <!--nombre_titular-->
            <div class="form-group col-md-4">
                <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label><br>
                <b>{{$convenios[0]->nombre_titular}}</b>
            </div>
            <!--nombre_titular END-->
            <!--nombre_enlace-->
            <div class="form-group col-md-4">
                <label for="nombre_enlace" class="control-label">NOMBRE DEL ENLACE</label><br>
                <b>{{$convenios[0]->nombre_enlace}}</b>
            </div>
            <!--nombre_enlace END-->
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="status">ARCHIVO DE CONVENIO</label><br>
                <div class="custom-file">
                    @if (isset($convenios[0]->archivo_convenio))
                        <a href="{{ $convenios[0]->archivo_convenio }}" download="convenio_{{ $convenios[0]->no_convenio }}.pdf" rel="{{ $convenios[0]->archivo_convenio }}">
                            <img src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="50px" height="50px">
                        </a>
                    @endif
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="direccion" class="control-label">DIRECCIÓN</label><br>
                <b>{{$convenios[0]->direccion}}</b>
            </div>
        </div>
        <!--botones de enviar y retroceder-->
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
            </div>
        </div>
    <br>
 </div>
@stop
