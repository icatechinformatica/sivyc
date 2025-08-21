<!--ELABORO ORLANDO CHAVEZ - orlando@sidmac.com.com-->
@extends('theme.sivyc.layout')
@section('title', 'Prevalidacion | SIVyC Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}
<div class="card-header">
    Prevalidacion de Instructor
</div>
<div class="card card-body" style=" min-height:450px;">
    @if (Session::has('success'))
        <div class="alert alert-info alert-block">
            <strong>{{ Session::get('success') }}</strong>
        </div>
    @endif
    {{ Form::open(['route' => 'prevalidar-ins', 'method' => 'GET', 'id'=>'frm']) }}
        @csrf
        <div class="form-row">
            @if(isset($rol))
                <div class="form-group col-md-2">
                    <select name="seluni" class="form-control mr-sm-2" id="seluni" onchange="local()">
                        <option value="">UNIDAD</option>
                        @foreach($unidades AS $unidad)
                            <option value="{{$unidad->unidad}}" @if($unidad->unidad == $seluni) selected @endif>{{$unidad->unidad}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            {{-- <div class="form-group col-md-2">
                    <select name="valor" class="form-control mr-sm-2" id="valor">
                        @if(isset($nrevisiones))
                            @foreach($nrevisiones AS $nrevision)
                                <option value="{{$nrevision->nrevision}}" @if($nrevision->nrevision == $valor) selected @endif>{{$nrevision->nrevision}}</option>
                            @endforeach
                        @else
                            <option value="{{$valor}}">{{$valor}}</option>
                        @endif
                    </select>
            </div> --}}
            <div class="form-group col-md-1">
                <button type="submit" class="btn">BUSCAR</button>
            </div>
            @if(isset($data) && $data->turnado == 'DTA' && ($data->status == 'EN FIRMA' || $data->status == 'BAJA EN FIRMA' || $data->status == 'REACTIVACION EN FIRMA'))
                <div class="form-group col-md-3">
                    <a target="_blank" class="btn mr-sm-4" href="{{$arch_sol}}">MEMORANDUM DE SOLICITUD</a>
                </div>
            @endif
        </div>
    {!! Form::close() !!}
    @if ($message)
        <div class="row ">
            <div class="col-md-12 alert alert-danger">
                <p>{{ $message }}</p>
            </div>
        </div>
    @endif
    @if(isset($data))
        @php $bajaesp = $bajaborrador = FALSE;
            foreach ($especialidades AS $moist)
            {
                if ($moist->status == 'BAJA EN FIRMA')
                {
                    $bajaesp = TRUE;
                }
                // if ($moist->status == 'BAJA EN PREVALIDACION')
                // {
                //     $bajaborrador = TRUE;
                // }
            }
        @endphp
        <form @if($data->status == 'BAJA EN FIRMA' || $data->status == 'BAJA EN PREVALIDACION' || $bajaborrador == TRUE) action="{{ route('instructor-baja-solicitud-pdf') }}" @else action="{{ route('instructor-solicitud-pdf') }}" @endif method="post" target="_blank" >
            @csrf
            <div class="form-row">
                <div class="form-group col-md-3 text-center">
                    <h5><b>MOVIMIENTOS A PREVALIDAR</b></h5>
                </div>
                @foreach($especialidades as $aztral)
                    @if($aztral->status == 'EN FIRMA' || $aztral->status == 'REVALIDACION EN FIRMA' || $aztral->status == 'BAJA EN FIRMA' || $aztral->status == 'REACTIVACION EN FIRMA' || ($regimen_actual != $data->tipo_honorario && $data->status == 'EN FIRMA'))
                        @can('instructor.editar_fase2')
                            <div class="form-group col-md-3"></div>
                            <div class="form-group col-md-3">
                                <input type="text" name="nomemo" id="nomemo" class="form-control" placeholder="NUMERO DE MEMORANDUM" @if($aztral->status != 'BAJA EN FIRMA' && $aztral->status != 'REACTIVACION EN FIRMA') value="{{$aztral->memorandum_solicitud}}" @endif required>
                            </div>
                            <div class="form-group col-md-3">
                                <input value="{{$data->id_oficial}}" hidden id="idins" name="idins">
                                <button type="submit" class="btn mr-sm-4 form-row" style="color: white;">GENERAR MEMORANDUM</button>
                            </div>
                        @endcan
                        @can('instructor.prevalidar')
                            <div class="form-group col-md-4"></div>
                            <div class="form-group col-md-2">
                                {{-- <input type="text" name="nomemoval" id="nomemo" class="form-control" placeholder="NUMERO DE MEMORANDUM" value="{{$data[0]->memorandum_solicitud}}" required> --}}
                            </div>
                            <div class="form-group col-md-3">
                                <input value="{{$data->id}}" hidden id="idins" name="idins">
                                @if($data->status == 'BAJA EN FIRMA')
                                    <button type="button" class="btn mr-sm-4"
                                        data-toggle="modal"
                                        data-placement="top"
                                        data-target="#generateddocbajaModal"
                                        data-id='{{$id_list}}'>
                                            GENERAR VALIDACIÓN
                                    </button>
                                @else
                                    <button type="button" class="btn mr-sm-4"
                                        data-toggle="modal"
                                        data-placement="top"
                                        data-target="#generateddocvalModal"
                                        data-id='{{$id_list}}'>
                                            GENERAR VALIDACIÓN
                                    </button>
                                @endif
                            </div>
                        @endcan
                        @break
                    @elseif(isset($data) && ($data->status == 'PREVALIDACION' || $data->status == 'BAJA EN PREVALIDACION' || $data->status == 'EN CAPTURA'))
                    <div class="form-group col-md-6"></div>
                    <div class="form-group col-md-3">
                        <input value="{{$data->id_oficial}}" hidden id="idins" name="idins">
                        <input value="{{TRUE}}" hidden id="borrador" name="borrador">
                        <button type="submit" class="btn mr-sm-4 form-row" style="color: white;">GENERAR BORRADOR</button>
                    </div>
                        @break
                    @endif
                @endforeach
            </div>
        </form>
        <table class="table table-responsive-md" id='tableperfiles'>
            <thead>
                <tr>
                    <th scope="col">INSTRUCTOR</th>
                    <th scope="col">CURP</th>
                    <th scope="col">UNIDAD SOLICITA</th>
                    @if(!isset($data->onlyins))
                        <th scope="col">ESPECIALIDAD</th>
                        <th scope="col">PERFIL PROFESIONAL</th>
                        <th width="85px">CRITERIO PAGO</th>
                    @else
                        <th scope="col">MOVIMIENTO</th>
                    @endif
                    <th scope="col">STATUS</th>
                    @if($data->status == 'BAJA EN PREVALIDACION' || $data->status == 'BAJA EN FIRMA')
                    <th scope="col">MOTIVO</th>
                    @endif
                    <th scope="col">ACCION</th>
                </tr>
            </thead>
            <tbody>
                @php $ret = $cambio_especialidad = FALSE; @endphp
                @foreach($data->data_especialidad as $cadwell)
                    @if($cadwell['status'] != 'INACTIVO')
                        {{-- @php dd($cadwell);  @endphp --}}
                        @if($cadwell['status'] != 'VALIDADO' || $regimen_actual != $data->tipo_honorario )
                            @php if($data->status == 'RETORNO'){ $ret = TRUE;} $cadwell = (object) $cadwell; @endphp
                            <tr>
                                <td><small>{{$data->apellidoPaterno}} {{$data->apellidoMaterno}} {{$data->nombre}}</small></td>
                                <td><small>{{ $data->curp }}</small></td>
                                <td><small>{{ $daesp }} </small></td>
                                @if(!isset($data->onlyins))
                                    @foreach($especialidadeslist as $especialidad)
                                        @if($especialidad->id == $cadwell->especialidad_id)
                                            <td><small>{{ $especialidad->nombre }} @if($data->tipo_honorario != $regimen_actual) (CAMBIO DE REGIMEN FISCAL) @endif</small></td>
                                            @break
                                        @endif
                                    @endforeach
                                    @foreach($data->data_perfil AS $gondor)
                                        @php $gondor = (object) $gondor; @endphp
                                        @if($gondor->id == $cadwell->perfilprof_id)
                                            <td><small>{{ $gondor->grado_profesional}} {{$gondor->area_carrera}}</small></td>
                                            @php $cambio_especialidad = TRUE; @endphp
                                            @break
                                        @endif
                                    @endforeach
                                    @foreach($critpag as $cps)
                                        @if($cps->id == $cadwell->criterio_pago_id)
                                            <td>{{ $cps->perfil_profesional}}</td>
                                            @break
                                        @endif
                                    @endforeach
                                    @foreach($especialidadeslist as $especialidad)
                                        @if($especialidad->id == $cadwell->especialidad_id)
                                            <td><small>{{$data->turnado}} {{ $cadwell->status}}</small></td>
                                            @if($data->status == 'BAJA EN PREVALIDACION' || $data->status == 'BAJA EN FIRMA')
                                                <td><small>{{$data->motivo}}</small></td>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    @if ($data->status == 'BAJA EN PREVALIDACION' || $data->status == 'BAJA EN FIRMA')
                                        <td><small>BAJA DE INSTRUCTOR. {{$cadwell->motivo}}</small></td>
                                    @elseif($data->status == 'REACTIVACION EN PREVALIDACION' || $data->status == 'REACTIVACION EN FIRMA')
                                        <td><small>REACTIVACIÓN DE INSTRUCTOR</small></td>
                                    @elseif($data->tipo_honorario != $regimen_actual)
                                            <td><small>CAMBIO DE REGIMEN FISCAL</small></td>
                                    @else
                                        <td><small>CAMBIO DE INFORMACIÓN BASICA DEL INSTRUCTOR</small></td>
                                    @endif
                                    <td><small>{{$data->turnado}} {{ $data->status}}</small></td>
                                    @if($data->status == 'BAJA EN PREVALIDACION' || $data->status == 'BAJA EN FIRMA')
                                        <td><small>{{$data->motivo}}</small></td>
                                    @endif
                                @endif
                                <td>
                                    @if($data->statusins == 'EN CAPTURA' || $data->statusins == 'RETORNO')
                                        @if($data->status == 'RETORNO')
                                            <a target="_blank" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="EDITAR" href="{{route('instructor-crear-p2', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                        @else
                                            @if($data->numero_control == 'Pendiente')
                                                <a target="_blank" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                            @else
                                                <a target="_blank"class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                            @endif
                                        @endif
                                    @else
                                        @if($data->numero_control == 'Pendiente')
                                            <a target="_blank" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                        @else
                                            <a target="_blank"class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if(isset($data->onlyins))
                            <tr>
                                <td><small>{{$data->apellidoPaterno}} {{$data->apellidoMaterno}} {{$data->nombre}}</small></td>
                                <td><small>{{ $data->curp }}</small></td>
                                <td><small>{{ $daesp }}</small></td>
                                @if ($data->status == 'BAJA EN PREVALIDACION' || $data->status == 'BAJA EN FIRMA')
                                    <td><small>BAJA DE INSTRUCTOR. {{$cadwell->motivo}}</small></td>
                                @elseif($data->status == 'REACTIVACION EN PREVALIDACION' || $data->status == 'REACTIVACION EN FIRMA')
                                    <td><small>REACTIVACIÓN DE INSTRUCTOR</small></td>
                                @elseif($data->tipo_honorario != $regimen_actual && $cambio_especialidad == FALSE)
                                    <td><small>CAMBIO DE REGIMEN FISCAL</small></td>
                                @else
                                    <td><small>CAMBIO DE INFORMACIÓN BASICA DEL INSTRUCTOR</small></td>
                                @endif
                                <td><small>{{$data->turnado}} {{ $data->status}}</small></td>
                                @if($data->status == 'BAJA EN PREVALIDACION' || $data->status == 'BAJA EN FIRMA')
                                    <td><small>{{$data->motivo}}</small></td>
                                @endif
                                <td>
                                    @if($data->statusins == 'EN CAPTURA' || $data->statusins == 'RETORNO')
                                        @if($data->status == 'RETORNO')
                                            <a target="_blank" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="EDITAR" href="{{route('instructor-crear-p2', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                        @else
                                            @if($data->numero_control == 'Pendiente')
                                                <a target="_blank" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                            @else
                                                <a target="_blank"class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                            @endif
                                        @endif
                                    @else
                                        @if($data->numero_control == 'Pendiente')
                                            <a target="_blank" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                        @else
                                            <a target="_blank"class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $data->id])}}"><i class="fas fa-pencil-alt" style="color: white;" aria-hidden="true"></i></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach
            </tbody>
        </table>
        <br>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    @if($data->status == 'PREVALIDACION' || $data->status == 'BAJA EN PREVALIDACION' || $data->status == 'EN FIRMA' || $data->status == 'BAJA EN FIRMA')
                        @can('instructor.prevalidar')
                            <button type="button" class="btn mr-sm-4 mt-3 btn-danger"
                                    data-toggle="modal"
                                    data-placement="top"
                                    data-target="#returntounidadModal"
                                    data-id='{{$id_list}}'>
                                        RETORNAR A UNIDAD
                            </button>
                        @endcan
                    @endif
                </div>
                <div class="pull-right">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            @if($ret == FALSE)
                                @can('instructor.editar_fase2')
                                    @if($data->status == 'EN FIRMA' || $data->status == 'REVALIDACION EN FIRMA' || $data->status == 'BAJA EN FIRMA' || $data->status == 'REACTIVACION EN FIRMA')
                                        <button type="button" class="btn mr-sm-4 mt-3 btn-danger"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#senddoctodtaModal"
                                                data-id='{{$id_list}}'>
                                                    ENVIAR A DTA
                                        </button>
                                    @else
                                        {{-- <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">ENVIAR A DTA</button> --}}
                                        <button type="button" class="btn mr-sm-4 mt-3 btn-danger"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#sendtodtaModal"
                                                data-id='{{$id_list}}'>
                                                    ENVIAR A DTA
                                        </button>
                                    @endif
                                @endcan
                            @endif
                            @if($data->status == 'EN FIRMA' || $data->status == 'REVALIDACION EN FIRMA' || $data->status == 'BAJA EN FIRMA' || $data->status == 'REACTIVACION EN FIRMA')
                                @can('instructor.prevalidar')
                                    @if($data->status == 'BAJA EN FIRMA')
                                        <button type="button" class="btn mr-sm-4 mt-3 btn-danger"
                                            data-toggle="modal"
                                            data-placement="top"
                                            data-target="#validarbajaModal"
                                            data-id='{{$id_list}}'>
                                                VALIDAR
                                        </button>
                                    @else
                                        <button type="button" class="btn mr-sm-4 mt-3 btn-danger"
                                            data-toggle="modal"
                                            data-placement="top"
                                            data-target="#validarModal"
                                            data-id='{{$id_list}}'>
                                                VALIDAR
                                        </button>
                                    @endif
                                @endcan
                            @else
                                @can('instructor.prevalidar')
                                    {{-- <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">ENVIAR A DTA</button> --}}
                                    <button type="button" class="btn mr-sm-4 mt-3 btn-danger"
                                            data-toggle="modal"
                                            data-placement="top"
                                            data-target="#prevalidarModal"
                                            data-id='[{{$id_list}}, "{{$chk_mod_espec}}"]'>
                                                PREVALIDAR
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(isset($databuzon))
        {{-- <div class="container"> --}}
            <h2>Buzon de actividad</h2>
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home">Solicitudes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#menu1">Atendidos</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="home" class="tab-pane fade show active">
                    <h3>Solicitudes</h3>
                    <table class="table table-responsive-md" id='tableperfiles'>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">INSTRUCTOR</th>
                                <th scope="col">NUMERO DE REVISIÓN</th>
                                <th scope="col">UNIDAD SOLICITA</th>
                                <th scope="col">STATUS</th>
                                <th scope="col">FECHA</th>
                                <th scope="col">ACCION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $iteration1 = 0; @endphp
                            @foreach ($databuzon AS $rise)
                                <tr>
                                    <td>{{ ++$iteration1 }}</td>
                                    <td>{{ $rise->nombre }} {{$rise->apellidoPaterno}} {{$rise->apellidoMaterno}}</td>
                                    <td>{{ $rise->nrevision }}</td>
                                    <td>{{ $rise->unidad_solicita }}</td>
                                    <td>{{ $rise->status }} {{ $rise->turnado }}</td>
                                    <td>{{ $rise->updated_at}}</td>
                                    <td>
                                        <form action="{{ route('prevalidar-ins') }}" method="GET">
                                        @csrf
                                            <input type="text" name="valor" id="valor" hidden value="{{ $rise->nrevision }}">
                                            <button style="font-color: white;" type="submit" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR"><i style="color: white;" class="fas fa-pencil-alt" aria-hidden="true"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <h3>Atendidos</h3>
                    <table class="table table-responsive-md" id='tableperfiles'>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">INSTRUCTOR</th>
                                <th scope="col">NUMERO DE REVISIÓN</th>
                                <th scope="col">UNIDAD SOLICITA</th>
                                <th scope="col">STATUS</th>
                                <th scope="col">FECHA</th>
                                <th scope="col">ACCION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $iteration = 0; @endphp
                            @foreach ($buzonhistory AS $rise)
                                <tr>
                                    <td>{{ ++$iteration }}</td>
                                    <td>{{ $rise->nombre }} {{$rise->apellidoPaterno}} {{$rise->apellidoMaterno}}</td>
                                    <td>{{ $rise->nrevision }}</td>
                                    <td>{{ $userunidad->ubicacion }}</td>
                                    <td>{{ $rise->status }} {{ $rise->turnado }}</td>
                                    <td>{{ $rise->updated_at}}</td>
                                    <td>
                                        <a style="color: white;" target="_blank" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" title="MOSTRAR" href="{{route('instructor-ver', ['id' => $rise->id])}}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        {{-- </div> --}}
    @endif
</div>
<!-- Modal Enviar a DTA -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="sendtodtaModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Enviar a DTA</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('ins-to-dta') }}" id="regsupre" method="POST">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="sendtodtawarning">
                        <span id="sendtodtaspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar el envio de estos registros a prevalidacion?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        {{-- <div class="form-group col-md-10">
                            <label for="inputmemosol">Numero de Memorandum para la Solicitud</label>
                            <input name="memosol" id="memosol" type="text" class="form-control" aria-required="true" required>
                        </div> --}}
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" style="text-align:center;width:100%">
                            <button onclick="sendtodta()" class="btn mr-sm-4 mt-3 btn-danger" >Enviar a DTA</button>
                        </div>
                    </div>
                    <br>
                    <input hidden  name="idinstructores" id="idinstructores">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Retornar a Unidad -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="returntounidadModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Retornar a Unidad</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('instructor-rechazo') }}" id="regsupre" method="POST">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="returntodtawarning">
                        <span id="returntodtaspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar el retorno de estos registros a unidad?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-3">
                            <textarea name="observacion_retorno" id="observacion_retorno" cols="40" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6" style="text-align:center;width:100%">
                            <button id='guardar' class="btn mr-sm-4 mt-3 btn-danger" type="submit" class="btn" value="firma" onclick="guardado(this.value)">Retornar a Firma</button>
                        </div>
                        <div class="form-group col-md-6" style="text-align:center;width:100%">
                            <button id='guardar' class="btn mr-sm-4 mt-3 btn-danger" type="submit" class="btn" value="captura" onclick="guardado(this.value)">Retornar a Captura</button>
                        </div>
                    </div>
                    <br>
                    <input id="tipo_envio" name="tipo_envio" hidden>
                    <input hidden  name="idinstructoresreturn" id="idinstructoresreturn">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Prevalidar -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="prevalidarModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>¿Desea confirmar la prevalidación de estos registros?</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('instructor-prevalidar') }}" id="regsupre" method="POST">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="prevalidarwarning">
                        <span id="prevalidarspan"></span>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2"></div>
                        <div class="form-group col-md-9" id='divbasico'>
                            <label><small>¿Desea confirmar la prevalidación de los datos?</small></label>
                        </div>
                        <div class="form-group col-md-9" id="divfecha">
                            <label for="fechadocs"><small>FECHA A ASIGNAR A LOS DOCUMENTOS</small></label>
                            <input type="date" name="fechadocs" id="fechadocs" class="form-control">
                        </div>
                    </div>
                    {{-- <label style="text-align:center"><h5><small>¿Desea confirmar la prevalidación de estos registros?</small></h5></label> --}}
                    <div class="form-row">
                        <div class="form-group col-md-1"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2"></div>
                        <div class="form-group col-md-2">
                            <button type="button" class="btn" style="text-align: right; background-color: #12322B;" data-dismiss="modal">Cerrar</button>
                        </div>
                        <div class="form-group col-md-8" style="text-align:center;width:100%">
                            <button onclick="sendtodta()" class="btn btn-danger" >PREVALIDAR</button>
                        </div>
                    </div>
                    <br>
                    <input hidden  name="idinstructoresprev" id="idinstructoresprev">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Enviar Solicitud Firmada a DTA -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="senddoctodtaModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Enviar Solicitud Firmada a DTA</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('instructor-solicitud-firmada-todta') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="senddoctodtawarning">
                        <span id="senddoctodtaspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar el envio de la solicitud de validación del instructor?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="inputmemosol">Carga de Solicitud de Validación Firmada</label>
                            <input name="memosolicitud" id="memosolicitud" type="file" accept="application/pdf" class="form-control" aria-required="true" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" style="text-align:center;width:100%">
                            <button onclick="sendtodta()" class="btn mr-sm-4 mt-3 btn-danger" >Enviar a DTA</button>
                        </div>
                    </div>
                    <br>
                    <input hidden  name="idinsdoctodta" id="idinsdoctodta">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Generar Validacion a DTA -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="generateddocvalModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Generar Memorandum de Validación</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('instructor-validacion-pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="generateddocvalwarning">
                        <span id="generateddocvalspan"></span>
                    </div>
                    {{-- <label style="text-align:center"><h5><small>¿Desea confirmar el envio de la solicitud de validación del instructor?</small></h5></label> --}}
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="inputmemosol">Memorandum de la Validación de instructor</label>
                            <input name="memovali" id="memovali" type="text" class="form-control" aria-required="true" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="inputmemosol">Observación de la Validación de instructor</label>
                            <textarea name="observacion_validacion" id="observacion_validacion" cols="30" rows="10" class="form-control" aria-required="true" required></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" style="text-align:center;width:100%">
                            <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">GENERAR VALIDACIÓN</button>
                        </div>
                    </div>
                    <br>
                    <input hidden  name="idinsgendocval" id="idinsgendocval">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Enviar Validacion Firmada a UNIDAD -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="validarModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Cargar Validación Firmada</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('instructor-validar') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="validarwarning">
                        <span id="validarspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar la carga de la validación del instructor?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="inputmemosol">Carga de Validación Firmada</label>
                            <input name="memovalidacion" id="memovalidacion" type="file" accept="application/pdf" class="form-control" aria-required="true" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" style="text-align:center;width:100%">
                            <button onclick="sendtodta()" class="btn mr-sm-4 mt-3 btn-danger" >Enviar a Unidad</button>
                        </div>
                    </div>
                    <br>
                    <input hidden  name="idinsvalidar" id="idinsvalidar">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Generar Validacion de Baja a DTA -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="generateddocbajaModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Generar Memorandum de Validación de Baja</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('instructor-baja-validacion-pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="generateddocbajawarning">
                        <span id="generateddocbajaspan"></span>
                    </div>
                    {{-- <label style="text-align:center"><h5><small>¿Desea confirmar el envio de la solicitud de validación del instructor?</small></h5></label> --}}
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="inputmemosol">Memorandum de la Baja de Instructor</label>
                            <input name="memobaja" id="memobaja" type="text" class="form-control" aria-required="true" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" style="text-align:center;width:100%">
                            <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">GENERAR VALIDACIÓN DE BAJA</button>
                        </div>
                    </div>
                    <br>
                    <input hidden  name="idinsbajadocval" id="idinsbajadocval">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Enviar Validacion de Baja Firmada a UNIDAD -->
<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="validarbajaModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Cargar Validación de Baja Firmada</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card card-body" >
                <form action="{{ route('instructor-baja-validar') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="alert alert-danger d-none d-print-none" id="validarbajawarning">
                        <span id="validarbajaspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar la carga de la validación de Baja del instructor?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="inputmemosol">Carga de Validación Firmada</label>
                            <input name="memovalidacionbaja" id="memovalidacionbaja" type="file" accept="application/pdf" class="form-control" aria-required="true" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" style="text-align:center;width:100%">
                            <button onclick="sendtodta()" class="btn mr-sm-4 mt-3 btn-danger" >Enviar a Unidad</button>
                        </div>
                    </div>
                    <br>
                    <input hidden  name="idinsvalidarbaja" id="idinsvalidarbaja">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END -->
@endsection
@section('script_content_js')
    <script src="{{ asset('js/solicitud/apertura.js') }}"></script>
    <script src="{{ asset('edit-select/jquery-editable-select.min.js') }}"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function local() {
            var valor = document.getElementById("seluni").value;
            var datos = {valor: valor};
            // console.log(datos);

            var url = '/instructores/busqueda/nrevision';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#valor").empty();
                var selectL = document.getElementById('valor'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].nrevision;
                    newOption.text=respuesta[i].nrevision;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        $('#sendtodtaModal').on('show.bs.modal', function(event){
            $('#sentodtawarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinstructores').value = id;
        });

        $('#returntounidadModal').on('show.bs.modal', function(event){
            $('#returntounidadwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            console.log(id)
            document.getElementById('idinstructoresreturn').value = id;
        });

        $('#prevalidarModal').on('show.bs.modal', function(event){
            $('#returntounidadwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            console.log(id)
            if(id['1'] == '1') {
                $('#divbasico').prop("class", "form-row d-none d-print-none")
                $('#divfecha').prop("class", "form-row col-md-9")
                $('#fechadocs').prop('required', true);
            } else {
                $('#divbasico').prop("class", "form-row col-md-9")
                $('#divfecha').prop("class", "form-row d-none d-print-none")
                $('#fechadocs').prop('required', false);
            }
            document.getElementById('idinstructoresprev').value = id['0'];
        });

        $('#senddoctodtaModal').on('show.bs.modal', function(event){
            $('#senddoctodtawarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinsdoctodta').value = id;
        });

        $('#generateddocvalModal').on('show.bs.modal', function(event){
            $('#generateddocvalwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinsgendocval').value = id;
        });

        $('#validarModal').on('show.bs.modal', function(event){
            $('#validarwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinsvalidar').value = id;
        });

        $('#generateddocbajaModal').on('show.bs.modal', function(event){
            $('#generateddocbajawarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinsbajadocval').value = id;
        });

        $('#validarbajaModal').on('show.bs.modal', function(event){
            $('#validarbajawarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinsvalidarbaja').value = id;
        });

        function guardado(buttonValue) {
            // Código de la función aquí
            console.log(buttonValue);
            var confirmacion = confirm("¿Está seguro de continuar?");
            document.getElementById('tipo_envio').value = buttonValue;

            // Si el usuario hace clic en "Aceptar", continuar con el envío del formulario
            if (confirmacion) {
                return true;
            }
            // Si el usuario hace clic en "Cancelar", detener el envío del formulario
            else {
                return false;
            }
        }
    </script>
@endsection
