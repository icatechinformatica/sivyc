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
                    <th scope="col">CLAVE</th>
                    <th scope="col">INSTRUCTOR</th>
                    <th scope="col">CURP</th>
                    <th scope="col">TELEFONO</th>
                    <th scope="col">ESTATUS</th>
                    <th scope="col">FEC.VALIDA</th>
                    <th width="160px" class="text-center">ACCIONES</th>
                    <th>VALIDACIÓN</th>
                    @can('only.admin') <th class="text-center">EXTRA</th> @endcan
                    @can('instructor.validar') <th class="text-center">ACTIVAR</th> @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $itemData)
                    <tr>
                    <th scope="row">{{$itemData->numero_control}}</th>
                        <td>{{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}} {{$itemData->nombre}}</td>
                        <td>{{$itemData->curp}}</td>
                        <td>{{$itemData->telefono}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>
                            @if(date('Y-m-d') >= $itemData->vigencia)
                                <span class="font-weight-bold text-danger">
                            @elseif(date('Y-m-d') > $itemData->por_vencer)
                                <span class="font-weight-bold" style="color:#FF8002">
                            @else
                                <span>
                            @endif
                                {{$itemData->fecha_validacion}}
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($itemData->status == 'EN CAPTURA' || $itemData->status == 'REACTIVACION EN CAPTURA')
                                {{-- @can('instructor.validar')
                                    <a class="btn btn-info" href="{{route('instructor-validar', ['id' => itemData->id])}}">Validar</a>
                                @endcan --}}
                                @if($itemData->numero_control == 'Pendiente')
                                    @can('instructor.create')
                                        <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="CONTINUAR SOLICITUD" href="{{route('instructor-crear-p2', ['id' => $itemData->id])}}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                    @endcan
                                @else
                                    <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="CONTINUAR SOLICITUD" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                @endif
                            @endif
                            @if($itemData->status == 'PREVALIDACION')
                                <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                            @endif
                            @if ($itemData->status == 'RETORNO')
                                @if($itemData->numero_control == 'Pendiente')
                                    @can('instructor.create')
                                        <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MODIFICAR" href="{{route('instructor-crear-p2', ['id' => $itemData->id])}}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                    @endcan
                                @else
                                    <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MODIFICAR" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                @endif
                            @endif
                            @if ($itemData->status == 'EN FIRMA')
                                <a style="color: white;" class="btn mr-sm-4 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $itemData->id])}}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                            @endif
                            @if ($itemData->status == 'Aprobado' || $itemData->status == 'BAJA')
                                    <a style="color: white;" class="btn mr-sm-4 " href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
                            @endif
                            @if ($itemData->status == 'VALIDADO' || $itemData->status == 'BAJA EN PREVALIDACION')
                                    <a style="color: white;" class="btn mr-sm-4 " href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>

                            @endif
                        </td>
                        <td class="text-center">
                            @if ($itemData->status == 'VALIDADO' || $itemData->status == 'BAJA EN PREVALIDACION' || $itemData->status == 'BAJA')
                                @php
                                    $hvalidacion = json_decode($itemData->hvalidacion);
                                    if(!is_null($hvalidacion)) {
                                        $hvalidacion = end($hvalidacion);
                                    }
                                @endphp
                                @if(isset($hvalidacion->arch_val))
                                    <a href="{{$hvalidacion->arch_val}}" target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                                @elseif(!is_null($hvalidacion))
                                    <a href="{{$hvalidacion->arch_baja}}" target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                                @endif

                            @endif
                        </td>
                        @can('only.admin')
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $itemData->id }}" name="estado"   onchange="curso_extra({{$itemData->id}},$(this).prop('checked'),$(this))"  @if($itemData->curso_extra==true){{'checked'}} @endif >
                                </div>
                            </td>
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
        function curso_extra(id, status, obj){
             $.ajax({
                method: "POST",
                url: "cursoExtra",
                data: {
                        id_instructor: id,
                        estado: status
                 }
            })
            .done(function( msg ) { alert(msg); });
        }

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
