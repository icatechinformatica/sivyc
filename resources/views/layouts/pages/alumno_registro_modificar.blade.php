@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Matricular Alumno | Sivyc Icatech')
<!--contenido-->
@section('content_script_css')
    <style>
        .modal
        {
            position: fixed;
            z-index: 999;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            background-color: Black;
            filter: alpha(opacity=60);
            opacity: 0.6;
            -moz-opacity: 0.8;
        }
        .center
        {
            z-index: 1000;
            margin: 300px auto;
            padding: 10px;
            width: 150px;
            background-color: White;
            border-radius: 10px;
            filter: alpha(opacity=100);
            opacity: 1;
            -moz-opacity: 1;
        }
        .center img
        {
            height: 128px;
            width: 128px;
        }
    </style>
@endsection
@section('content')
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

        <div style="text-align: center;">
            <h3><b>MODIFICACIÓN (SID - {{ $alumnos->no_control }})</b></h3>
        </div>

        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS DEL CURSO</b></h4>
        </div>
        
        <form method="POST" id="form_sid_registro" action="{{ route('alumnos-cursos.update', ['idregistrado' => base64_encode($alumnos->id) ]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="especialidad" class="control-label">ESPECIALIDAD A LA QUE ESTA INSCRITO:</label>
                    <select class="form-control" id="especialidad_sid_mod" name="especialidad_sid_mod">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($especialidades as $itemEspecialidad)
                            <option {{ ($alumnos->id_especialidad == $itemEspecialidad->id) ? "selected" : "" }} value="{{$itemEspecialidad->id}}">{{ $itemEspecialidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="horario" class="control-label">TIPO DE CURSO</label>
                    <select class="form-control" id="tipo_curso_mod" name="tipo_curso_mod" required>
                        <option value="">--SELECCIONAR--</option>
                        <option {{ ($alumnos->tipo_curso == "PRESENCIAL") ? "selected" : "" }} value="PRESENCIAL">PRESENCIAL</option>
                        <option {{ ($alumnos->tipo_curso == "A DISTANCIA") ? "selected" : "" }} value="A DISTANCIA">A DISTANCIA</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label for="cursos" class="control-label">CURSO:</label>
                    <select class="form-control" id="curso_sid_mod" name="curso_sid_mod">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($cursos as $itemCursos)
                            <option {{ ($alumnos->id_curso == $itemCursos->id) ? "selected" : "" }} value="{{$itemCursos->id}}">{{ $itemCursos->nombre_curso }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="horario" class="control-label">HORARIO:</label>
                    <input type="text" name="horario_mod" id="horario_mod" value="{{$alumnos->horario}}" class="form-control" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="grupo_mod" class="control-label">GRUPO:</label>
                    <input type="text" name="grupo_mod" id="grupo_mod" value="{{$alumnos->grupo}}" class="form-control" autocomplete="off">
                </div>
            </div>
            <!--botones de enviar y retroceder-->
            <div class="row mt-5">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    @can('alumno.inscrito.update')
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary" >Modificar</button>
                        </div>
                    @endcan
                </div>
            </div>
            <input type="hidden" name="no_control_update" id="no_control_update" value="{{$alumnos->no_control}}">
        </form>
        {{-- modal --}}
            <div class="modal">
                <div class="center">
                    <img alt="" src="{{URL::asset('/img/cargando.gif')}}" />
                </div>
            </div>
        {{-- modal END --}}
        
    </div>
@endsection
{{-- parte de javascript --}}
@section('script_content_js')
    <script type="text/javascript">
        $(function(){
            /***
             * escuchará los cambios del select de especialidades y enviará una petición Ajax para buscar
             * los cursos de esa especialidad
            */
            $('#especialidad_sid_mod').on('change', () => {
                $('#tipo_curso_mod').val('');
            });

            $('#form_sid_registro').validate({
                rules: {
                    especialidad_sid_mod: {
                        required: true
                    },
                    tipo_curso_mod: {
                        required: true
                    },
                    curso_sid_mod: {
                        required: true
                    }
                },
                messages: {
                    especialidad_sid_mod: {
                        required: 'Campo requerido'
                    },
                    tipo_curso_mod: {
                        required: 'Campo requerido'
                    },
                    curso_sid_mod: {
                        required: 'Campo requerido'
                    }
                }
            });

            $('#tipo_curso_mod').on("change", () => {
                $("#especialidad_sid_mod option:selected").each( () => {
                    var IdEsp = $('#especialidad_sid_mod').val();
                    var tipo = $('#tipo_curso_mod').val();
                    var unidad = $('#tblunidades').val();
                    var datos = { idEsp_mod: IdEsp, tipo_mod: tipo, _token: "{{ csrf_token() }}"};
                    var url = "{{route('alumnos.sid.cursos.modificado')}}";

                    var solicitud = $.ajax({
                        url: url,
                        method: 'POST',
                        data: datos,
                        dataType: 'json',
                        beforeSend: function(){
                            $(".modal").show();
                        },
                        success: function(response){
                            /*
                                *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                                *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                                *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                                *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
                            */
                            if (response.length < 1) {
                                $("#curso_sid_mod").empty();
                                $("#curso_sid_mod").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                            } else {
                                if(!response.hasOwnProperty('error')){
                                    $("#curso_sid_mod").empty();
                                    $("#curso_sid_mod").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                    $.each(response, (k ,v) => {
                                        $('#curso_sid_mod').append('<option value="' + v.id + '">' + v.nombre_curso + '</option>');
                                    });
                                    $("#curso_sid_mod").focus();
                                }
                            }
                        },
                        complete:function(data){
                            // escondemos el modal
                            $(".modal").hide();
                        },
                        error: function(jqXHR, textStatus){
                            jsonValue = jQuery.parseJSON( jqXHR.responseText );
                            console.log(jqXHR.status);
                            alert( "Hubo un error: " + jsonValue );
                        }
                    });

                    $.when(solicitud).then(function(data, textStatus, jqXHR ){
                        if (jqXHR.status === 200) {
                            $(".modal").hide();
                        }
                    });
                });
            });
        });
    </script>
@endsection
{{-- parte de javascript END --}}

