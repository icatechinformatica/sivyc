<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Instructor | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" /> 
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.0.1/css/bootstrap.css') }}"> 
    <style>
        .form-check-input{
            width:22px;
            height:22px;
        }        
    </style>   
@endsection
<!--seccion-->
@section('content')     
    <div class="card-header">
        <h3>Registro de Instructores</h3>
    </div>
    <div class="card card-body">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    {!! Form::open(['route' => 'instructor-inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipo_busqueda_instructor" class="form-control mr-sm-2" id="tipo_busqueda_instructor">
                            <option value="">BUSCAR POR TIPO</option>                            
                            <option value="clave_instructor">CLAVE</option>
                            <option value="nombre_instructor">NOMBRE</option>
                            <option value="curp">CURP</option>
                            <option value="telefono_instructor">TELÉFONO</option>
                            <option value="estatus_instructor">ESTATUS</option>
                            <option value="especialidad">ESPECIALIDAD</option>
                        </select>
                        <Div id="divcampo" name="divcampo">
                            {!! Form::text('busquedaPorInstructor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                        </Div>
                        <Div id="divstat" name="divstat" class="d-none d-print-none">
                            <select name="tipo_status" class="form-control mr-sm-2" id="tipo_status">
                                <option value="">BUSQUEDA POR STATUS</option>
                                <option value="EN CAPTURA">EN CAPTURA</option>
                                <option value="PREVALIDACION">PREVALIDACION</option>
                                <option value="EN FIRMA">EN FIRMA</option>
                                <option value="VALIDADO">VALIDADO</option>
                                <option value="RETORNO">RETORNO</option>
                            </select>
                        </Div>
                        <Div id="divespecialidad" name="divespecialidad" class="d-none d-print-none">
                            <select name="tipo_especialidad" class="form-control mr-sm-2" id="tipo_especialidad">
                                <option value="">BUSQUEDA POR ESPECIALIDAD</option>
                                @foreach ($especialidades as $moist)
                                    <option value="{{$moist->id}}">{{$moist->nombre}}</option>
                                @endforeach
                            </select>
                        </Div>
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}

                </div>
                <br>
                <div class="pull-right">
                    @can('instructor.create')
                        <a class="btn mr-sm-4 mt-3 btn-lg" href="{{route('instructor-crear')}}"> Nuevo</a>
                    @endcan
                    @can('academico.exportar.instructores')
                        <a class="btn mr-sm-4 mt-3 btn-info btn-lg" href="{{route('academico.exportar.instructores')}}">Exportar Instructores</a>
                        <a class="btn mr-sm-4 mt-3 btn-info btn-lg" href="{{route('academico.exportar.instructores.activos')}}">Exportar Activos</a>
                    @endcan
                </div>
            </div>
        </div>        
        <table  id="table-instructor" class="table table-bordered table-responsive-md">
            <caption>Catalogo de Instructrores</caption>
            <thead>
                <tr>
                    <th scope="col">CLAVE INSTRUCTOR</th>
                    <th scope="col">INSTRUCTOR</th>
                    <th scope="col">CURP</th>
                    <th scope="col">TELEFONO</th>
                    <th scope="col">ESTATUS</th>
                    <th width="160px">ACCIONES</th>
                    <th>VALIDACIÓN</th>
                    @can('only.admin') <th class="text-center">CURSO EXTRA</th> @endcan
                    @can('instructor.validar') <th class="text-center">ACTIVAR</th> @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->numero_control}}</th>
                        <td>{{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}} {{$itemData->nombre}}</td>
                        <td>{{$itemData->curp}}</td>
                        <td>{{$itemData->telefono}}</td>
                        <td>{{$itemData->status}} {{$itemData->fecha_validacion}}</td>
                        <td class="text-center">
                            @if ($itemData->status == 'EN CAPTURA' || $itemData->status == 'REACTIVACION EN CAPTURA')
                                {{-- @can('instructor.validar')
                                    <a class="btn btn-info" href="{{route('instructor-validar', ['id' => itemData->id])}}">Validar</a>
                                @endcan --}}
                                @if($itemData->numero_control == 'Pendiente')
                                    @can('instructor.create')
                                        <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="CONTINUAR SOLICITUD" href="{{route('instructor-crear-p2', ['id' => $itemData->id])}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                    @endcan
                                @else
                                    <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="CONTINUAR SOLICITUD" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                @endif
                            @endif
                            @if($itemData->status == 'PREVALIDACION')
                                <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            @endif
                            @if ($itemData->status == 'RETORNO')
                                @if($itemData->numero_control == 'Pendiente')
                                    @can('instructor.create')
                                        <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MODIFICAR" href="{{route('instructor-crear-p2', ['id' => $itemData->id])}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                    @endcan
                                @else
                                    <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MODIFICAR" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                @endif
                            @endif
                            @if ($itemData->status == 'EN FIRMA')
                                <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            @endif
                            @if ($itemData->status == 'Aprobado' || $itemData->status == 'BAJA')
                                    <a style="color: white;" class="btn mr-sm-4 " href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
                            @endif
                            @if ($itemData->status == 'VALIDADO' || $itemData->status == 'BAJA EN PREVALIDACION')
                                    <a style="color: white;" class="btn mr-sm-4 " href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>                                    

                            @endif
                        </td>
                        <td class="text-center">
                            @if ($itemData->status == 'VALIDADO' || $itemData->status == 'BAJA EN PREVALIDACION')                                    
                                    @if($itemData->archivo_alta == NULL)
                                        <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger from-control"></i>
                                    @else
                                        <a href="{{$itemData->archivo_alta}}" target="_blank"><i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger from-control"></i></a>
                                    @endif

                            @endif
                        </td>
                        @can('only.admin')
                            <td class="text-center">
                                <button type="button" class="btn mr-sm-4"
                                    id = 'curso_ext'
                                    data-toggle="modal"
                                    data-placement="top"
                                    data-target="#cursoExtraModal"
                                    data-id='["{{$itemData->curso_extra ? "true" : "false"}}","{{$itemData->id}}"]'><small>Curso Extra</small>
                                </button>
                            </td>
                            {{-- @php dd($itemData->curso_extra); @endphp --}}
                        @endcan
                        @can('instructor.validar')
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $itemData->id }}" name="estado"   onchange="cambia_estado({{$itemData->id}},$(this).prop('checked'),$(this))"  @if($itemData->estado==true){{'checked'}} @endif >                                
                                </div>                                        
                            </td>                            
                        @endcan
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        {{ $data->appends(request()->query())->links() }}
                    </td>
                </tr>
            </tfoot>
        </table>
        <br>
    </div>
    <br>
    <!-- Modal -->
    <div class="modal fade" id="cursoExtraModal" role="dialog">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('mod-curso-extra') }}" id="mod_curso_extra">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">¿Esta seguro de ir al siguiente paso?<b></b></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="form-row">
                    <div class="form-group col-md-3"></div>
                        <div class="form-group col-md-6">
                            <label for="unidad" class="control-label">Curso Extra Actualmente</label>
                            <select class="form-control" name="extra" id="extra">
                                <option value="false">Inactivo</option>
                                <option value="true">Activo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2"></div>
                        <div class="form-group col-md-4">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                        <input name="id_instructor_cursoext" id="id_instructor_cursoext" hidden>
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                            {{-- <a id="valsupre_confirm" href="#"  >Confirmar</a> --}}
                        </div>
                        <div class="form-group col-md-1"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<!-- END -->
    <!-- Modal -->
        <div class="modal fade" id="confirmsaveins" role="dialog">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('folio-permiso-mod') }}" id="mod_folio">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">CURSO EXTRA<b></b></h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="form-row">
                        <div class="form-group col-md-3"></div>
                            {{-- <div class="form-group col-md-6">
                                <label for="unidad" class="control-label">Esto Regresara la Validación Suficiencia Presupuestal a planeación para su correción</label>
                            </div> --}}
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2"></div>
                            <div class="form-group col-md-4">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                            <div class="form-group col-md-4">
                                <a id="valsupre_confirm" href="#" class="btn btn-primary" >Confirmar</a>
                            </div>
                            <div class="form-group col-md-1"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <!-- END -->   
