<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'UNIDADES MOVILES | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif
        <div class="row">

            <div class="col-xl-12 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">UNIDADES MOVILES</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" id="form_sid_registro" action="{{ route('registrado_consecutivo.index') }}">
                        @csrf
                        <div class="form-row">
                            <!-- domicilio -->
                            <div class="form-group col-md-6">
                                <label for="ubicaciones" class="control-label">UNIDADES</label>
                                <select class="form-control" id="ubicaciones" name="ubicaciones" required>
                                    <option value="">--SELECCIONAR--</option>
                                    @foreach ($tblUnidades as $itemTblUnidades)
                                        <option value="{{$itemTblUnidades->ubicacion}}">{{$itemTblUnidades->ubicacion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- domicilio END -->
                            <div class="form-group col-md-6">
                                <label for="unidades_ubicacion" class="control-label">ACCIÓN MÓVIL</label>
                                <select class="form-control" id="unidades_ubicacion" name="unidades_ubicacion" required>
                                    <option value="">--SELECCIONAR--</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">CARGAR</button>
                    </form>
                  </div>
                </div>
            </div>
        </div>

        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc_admin.footer")
        <!-- FOOTER PORTAL DE GOBIERNO END-->
    </div>
@stop
@section('scripts_content')
    <!--scripts-->
    <script type="text/javascript">
        $(function(){
            /**
            * cambios select dependientes de tbl_unidades
            */
            $('#ubicaciones').on("change", () => {
                console.log('listos');
                $("#ubicaciones option:selected").each( () => {
                    var ubicacion = $('#ubicaciones').val();
                    var url = '/unidades/unidad_by_ubicacion/'+ ubicacion;

                    var request = $.ajax
                    ({
                        url: url,
                        method: 'GET',
                        dataType: 'json'
                    });

                    /*
                        *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                        *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                        *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                        *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
                    */

                    request.done(( respuesta ) => {
                        if (respuesta.length < 1) {
                            $("#unidades_ubicacion").empty();
                            $("#unidades_ubicacion").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#unidades_ubicacion").empty();
                                $("#unidades_ubicacion").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#unidades_ubicacion').append('<option value="' + v.unidad + '">' + v.unidad + '</option>');
                                });
                                $("#unidades_ubicacion").focus();
                            }else{

                                //Puedes mostrar un mensaje de error en algún div del DOM
                            }
                        }
                    });

                    request.fail(( jqXHR, textStatus ) =>
                    {
                            alert( "Hubo un error: " + textStatus );
                    });
                });
            });
        });
    </script>
@stop
