@extends("theme.sivyc.layout")
@section('title', 'FIRMAR ELECTRONICAMENTE | Sivyc Icatech')

@section('content_script_css')
    <style>
        .colorTop {
            background-color: #541533;
        }
        .negrita{
            font-weight: bold;
        }

        /* Estilo del loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
            z-index: 9999; /* Asegura que esté por encima de otros elementos */
            display: none; /* Ocultar inicialmente */
        }

        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 6px solid #fff;
            border-top: 6px solid #621132;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

    </style>

    {{-- links de prueba y de produccion --}}
    <link rel="stylesheet" type="text/css" href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" />

@endsection

@section('content')
    <div class="d-none" id="vHTMLSignature"></div>
    <input class="d-none" id="token" name="token" type="text" value="{{$token}}">

    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    <div class="container-fluid pt-3 px-0 py-0 mx-0 my-0">
        <div class="row">
            <div class="col">
                @if ($message = Session::get('warning'))
                    <div class="alert alert-info">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                @if ($message = Session::get('danger'))
                    <div class="alert alert-danger">
                        @if(isset($message['descripcion']))
                            <p>{{ $message['descripcion'] }} con el folio: {{ $message['uuid'] }}</p>
                        @else
                            <p>{{$message}}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- <form action="{{ route('firma.inicio') }}" method="get">
            <div class="row d-flex align-items-center py-2">
                <div class="col">
                    <select class="custom-select" id="tipo_documento" name="tipo_documento">
                        <option value="" selected>Todos los documentos</option>
                        <option {{$tipo_documento == 'Contrato' ? 'selected' : ''}} value="Contrato">Contrato</option>
                        <option {{$tipo_documento == 'Lista de asistencia' ? 'selected' : ''}} value="Lista de asistencia">Lista de asistencia</option>
                        <option {{$tipo_documento == 'Lista de calificaciones' ? 'selected' : ''}} value="Lista de calificaciones">Lista de calificaciones</option>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-info">Buscar</button>
                </div>
            </div>
        </form> --}}

        <div class="card">
            <div class="card-header">Mis documentos</div>
            {{-- Buscador --}}
            <div class="card">
                <div class="card-body">
                    <form action="" class="form-inline" id="frmBuscar" method="get">
                        <input type="text" class="form-control" placeholder="CLAVE DE CURSO" name="txtBusqueda" id="txtBusqueda" value="{{($busqueda_clave != null) ? $busqueda_clave : ''}}">
                        <input type="hidden" name="seccion" id="seccion">
                        <a class="btn btn-success ml-3" data-toggle="tooltip"
                                data-placement="top" title="Buscar" href="#" onclick="Buscar(event)">
                                <i class="fa fa-search" aria-hidden="true"></i>
                        </a>
                        <a class="btn btn-danger" data-toggle="tooltip"
                                data-placement="top" title="Limpiar" href="#" onclick="Limpiar(event)">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </a>
                    </form>
                </div>
            </div>
            <div class="card-body px-0">

                <div class="row">
                    <div class="col">
                        {{-- encabezado --}}
                        <nav>
                            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                @php
                                    $clase_pestaña = 'nav-item nav-link active';
                                    $clase_contenido = 'tab-pane fade show active';
                                @endphp
                                {{-- @if($rol->role_id == 30 || $rol->role_id == 31)
                                    <a class="nav-item nav-link active" id="nav-vobo-tab" data-toggle="tab" href="#nav-vobo" role="tab" aria-controls="nav-vobo" aria-selected="true">Vo. Bo.</a>
                                    @php
                                        $clase_pestaña = 'nav-item nav-link';
                                        $clase_contenido = 'tab-pane fade show';
                                    @endphp
                                @endif --}}
                                <a class="{{($seleccion == null || $seleccion == 'por_firmar') ? $clase_pestaña : 'nav-item nav-link'}} font-weight-bold" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true" onclick="cambiarSection('por_firmar')">Por Firmar</a>
                                <a class="{{($seleccion == 'firmados') ? $clase_pestaña : 'nav-item nav-link'}} font-weight-bold" id="nav-firmados-tab" data-toggle="tab" href="#nav-firmados" role="tab" aria-controls="nav-firmados" aria-selected="false" onclick="cambiarSection('firmados')">Firmados</a>
                                <a class="{{($seleccion == 'sellados') ? $clase_pestaña : 'nav-item nav-link'}} font-weight-bold" id="nav-validados-tab" data-toggle="tab" href="#nav-validados" role="tab" aria-controls="nav-validados" aria-selected="false" onclick="cambiarSection('sellados')">Sellados</a>
                                <a style="color: #FF5733" class="{{($seleccion == 'cancelados') ? $clase_pestaña : 'nav-item nav-link'}} font-weight-bold" id="nav-cancelados-tab" data-toggle="tab" href="#nav-cancelados" role="tab" aria-controls="nav-cancelados" aria-selected="false" onclick="cambiarSection('cancelados')">Cancelados</a>
                                @if(isset($curpUser))<p  class="nav-item nav-link font-weight-bold" style="margin-left: 50%;">CURP: {{$curpUser->curp}}</p>@endif
                            </div>
                        </nav>

                        {{-- contenido --}}
                        <div class="tab-content py-3 px-sm-0" id="nav-tabContent">
                            {{-- Por Firmar --}}
                            <div class="{{($seleccion == null || $seleccion == 'por_firmar') ? $clase_contenido : 'tab-pane fade'}}" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                @if (count($docsFirmar) != 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="negrita">Grupo</th>
                                                    <th scope="col" class="negrita">Clave</th>
                                                    <th scope="col" class="negrita">Nombre del documento</th>
                                                    <th scope="col" class="negrita">Ver documento</th>
                                                    <th scope="col" class="negrita">Firmantes</th>
                                                    <th scope="col" class="negrita">Creado</th>
                                                    <th scope="col" colspan="2" class="text-center negrita">Acción</th>
                                                    {{-- <th scope="col">Firmar</th> --}}
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($docsFirmar as $key => $docFirmar)
                                                    @php
                                                        $firmantes = [];
                                                        $nameArchivo = '';
                                                        $obj = json_decode($docFirmar->obj_documento, true);
                                                        $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                        foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                            // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].'(), ';
                                                            if($value['_attributes']['email_firmante'] == $email){
                                                                $curp = $value['_attributes']['curp_firmante'];
                                                            }

                                                            if(empty($value['_attributes']['firma_firmante'])){
                                                                // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (NO), ';
                                                                array_push($firmantes, $value['_attributes']['nombre_firmante'].' (NO)');
                                                            } else {
                                                                // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (SI), ';
                                                                array_push($firmantes, $value['_attributes']['nombre_firmante'].' (SI)');
                                                            }
                                                        }
                                                        // $firmantes = substr($firmantes, 0, -2);
                                                    @endphp
                                                    <tr>
                                                        <td><p>{{ isset($docFirmar->folio_grupo) ? $docFirmar->folio_grupo : '' }}</p></td>
                                                        <td><p>{{ isset($docFirmar->numero_o_clave) ? $docFirmar->numero_o_clave : '' }}</p></td>
                                                        <td><small>{{$nameArchivo}}</small></td>
                                                        <td>
                                                            @switch($docFirmar->tipo_archivo)
                                                                @case('Lista de asistencia')
                                                                    <a href="{{route('asistencia-pdf', ['id' => $docFirmar->idcursos])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('Lista de calificaciones')
                                                                    <a href="{{route('calificacion-pdf', ['id' => $docFirmar->idcursos])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('Reporte fotografico')
                                                                    <a href="{{route('reportefoto-pdf', ['id' => $docFirmar->idcursos])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('Solicitud Pago')
                                                                    <a href="{{route('solpa-pdf', ['id' => $docFirmar->id_folios])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('supre')
                                                                    @php $ids =  base64_encode($docFirmar->id_supre) @endphp
                                                                    <a href="{{route('supre-pdf', ['id' => $ids])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('valsupre')
                                                                    @php $ids =  base64_encode($docFirmar->id_supre) @endphp
                                                                    <a href="{{route('valsupre-pdf', ['id' => $ids])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @default {{-- Contratos --}}
                                                                    <a href="{{route('contrato-pdf', ['id' => $docFirmar->id_contrato])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <small>
                                                                @foreach ($firmantes as $item)
                                                                    <p class="my-0">{{$item}}</p>
                                                                @endforeach
                                                            </small>
                                                        </td>
                                                        <td><small>{{$docFirmar->created_at->format('d-m-Y')}}</small></td>
                                                        @if(!in_array($rol->role_id, [31, 47]))
                                                            <td>
                                                                <button class="btn btn-outline-danger" type="button" onclick="cancelarDocumento('{{$docFirmar->id}}', '{{$nameArchivo}}', '{{$docFirmar->tipo_archivo}}', '{{$docFirmar->numero_o_clave}}')">Cancelar</button>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-outline-primary" href="#" data-toggle="modal" data-target="#mdlLoadViewSignature" onclick="abriModal('{{$key}}')">firmar</button>
                                                            </td>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                        @endif
                                                        <input class="d-none" value="{{$docFirmar->id}}" name="idFile{{$key}}" id="idFile{{$key}}" type="text">
                                                        <input class="d-none" value="{{$docFirmar->cadena_original}}" name="cadena{{$key}}" id="cadena{{$key}}" type="text">
                                                        <input class="d-none" value="{{$docFirmar->base64xml}}" name="xml{{$key}}" id="xml{{$key}}" type="text">
                                                        <input class="d-none" value="{{$curpUser->curp}}" name="curp{{$key}}" id="curp{{$key}}" type="text">
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- Paginación --}}
                                    <div class="row py-4">
                                        <div class="col d-flex justify-content-center">
                                            {{$docsFirmar->appends(array_merge(request()->query(), ['section' => 'por_firmar']))->links()}}
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-primary" role="alert">
                                        <strong class="d-block text-center">No se encontraron documentos en esta sección</strong>
                                    </div>
                                @endif
                            </div>

                            {{-- Firmados --}}
                            <div class="{{($seleccion == 'firmados') ? $clase_contenido : 'tab-pane fade'}}" id="nav-firmados" role="tabpanel" aria-labelledby="nav-home-tab">
                                @if (count($docsFirmados) != 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="negrita">Grupo</th>
                                                    <th scope="col" class="negrita">Clave</th>
                                                    <th scope="col" class="negrita">Nombre del documento</th>
                                                    <th scope="col" class="negrita">Ver documento</th>
                                                    <th scope="col" class="negrita">Firmantes</th>
                                                    <th scope="col" class="negrita">Creado</th>
                                                    <th scope="col" colspan="2" class="text-center negrita">Acción</th>
                                                    {{-- <th scope="col">Validar</th> --}}
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($docsFirmados as $docFirmado)
                                                    @php
                                                        $sendValidation = true;
                                                        $firmantes = [];
                                                        $nameArchivo = '';
                                                        $obj = json_decode($docFirmado->obj_documento, true);
                                                        // $obj2 = json_decode($docFirmado->obj_documento_interno, true);
                                                        $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                        foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                            if(empty($value['_attributes']['firma_firmante'])){
                                                                $sendValidation = false;
                                                                // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (NO), ';
                                                                array_push($firmantes, $value['_attributes']['nombre_firmante'].' (NO)');
                                                            } else {
                                                                // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (SI), ';
                                                                array_push($firmantes, $value['_attributes']['nombre_firmante'].' (SI)');
                                                            }
                                                        }
                                                    @endphp

                                                    <tr>
                                                        <td><p>{{ isset($docFirmado->folio_grupo) ? $docFirmado->folio_grupo : '' }}</p></td>
                                                        <td><p>{{ isset($docFirmado->numero_o_clave) ? $docFirmado->numero_o_clave : '' }}</p></td>
                                                        <td><small>{{$nameArchivo}}</small></td>
                                                        <td>
                                                            @switch($docFirmado->tipo_archivo)
                                                                @case('Lista de asistencia')
                                                                    <a href="{{route('asistencia-pdf', ['id' => $docFirmado->idcursos])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('Lista de calificaciones')
                                                                    <a href="{{route('calificacion-pdf', ['id' => $docFirmado->idcursos])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('Reporte fotografico')
                                                                    <a href="{{route('reportefoto-pdf', ['id' => $docFirmado->idcursos])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('Solicitud Pago')
                                                                    <a href="{{route('solpa-pdf', ['id' => $docFirmado->id_folios])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('supre')
                                                                    @php $ids =  base64_encode($docFirmado->id_supre) @endphp
                                                                    <a href="{{route('supre-pdf', ['id' => $ids])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('valsupre')
                                                                    @php $ids =  base64_encode($docFirmado->id_supre) @endphp
                                                                    <a href="{{route('valsupre-pdf', ['id' => $ids])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @default {{-- Contratos --}}
                                                                    <a href="{{route('contrato-pdf', ['id' => $docFirmado->id_contrato])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <small>
                                                                @foreach ($firmantes as $item)
                                                                    <p class="my-0">{{$item}}</p>
                                                                @endforeach
                                                            </small>
                                                        </td>
                                                        <td><small>{{$docFirmado->created_at->format('d-m-Y')}}</small></td>
                                                        @if($rol->role_id == '30' || $rol->role_id == '2' || $rol->role_id == '8' || $rol->role_id == '4')
                                                            <td>
                                                                <button type="button" onclick="cancelarDocumento('{{$docFirmado->id}}', '{{$nameArchivo}}', '{{$docFirmado->tipo_archivo}}', '{{$docFirmado->numero_o_clave}}')" class="btn btn-outline-danger">Cancelar</button>
                                                            </td>
                                                            <td>
                                                                {{-- @if ($obj['emisor']['_attributes']['email'] == $email) --}}
                                                                @can('efirma.sellar')
                                                                    {{-- @if ($sendValidation && $docFirmado->tipo_archivo == 'Contrato' && $rol->role_id == '2')
                                                                        <button type="button" onclick="sellardocumento('{{$docFirmado->id}}')" class="btn btn-outline-primary">Sellar</button>
                                                                    @elseif($sendValidation && in_array($docFirmado->tipo_archivo, ['Lista de asistencia','Lista de calificaciones','Reporte fotografico']) && $rol->role_id == '30')
                                                                        <button type="button" onclick="sellardocumento('{{$docFirmado->id}}')" class="btn btn-outline-primary">Sellar</button>
                                                                    @elseif($sendValidation && !in_array($rol->role_id, ['2','30'])
                                                                        <button type="button" onclick="sellardocumento('{{$docFirmado->id}}')" class="btn btn-outline-primary">Sellar</button>
                                                                    @else
                                                                        Faltan Firmas
                                                                    @endif --}}
                                                                    @if ($sendValidation)
                                                                        <button type="button" onclick="sellardocumento('{{$docFirmado->id}}')" class="btn btn-outline-primary">Sellar</button>
                                                                    @else
                                                                        Faltan Firmas
                                                                    @endif
                                                                @endcan

                                                            </td>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- Paginación --}}
                                    <div class="row py-4">
                                        <div class="col d-flex justify-content-center">
                                            {{$docsFirmados->appends(array_merge(request()->query(), ['section' => 'firmados']))->links()}}
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-primary" role="alert">
                                        <strong class="d-block text-center">No se encontraron documentos en esta sección</strong>
                                    </div>
                                @endif
                            </div>

                            {{-- Sellados --}}
                            <div class="{{($seleccion == 'sellados') ? $clase_contenido : 'tab-pane fade'}}" id="nav-validados" role="tabpanel" aria-labelledby="nav-home-tab">
                                @if (count($docsValidados) != 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="negrita">Grupo</th>
                                                    <th scope="col" class="negrita">Clave</th>
                                                    <th scope="col" class="negrita">Nombre del documento</th>
                                                    <th scope="col" class="negrita">Descargar</th>
                                                    <th scope="col" class="negrita">Firmantes</th>
                                                    <th scope="col" class="negrita">Creado</th>
                                                    <th scope="col" class="negrita">Validado</th>
                                                    <th scope="col" class="text-center negrita">Acción</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($docsValidados as $docValidado)
                                                    @php
                                                        $firmantes = [];
                                                        $nameArchivo = '';
                                                        $obj = json_decode($docValidado->obj_documento, true);
                                                        $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                        foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                            // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].', ';
                                                            array_push($firmantes, $value['_attributes']['nombre_firmante']);
                                                        }
                                                        // $firmantes = substr($firmantes, 0, -2);
                                                    @endphp
                                                    <tr>
                                                        <td><p>{{ isset($docValidado->folio_grupo) ? $docValidado->folio_grupo : '' }}</p></td>
                                                        <td><p>{{ isset($docValidado->numero_o_clave) ? $docValidado->numero_o_clave : '' }}</p></td>
                                                        <td><small>{{$nameArchivo}}</small></td>
                                                        <td>
                                                            @switch($docValidado->tipo_archivo)
                                                                @case('Lista de asistencia')
                                                                    @if(!is_null($docValidado->idcursos))
                                                                        <a href="{{route('asistencia-pdf', ['id' => $docValidado->idcursos])}}" target="_blank">
                                                                            <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                        </a>
                                                                    @endif
                                                                @break
                                                                @case('Lista de calificaciones')
                                                                    @if(!is_null($docValidado->idcursos))
                                                                        <a href="{{route('calificacion-pdf', ['id' => $docValidado->idcursos])}}" target="_blank">
                                                                            <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                        </a>
                                                                    @endif
                                                                @break
                                                                @case('Reporte fotografico')
                                                                    @if(!is_null($docValidado->idcursos))
                                                                        <a href="{{route('reportefoto-pdf', ['id' => $docValidado->idcursos])}}" target="_blank">
                                                                            <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                        </a>
                                                                    @endif
                                                                @break
                                                                @case('Solicitud Pago')
                                                                    <a href="{{route('solpa-pdf', ['id' => $docValidado->id_folios])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('supre')
                                                                    @php $ids =  base64_encode($docValidado->id_supre) @endphp
                                                                    <a href="{{route('supre-pdf', ['id' => $ids])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @case('valsupre')
                                                                    @php $ids =  base64_encode($docValidado->id_supre) @endphp
                                                                    <a href="{{route('valsupre-pdf', ['id' => $ids])}}" target="_blank">
                                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                    </a>
                                                                @break
                                                                @default {{-- Contratos --}}
                                                                    @if(!is_null($docValidado->id_contrato))
                                                                        <a href="{{route('contrato-pdf', ['id' => $docValidado->id_contrato])}}" target="_blank">
                                                                            <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                                        </a>
                                                                    @endif
                                                                @break
                                                            @endswitch
                                                            {{-- <button type="button" onclick="descargarDocumento('{{$docValidado->id}}')" class="btn btn-outline-success">Descargar</button> --}}
                                                        </td>
                                                        <td>
                                                            <small>
                                                                @foreach ($firmantes as $item)
                                                                    <p class="my-0">{{$item}}</p>
                                                                @endforeach
                                                            </small>
                                                        </td>
                                                        <td><small>{{$docValidado->created_at->format('d-m-Y')}}</small></td>
                                                        <td><small>{{$docValidado->fecha_sellado}}</small></td>
                                                        @if($rol->role_id == '30' || $rol->role_id == '2' || $rol->role_id == '4' || $rol->role_id == '8')
                                                            <td>
                                                                <button type="button" onclick="cancelarDocumento('{{$docValidado->id}}', '{{$nameArchivo}}', '{{$docValidado->tipo_archivo}}', '{{$docValidado->numero_o_clave}}')" class="btn btn-outline-danger">Anular</button>
                                                            </td>
                                                        @else
                                                            <td></td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Paginación --}}
                                    <div class="row py-4">
                                        <div class="col d-flex justify-content-center">
                                            {{$docsValidados->appends(array_merge(request()->query(), ['section' => 'sellados']))->links()}}
                                        </div>
                                    </div>

                                @else
                                    <div class="alert alert-primary" role="alert">
                                        <strong class="d-block text-center">No se encontraron documentos en esta sección</strong>
                                    </div>
                                @endif
                            </div>

                            {{-- cancelados --}}
                            <div class="{{($seleccion == 'cancelados') ? $clase_contenido : 'tab-pane fade'}}" id="nav-cancelados" role="tabpanel" aria-labelledby="nav-home-tab">
                                @if (count($docsCancelados) != 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="negrita">Nombre del documento</th>
                                                    <th scope="col" class="negrita">Firmantes</th>
                                                    <th scope="col" class="negrita">Creado</th>
                                                    <th scope="col" class="negrita">Cancelado</th>
                                                    <th scope="col" class="negrita">Motivo</th>
                                                    <th scope="col" class="negrita">Canceló</th>
                                                    <th scope="col" class="negrita">Acción</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($docsCancelados as $docCancelado)
                                                    @php
                                                        $firmantes = [];
                                                        $nameArchivo = '';
                                                        $obj = json_decode($docCancelado->obj_documento, true);
                                                        $objCancelado = json_decode($docCancelado->cancelacion, true);
                                                        $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                        foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                            // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].', ';
                                                            array_push($firmantes, $value['_attributes']['nombre_firmante']);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td><small>{{$nameArchivo}}</small></td>
                                                        <td>
                                                            <small>
                                                                @foreach ($firmantes as $item)
                                                                    <p class="my-0">{{$item}}</p>
                                                                @endforeach
                                                            </small>
                                                        </td>
                                                        <td><small>{{$docCancelado->created_at->format('d-m-Y')}}</small></td>
                                                        @if(isset($objCancelado['fecha']))
                                                            <td><small>{{$objCancelado['fecha']}}</small></td>
                                                            <td><small>{{$objCancelado['motivo']}}</small></td>
                                                            <td><small>{{$objCancelado['correo']}}</small></td>
                                                        @endif

                                                        @if($rol->role_id == '30' || $rol->role_id == '2')
                                                            <td>
                                                                @if ($docCancelado->status_recepcion != 'VALIDADO' || $docCancelado->status_recepcion != 'En Espera')
                                                                    @if ($docCancelado->status == 'CANCELADO ICTI')
                                                                        <button class="btn m-0 btn-danger" onclick="anularCancelacion({{$docCancelado->id}})">DESHACER ANULADO</button>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @else
                                                            <td></td>
                                                        @endif

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- Paginación --}}
                                    <div class="row py-4">
                                        <div class="col d-flex justify-content-center">
                                            {{$docsCancelados->appends(array_merge(request()->query(), ['section' => 'cancelados']))->links()}}
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-primary" role="alert">
                                        <strong class="d-block text-center">No se encontraron documentos en esta sección</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalCancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="formCancel" action="{{ route('firma.cancelar') }}" method="post">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="motivo">Motivo de Cancelación</label>
                                    <textarea class="form-control" name="motivo" id="motivo" rows="4"></textarea>
                                </div>
                                <input class="d-none" type="text" name="txtIdCancel" id="txtIdCancel">
                                <input class="d-none" type="text" name="txtTipo" id="txtTipo">
                                <input class="d-none" type="text" name="txtClave" id="txtClave">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-danger">Cancelar Documento</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Rechazo -->
            <div class="modal fade" id="modalRechazo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="formRechazo" action="{{ route('asistencia-xml') }}" method="post">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="rechazoModalLongTitle"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="motivo">Motivo de Rechazo</label>
                                    <textarea class="form-control" name="motivoRechazo" id="motivoRechazo" rows="4"></textarea>
                                </div>
                                <input class="d-none" type="text" name="txtIdRechazo" id="txtIdRechazo">
                                <input class="d-none" type="text" name="txtTipoRechazo" id="txtTipoRechazo">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-danger">Rechazar Documento</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <form id="formValidar"  method="post">
                @csrf
                <input class="d-none" id="txtIdValidado" name="txtIdValidado" type="text">
            </form>

            <form id="formUpdate" action="{{route('firma.update')}}" method="post">
                @csrf
                <input class="d-none" id="fechaFirmado" name="fechaFirmado" type="text">
                <input class="d-none" id="serieFirmante" name="serieFirmante" type="text">
                <input class="d-none" id="firma" name="firma" type="text">
                <input class="d-none" id="curp" name="curp" type="text">
                <input class="d-none" id="idFile" name="idFile" type="text">
                <input class="d-none" id="certificado" name="certificado" type="text">
            </form>

            <form id="formSellar" action="{{route('firma.sellar')}}" method="post">
                @csrf
                <input class="d-none" id="txtIdFirmado" name="txtIdFirmado" type="text">
            </form>

            <form id="formGenerarPDF" action="{{route('firma.generarPdf')}}" method="post">
                @csrf
                <input class="d-none" id="txtIdGenerar" name="txtIdGenerar" type="text">
            </form>
        </div>
    </div>