@endsection
@section('script_content_js')
    <script>
        $(function(){
            //metodo
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            document.getElementById('tipo_busqueda_instructor').onchange = function() {
                var index = this.selectedIndex;
                var inputText = this.children[index].innerHTML.trim();
                console.log(inputText);
                if(inputText == 'ESTATUS')
                {
                    $('#divstat').prop("class", "")
                    $('#divcampo').prop("class", "form-row d-none d-print-none")
                    $('#divespecialidad').prop("class", "form-row d-none d-print-none")
                }
                else if(inputText == 'ESPECIALIDAD')
                {
                    $('#divespecialidad').prop("class", "")
                    $('#divcampo').prop("class", "form-row d-none d-print-none")
                    $('#divstat').prop("class", "form-row d-none d-print-none")
                }
                else
                {
                    $('#divstat').prop("class", "form-row d-none d-print-none")
                    $('#divespecialidad').prop("class", "form-row d-none d-print-none")
                    $('#divcampo').prop("class", "")
                }
            }
        });
    </script>
    <script>
        $('#cursoExtraModal').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            var selectElement = document.getElementById('extra');
            selectElement.value = id['0'];
            document.getElementById('id_instructor_cursoext').value = id['1'];
            // document.getElementById('loc2del').value = id['1'];
        });
        
    </script>
    <script>
        function cambia_estado(id, status, obj){         
            if (confirm("Está seguro de realizar el cambio?") == true) {
                $.ajax({
                    method: "POST", 
                    url: "estado", 
                    data: { 
                        id_instructor: id,
                        estado: status
                    }
                })
                .done(function( msg ) { alert(msg); }); 
            }else{                
                if(obj.prop('checked')) obj.prop('checked', false); 
                else obj.prop('checked', true); 
            }
        }
    </script>   
@endsection
