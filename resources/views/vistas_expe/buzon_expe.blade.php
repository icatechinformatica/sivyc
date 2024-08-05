<!--Creado por Jose Luis Moreno Arcos luisito08672@gmail.com-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        table tr td, table tr th{ font-size: 12px;}
    </style>
@endsection

@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Buzon Expediente | SIVyC Icatech')
    <!--seccion-->

@section('content')
    <style>
            input[type=text],
            select,
            textarea {
                text-transform: none !important;
            }

            /* estilo de cuerpo */
            .colorTitulo{
                background-color: #adabab;
            }
            .negrita {
                font-weight: bold;
                color: #000;
            }
            #txtbuscar{
                width: 20%;
            }
            #unidad{
                width: 15%;
            }
            #status{
                width: 20%;
            }
            /* Color de fondo de status dptos */
            .fondo_save{
                /* background-color: #aeeca8; */
                font-weight: bold;
                color: #13B429;
            }
            .fondo_capt{
                /* background-color: #fefda6; */
                font-weight: bold;
                color: #257DD6 ;
            }
            .fila-especial {
                font-size: 11px;
            }
    </style>

    <div class="card-header py-2">
        <h3>Buzón Expediente Único</h3>
    </div>

    {{-- Card como contenedor --}}
    <div class="card card-body">
        {{-- Mensaje de alerta --}}
        @if (session()->has('message') && session()->has('status'))
            <div class="alert alert-{{ session('status') }} alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ session('message') }}</strong>
            </div>
        @endif

        {{-- FORMULARIO DE BUSQUEDA --}}
        {{-- @can('alumno.inscrito.update') --}}

        <form action="" method="" id="frmBuscarGrupo">
            @csrf
            <div class="row form-inline pl-4">
                <select name="sel_ejercicio" id="" class="form-control mx-2">
                    @foreach ($ejercicio as $anio)
                        <option {{$anio == $sel_eje ? 'selected' : '' }} value="{{$anio}}">{{$anio}}</option>
                    @endforeach
                </select>
                <select name="sel_unidad" id="unidad" class="form-control mx-2">
                    <option {{($sel_uni == '') ? 'selected' : ''}} value="">- UNIDAD -</option>
                    @foreach ($unidades as $unidad)
                        <option {{($unidad == $sel_uni) ? 'selected' : ''}} value="{{$unidad}}">{{$unidad}}</option>
                    @endforeach
                </select>
                <select name="sel_status" id="status" class="form-control mx-2">
                    {{-- <option value="">- ESTATUS -</option> --}}
                    @foreach ($status_dpto as $st)
                        <option {{($st == $sel_status) ? 'selected' : ''}} value="{{$st}}">{{$st}}</option>
                    @endforeach
                </select>
                <input type="text" class="form-control mx-2" name="txtbuscar" id="txtbuscar" value="{{$txtbuscar}}" placeholder="FOLIO / CLAVE">

                <button class="btn mx-2" id="btn_buscar">BUSCAR</button>
                <a href="{{ route('buzon.expunico.index') }}" class="btn mx-2">BORRAR FILTRO</a>

            </div>
        </form>

        {{-- @endcan --}}

        {{-- TABLA DE DELEGACION ADMINISTRATIVA --}}
        {{-- AGREGAR OTRO IF SI ES DELEGACION ADMINISTRATIVA ENTONCES QUE MUESTRE ESTA TABLA --}}
        @if ($val_rol  == 3)
            <div class="table-responsive p-0 m-0">
                @if (count($data_admin)>0)
                    <table class="table table-hover table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">UNIDAD</th>
                                <th scope="col">GRUPO</th>
                                <th scope="col" class="col-1">CLAVE</th>
                                <th scope="col">CURSO</th>
                                <th scope="col">INSTRUCTOR</th>
                                <th scope="col">FECHA DE CURSO</th>
                                <th scope="col">HORARIO</th>
                                <th scope="col" class="text-center">VINC</th>
                                <th scope="col" class="text-center">ACAD</th>
                                <th scope="col" class="text-center">ADMIN</th>
                                <th scope="col">STATUS</th>
                                <th scope="col">VER</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data_admin as $key => $item)
                                <tr>
                                    <td class="fila-especial">{{($data_admin->currentPage() - 1) * $data_admin->perPage() + $key + 1 }}</td>
                                    <td class="fila-especial">{{$item->unidad}}</td>
                                    <td class="fila-especial">{{$item->folio_grupo}}</td>
                                    <td class="fila-especial">{{$item->clave}}</td>
                                    <td class="fila-especial">{{$item->curso}}</td>
                                    <td class="fila-especial">{{$item->nombre}}</td>
                                    <td class="fila-especial">{{\Carbon\Carbon::parse($item->inicio)->format('d/m/Y') .' '. \Carbon\Carbon::parse($item->termino)->format('d/m/Y')}}</td>
                                    <td class="fila-especial">{{$item->hini}} a {{$item->hfin}}</td>
                                    <td class="{{($item->sav_vinc == "true") ? 'fondo_save' : 'fondo_capt'}} text-center fila-especial">
                                        {{($item->sav_vinc == "true") ? 'GUARDADO EL '.\Carbon\Carbon::parse($item->fecg_vin)->format('d/m/Y g:i A') : 'CAPTURA'}}
                                    </td>
                                    <td class="{{($item->sav_acad == "true") ? 'fondo_save' : 'fondo_capt'}} text-center fila-especial">
                                        {{($item->sav_acad == "true") ? 'GUARDADO EL '.\Carbon\Carbon::parse($item->fecg_aca)->format('d/m/Y g:i A') : 'CAPTURA'}}
                                    </td>
                                    <td class="{{($item->sav_admin == "true") ? 'fondo_save' : 'fondo_capt'}} text-center fila-especial">
                                        {{($item->sav_admin == "true") ? 'GUARDADO EL '.\Carbon\Carbon::parse($item->fecg_admi)->format('d/m/Y g:i A') : 'CAPTURA'}}
                                    </td>
                                    <td class="fila-especial">
                                        @if ($item->sav_vinc == "true" && $item->sav_acad == "true"
                                        && $item->sav_admin == "true" && $item->st_admin == 'CAPTURA')
                                            PENDIENTE POR ENVIAR
                                        @elseif($item->sav_vinc == "false" || $item->sav_acad == "false"
                                        && $item->sav_admin == "false" || $item->st_admin == 'CAPTURA')
                                            EN CAPTURA
                                        @elseif($item->st_vinc == 'ENVIADO' && $item->st_aca == 'ENVIADO'
                                        && $item->st_admin == 'ENVIADO')
                                            ENVIADO
                                        @elseif($item->st_vinc == 'RETORNADO' && $item->st_aca == 'RETORNADO'
                                        && $item->st_admin == 'RETORNADO')
                                            RETORNADO
                                        @elseif($item->st_vinc == 'VALIDADO' && $item->st_aca == 'VALIDADO'
                                        && $item->st_admin == 'VALIDADO')
                                            VALIDADO
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a class="btn-circle btn-circle-sm"  title="Ver pdf" id=""
                                            href="{{route('expunico.principal.mostrar.get', ['folio' => $item->folio_grupo])}}" target="_blank">
                                            <i class="fa fa-search fa-2x fa-lg text-info from-control" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='14'>
                                    {{$data_admin->appends(request()->query())->links()}}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="text-center p-5 bg-light">
                        <h5> <b>NO SE ENCONTRARON REGISTROS</b></h5>
                    </div>
                @endif
            </div>
        @endif

        {{-- TABLA PARA DTA --}}
        {{-- @dd($data_admin) --}}
        @if ($val_rol  == 4)
            <div class="table-responsive p-0 m-0">
                @if (count($data_admin)>0)
                    <table class="table table-hover table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">UNIDAD</th>
                                <th scope="col">GRUPO</th>
                                <th scope="col" class="col-1">CLAVE</th>
                                <th scope="col">CURSO</th>
                                <th scope="col">INSTRUCTOR</th>
                                <th scope="col">FECHA DE CURSO</th>
                                <th scope="col">HORARIO</th>
                                @if ($sel_status == 'PENDIENTE')
                                    <th scope="col">FECHA DE ENVIO</th>
                                @elseif($sel_status == 'RETORNADO')
                                    <th scope="col">FECHA DE RETORNO</th>
                                @elseif($sel_status == 'VALIDADO')
                                    <th scope="col">FECHA DE VALIDACION</th>
                                @endif
                                <th scope="col">STATUS</th>
                                <th scope="col">VER</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data_admin as $key => $item)
                                <tr>
                                    <td>{{($data_admin->currentPage() - 1) * $data_admin->perPage() + $key + 1 }}</td>
                                    <td>{{$item->unidad}}</td>
                                    <td>{{$item->folio_grupo}}</td>
                                    <td>{{$item->clave}}</td>
                                    <td>{{$item->curso}}</td>
                                    <td>{{$item->nombre}}</td>
                                    <td>{{\Carbon\Carbon::parse($item->inicio)->format('d/m/Y').' '.\Carbon\Carbon::parse($item->termino)->format('d/m/Y')}}</td>
                                    <td>{{$item->hini}} a {{$item->hfin}}</td>
                                    @if ($sel_status == 'PENDIENTE')
                                        <td>{{\Carbon\Carbon::parse($item->fec_envio)->format('d/m/Y g:i A')}}</td>
                                    @elseif($sel_status == 'RETORNADO')
                                        <td>{{\Carbon\Carbon::parse($item->fec_return)->format('d/m/Y g:i A')}}</td>
                                    @elseif($sel_status == 'VALIDADO')
                                        <td>{{\Carbon\Carbon::parse($item->fec_valid)->format('d/m/Y g:i A')}}</td>
                                    @endif
                                    <td>{{$sel_status}}</td>
                                    <td class="text-center">
                                        <a class="btn-circle btn-circle-sm"  title="Ver pdf" id=""
                                            href="{{route('expunico.principal.mostrar.get', ['folio' => $item->folio_grupo])}}" target="_blank">
                                            <i class="fa fa-search fa-2x fa-lg text-info from-control" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='14'>
                                    {{$data_admin->appends(request()->query())->links()}}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="text-center p-5 bg-light">
                        <h5> <b>NO SE ENCONTRARON REGISTROS</b></h5>
                    </div>
                @endif
            </div>
        @endif


    </div>
    {{-- fin del contenedor card --}}


        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){

                /*Deshabilitamos la prate de convertir a mayusculas*/
                $("input[type=text], textarea, select").off("keyup");

                $("#btn_buscar").click(function(){
                    $('#frmBuscarGrupo').attr({
                        'action':"{{route('buzon.expunico.index')}}",
                        'target':'_self'
                    });
                    $('#frmBuscarGrupo').submit();
                });

                $("#btn_borrar").click(function(){
                    const url = window.location.origin + window.location.pathname;
                    // Redirigimos al formulario adicional que enviará una solicitud GET sin parámetros
                    document.getElementById('frmBuscarGrupo').action = url;
                    document.getElementById('frmBuscarGrupo').submit();
                });
            });


        </script>
        @endsection
@endsection
