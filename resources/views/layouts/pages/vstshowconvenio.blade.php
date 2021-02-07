@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
    <!--empieza aquí-->

    <div class="container g-pt-30">

        <div style="text-align: center">
            <label for="tituloagregar_convenio">
                <h1>DETALLE DEL CONVENIO </h1>
            </label>
        </div>

        <hr>

        <div class="form-row">
            <div class="form-group col">
                <label for="no_convenio" class="control-label">N° CONVENIO</label><br>
                <strong class="ml-5">{{ $convenios[0]->no_convenio }}</strong>
            </div>
            <!-- intitucion -->
            <div class="form-group col">
                <label for="institucion" class="control-label">INSTITUCIÓN</label><br>
                <strong class="ml-5">{{ $convenios[0]->institucion }}</strong>
            </div>
        </div>

        <div class="form-row">
            <!--nombre_titular-->
            <div class="form-group col">
                <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label><br>
                <strong class="ml-5">{{ $convenios[0]->nombre_titular }}</strong>
            </div>
            <!-- Telefono -->
            <div class="form-group col">
                <label for="telefono" class="control-label">TELÉFONO</label><br>
                <strong class="ml-5">{{ $convenios[0]->telefono }}</strong>
            </div>
        </div>

        <div class="form-row">
            {{-- direccion --}}
            <div class="form-group col">
                <label for="direccion" class="control-label">DIRECCIÓN</label><br>
                <div class="col ml-4">
                    <strong>{{ $convenios[0]->direccion }}</strong>
                </div>
            </div>
            <!--poblacion-->
            <div class="form-group col">
                <label for="poblacion" class="control-label">POBLACIÓN</label><br>
                <strong class="ml-5">{{ $convenios[0]->poblacion }}</strong>
            </div>
            <!--municipio-->
            <div class="form-group col">
                <label for="municipio" class="control-label">MUNICIPIO</label><br>
                <strong class="ml-5">{{ $convenios[0]->muni }}</strong>
            </div>
        </div>

        <hr>

        <div class="form-row">
            <!--tipo de convenio-->
            <div class="form-group col">
                <label for="tipo_convenio" class="control-label">TIPO DE CONVENIO</label><br>
                <strong
                    class="ml-5">{{ $convenios[0]->tipo_convenio != null ? $convenios[0]->tipo_convenio : 'NO DEFINIDO' }}</strong>
            </div>
            <!--nombre de firma -->
            <div class="form-group col">
                <label for="nombre_firma" class="control-label">NOMBRE DE FIRMA</label><br>
                <strong
                    class="ml-5">{{ $convenios[0]->nombre_firma != null ? $convenios[0]->nombre_firma : 'NO DEFINIDO' }}</strong>
            </div>
        </div>

        <div class="form-row">
            <!-- fecha inicial -->
            <div class="form-group col">
                <label for="fecha_firma" class="control-label">FECHA DE LA FIRMA</label><br>
                {{-- <strong class="ml-5">{{ $fecha_firma }}</strong> --}}
            </div>
            <!-- Fecha conclusion -->
            <div class="form-group col">
                <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label><br>
                {{-- <strong class="ml-5">{{ $fecha_vigencia }}</strong> --}}
            </div>
        </div>

        <div class="form-row">
            <!--nombre_enlace-->
            <div class="form-group col">
                <label for="nombre_enlace" class="control-label">NOMBRE DEL ENLACE</label><br>
                <strong class="ml-5">{{ $convenios[0]->nombre_enlace }}</strong>
            </div>
            <!--telefono_enlace-->
            <div class="form-group col">
                <label for="telefono_enlace" class="control-label">TELEFONO DEL ENLACE</label><br>
                <strong
                    class="ml-5">{{ $convenios[0]->telefono_enlace != null ? $convenios[0]->telefono_enlace : 'NO DEFINIDO' }}</strong>
            </div>
        </div>

        <hr>

        <div class="form-row">
            {{-- archivo convenio --}}
            <div class="form-group col">
                <label for="status">ARCHIVO DE CONVENIO</label><br>
                <div class="custom-file">
                    @if (isset($convenios[0]->archivo_convenio))
                        <a href="{{ $convenios[0]->archivo_convenio }}" target="_blank"
                            rel="{{ $convenios[0]->archivo_convenio }}">
                            <img src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="50px"
                                height="50px">
                        </a>
                    @else
                        <strong class="ml-5">NO ADJUNTADO</strong>
                    @endif
                </div>
            </div>
            <!-- Tipo de sector -->
            <div class="form-group col">
                <label for="sector">TIPO SECTOR</label><br>
                <strong class="ml-5">{{ $convenios[0]->tipo_sector }}</strong>
            </div>
            {{-- status --}}
            <div class="form-group col">
                <label for="status">ESTATUS</label><br>
                @if (trim($convenios[0]->status) == 'true')
                    <strong class="ml-5">ACTIVO</strong>
                @else
                    <strong class="ml-5">TERMINADO</strong>
                @endif

            </div>
        </div>

        <div class="form-row">
            <!-- Publicado -->
            <div class="form-group col">
                <label for="sector">Publicado</label><br>
                <strong class="ml-5">{{ $convenios[0]->activo == 'true' ? 'SI' : 'NO' }}</strong>
            </div>
        </div>

        <!--botones de enviar y retroceder-->
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{ URL::previous() }}">Regresar</a>
                </div>
            </div>
        </div>
        <br>
    </div>
@stop