@endsection

@section('script_content_js')

    {{-- Todos estos links se ocupan en prueba y en producción --}}
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script>

    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sjcl.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha1_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/llave.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/jsbn.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/jsbn2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsa.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsa2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/crypto-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1hex-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsasign-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/x509-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/tripledes_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/aes.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rc2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hex_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/yahoo-min.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hex.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64x-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/x64-core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/tripledes.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/md5.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha256.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/ripemd160.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha512.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/enc-base64.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hmac.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/cipher-core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsapem-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/keyutil-1.js"></script>

    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/forge-0.7.1/forge-0.7.1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/mistake.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/validate.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/access.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/dataSign.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/dataTransportSign.js"></script>
    {{-- Link de producción signature-spv021_doctos --}}
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos.js"></script>

    {{-- link de prueba signature-spv021_doctos-prueba--}}
    {{-- <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos-prueba.js"></script> --}}

    <script>
        var cadena = '', xmlBase64 = '', curp = '', idFile = '';
        $(document).ready(function() {
            $('#btnsignature').attr('onclick', 'firmar();');
        });

        // paginacion
        $(document).on('click', '.pagination a', function(e) {
            loader('show');
        });

        //Boton de buscar y limpiar
        function Buscar(event) {
            event.preventDefault();
            if ($("#txtBusqueda").val() != '') {
                loader('show');
                $('#frmBuscar').attr('action', "{{route('firma.inicio')}}");
                $("#frmBuscar").attr("target", '_self');
                $('#frmBuscar').submit();
            }else{
                alert("INGRESA UNA CLAVE VALIDA");
                return false;
            }
        }

        function Limpiar(event) {
            event.preventDefault();
            loader('show');
            $("#txtBusqueda").val("");
            $('#frmBuscar').attr('action', "{{route('firma.inicio')}}");
            $("#frmBuscar").attr("target", '_self');
            $('#frmBuscar').submit();
        }

        function abriModal(key) {
            $('#vHTMLSignature').removeClass('d-none');
            cadena = $('#cadena'+ key).val();
            // xmlBase64 = $('#xml'+ key).val();
            curp = $('#curp'+ key).val();
            idFile = $('#idFile' + key).val();
        }

        function firmar() {
            var response = firmarDocumento($('#token').val());
            console.log(response)
            if(response.codeResponse == '401') {
                generarToken().then((value) => {
                    response = firmarDocumento(value);
                    continueProcess(response);
                }).catch((error) => {
                    continueProcess(response);
                });
            } else {
                continueProcess(response);
            }
        }

        function firmarDocumento(token) {
            var vresponseSignature = sign(cadena, curp, $('#txtpassword').val(), '30', token);
            // el sistema 87 es el de produccion 30 es de pruebas
            console.log(curp)
            return vresponseSignature;
        }

        function generarToken() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/firma/token') }}",
                    data: {
                        'nombre': '',
                        'key': '',
                        '_token': $("meta[name='csrf-token']").attr("content"),
                    },
                    success: function(result) {
                        resolve(result);

                    },
                    error: function(jqXHR, textStatus) {
                        reject('error');
                    }
                });
            })
        }

        function continueProcess(response) {
            if (response.statusResponse) {
                $('#fechaFirmado').val(response.date);
                $('#serieFirmante').val(response.certifiedSeries)
                $('#firma').val(response.sign);
                $('#curp').val(curp);
                $('#certificado').val(response.certificated)
                $('#idFile').val(idFile);
                $('#formUpdate').submit();
            } else {
                confirm(response.messageResponse + ' ' + response.descriptionResponse)
                location.reload;
            }
        }

        function sellardocumento(id) {
            if (confirm("¿Está seguro de enviar a validación el documento?") == true) {
                $('#txtIdFirmado').val(id);
                $('#formSellar').submit();
            }
        }

        function descargarDocumento(id) {
            $('#txtIdGenerar').val(id);
            $('#formGenerarPDF').submit();
        }

        function cancelarDocumento(id, name, tipo, clave) {
            $('#exampleModalLongTitle').html('Cancelar ' + name);
            $('#txtIdCancel').val(id);
            $('#txtTipo').val(tipo);
            $('#txtClave').val(clave);
            $('#modalCancel').modal('toggle');
        }

        function validarDocumento(id, tipo) {
            if (confirm("¿Está seguro de validar el documento?") == true) {
                $('#txtIdValidado').val(id);
                if(tipo == 'asistencia') {
                    $('#formValidar').attr('action', '/asistencia/validar');
                } else if(tipo == 'calificacion') {
                    $('#formValidar').attr('action', '/calificacion/validar');
                }else if(tipo == 'reportefoto'){ //By Jose Luis
                    $('#formValidar').attr('action', '/reportefoto/validar');
                }
                $('#formValidar').submit();
            }
        }

        function rechazarDocumento(id, tipo) {
            $('#rechazoModalLongTitle').html('Rechazar Lista de ' + tipo);
            $('#txtIdRechazo').val(id);
            $('#txtTipoRechazo').val(tipo);
            if(tipo == 'asistencia') {
                $('#formRechazo').attr('action', '/asistencia/rechazo');
            } else if(tipo == 'calificacion'){
                $('#formRechazo').attr('action', '/calificacion/rechazo');
            }else if(tipo == 'reportefoto'){ //By Jose Luis
                $('#formRechazo').attr('action', '/reportefoto/rechazo');
            }
            $('#modalRechazo').modal('toggle');
        }

        function anularCancelacion(id_efirma) {
            let data = {
                "_token": $("meta[name='csrf-token']").attr("content"),
                "id_efirma": id_efirma }
            $.ajax({
                type:"post",
                url: "{{ route('efirma.deshacer.anulado') }}",
                data: data,
                dataType: "json",
                success: function (response) {
                    if (response.status == 200) {
                        location.reload();
                    }
                }
            });
        }

        //Cambiar de seccion e implementar loader
        function cambiarSection(status) {
            $("#seccion").val(status);
            let queryParams = window.location.search;
            let bustxtBus = queryParams.indexOf("?txtBusqueda");
            let buspage = queryParams.indexOf("page");

            console.log(buspage);
            if(queryParams != '' && bustxtBus != 0 && buspage != -1){
                loader('show');
                // window.location.href = window.location.origin + window.location.pathname;
                var nuevaURL = window.location.origin + window.location.pathname;
                nuevaURL += '?seccion=' + status;
                window.location.href = nuevaURL;
            }
        }

        function loader(make) {
            if(make == 'hide') make = 'none';
            if(make == 'show') make = 'block';
            document.getElementById('loader-overlay').style.display = make;
        }

    </script>

@endsection
