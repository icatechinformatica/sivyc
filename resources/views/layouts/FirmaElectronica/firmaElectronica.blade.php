@extends("theme.sivyc.layout")
@section('title', 'FIRMAR ELECTRONICAMENTE | Sivyc Icatech')

@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        .colorTop {
            background-color: #541533;
        }
        .negrita{
            font-weight: bold;
        }

        a.border-left {
            border-color: #5e5b5b; /* gris claro */
        }

        /* Estilos de las pestañas */
       .nav-tabs .nav-link {
            border: none;
            border-radius: 20px;
            margin: 0 5px;
            transition: background 0.3s ease, color 0.3s ease;
            color: #444; /* color de texto normal */
            /*fondo normal*/
            /* background-color: #f1f1f1;  */
        }

        .nav-tabs .nav-link.active {
            background-color: #717377 !important; /* fondo activo */
            color: #fff !important; /* texto en blanco para contraste */
        }

        .nav-tabs .nav-link:hover {
            background-color: #ccced1 !important; /* fondo hover */
            color: #fff; /* opcional, para que el texto se vea en hover */
        }

        /* Dimension del check */
        .form-check-input{
            width:22px;
            height:22px;
        }


        .modal_del {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }



    </style>

    {{-- links de prueba viejos y de produccion --}}
    <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" />

    {{-- Nuevos Links para firma electronica --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" /> --}}
    {{-- Producción --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic/css/firma.css "> --}}
    {{-- Prueba --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic-prueba/css/firma.css"> --}}

@endsection

@section('content')
    <div class="d-none" id="vHTMLSignature"></div>
    <input class="d-none" id="token" name="token" type="text" value="{{$token}}">
    <input type="hidden" value="{{ $curpUser->curp ?? '' }}" id="curp_usuario">

    <div class="card-header">
        Documentos Electrónicos
    </div>

    <div class="card card-body">

        {{-- SECCIÓN DE MENSAJES --}}
        <div class="row">
            <div class="col">
                @if ($message = Session::get('warning'))
                    <div class="alert alert-warning">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                @if ($message = Session::get('success'))
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
        {{-- FIN SECCIÓN DE MENSAJES --}}


        {{-- SECCIÓN DE BUSQUEDA --}}
        <div class="row">
            <div class="col-6">
                <form action="" id="frmBuscar" method="get" class="d-flex align-items-center">
                    <label for="txtBusqueda" class="sr-only">Clave de curso</label>
                    <input type="text"
                        class="form-control"
                        placeholder="BUSQUEDA POR CLAVE DE CURSO"
                        name="txtBusqueda"
                        id="txtBusqueda"
                        value="{{ $busqueda_clave ?? '' }}">

                    <input type="hidden" name="seccion" id="seccion">

                    <a href="#"
                        onclick="Buscar(event)"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="Buscar">
                            <i class="fa fa-2x fa-search text-success ml-3 mr-2" aria-hidden="true"></i>
                            <span class="sr-only">Buscar</span>
                    </a>
                    <a href="#"
                        onclick="Limpiar(event)"
                        class="pl-2 border-left"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="Limpiar">
                        <i class="fa fa-2x fa-trash text-danger ml-2" aria-hidden="true"></i>
                        <span class="sr-only">Limpiar</span>
                    </a>
                </form>

            </div>

            <div class="col-6 d-flex justify-content-end">
                @if(isset($curpUser))
                    <span class="badge p-2 shadow-lg" style="font-size: 1rem;">
                        <p class="text-dark py-0"><strong>CURP:</strong> {{ $curpUser->curp }}</p>
                    </span>
                @endif
            </div>

        </div>
        {{-- FIN SECCIÓN DE BUSQUEDA --}}

        <div style="height: 2px; background: linear-gradient(90deg, transparent, #4a90e2, transparent); margin: 20px 0;"></div>

        {{-- CUERPO --}}
        <div>
            <div class="col-12">
                {{-- SECCIÓN DE PESTAÑAS --}}
                <div class="row">
                    <div class="col-5">
                        <nav>
                            @php
                                $clase_pestaña = 'nav-item nav-link active';
                                $clase_contenido = 'tab-pane fade show active';
                            @endphp
                            <div class="nav nav-tabs nav-fill shadow-sm rounded" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link {{ ($seleccion == null || $seleccion == 'por_firmar') ? 'active' : '' }} font-weight-bold text-primary"
                                id="nav-home-tab"
                                data-toggle="tab"
                                href="#nav-home"
                                role="tab"
                                aria-controls="nav-home"
                                aria-selected="{{ ($seleccion == null || $seleccion == 'por_firmar') ? 'true' : 'false' }}"
                                onclick="cambiarSection('por_firmar')">
                                    Por Firmar
                                </a>

                                <a class="nav-item nav-link {{ ($seleccion == 'firmados') ? 'active' : '' }} font-weight-bold text-success"
                                id="nav-firmados-tab"
                                data-toggle="tab"
                                href="#nav-firmados"
                                role="tab"
                                aria-controls="nav-firmados"
                                aria-selected="{{ ($seleccion == 'firmados') ? 'true' : 'false' }}"
                                onclick="cambiarSection('firmados')">
                                    Firmados
                                </a>

                                <a class="nav-item nav-link {{ ($seleccion == 'sellados') ? 'active' : '' }} font-weight-bold text-info"
                                id="nav-validados-tab"
                                data-toggle="tab"
                                href="#nav-validados"
                                role="tab"
                                aria-controls="nav-validados"
                                aria-selected="{{ ($seleccion == 'sellados') ? 'true' : 'false' }}"
                                onclick="cambiarSection('sellados')">
                                    Sellados
                                </a>

                                <a class="nav-item nav-link {{ ($seleccion == 'cancelados') ? 'active' : '' }} font-weight-bold text-danger"
                                id="nav-cancelados-tab"
                                data-toggle="tab"
                                href="#nav-cancelados"
                                role="tab"
                                aria-controls="nav-cancelados"
                                aria-selected="{{ ($seleccion == 'cancelados') ? 'true' : 'false' }}"
                                onclick="cambiarSection('cancelados')">
                                    Cancelados
                                </a>
                            </div>
                        </nav>
                    </div>
                    <div class="col-7 text-right">
                        {{-- botones de acción masiva --}}
                        <div class="row justify-content-end align-items-center">
                            <div id="cantidadSeleccionados" class="font-weight-bold mr-5">
                                Documentos Seleccionados: <span id="contador">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLAS CON CONTENIDO --}}
                <div class="tab-content mt-4" id="nav-tabContent">
                    {{-- Por Firmar --}}
                    <div class="{{($seleccion == null || $seleccion == 'por_firmar') ? $clase_contenido : 'tab-pane fade'}}" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        @if (count($docsFirmar) != 0)
                            <div class="table-responsive">
                                {{-- boton de firmar --}}
                                <div id="content_firmar" class="d-none text-right mb-3">
                                    <button class="btn btn-md btn-primary" href="#" data-toggle="modal" data-target="#mdlLoadViewSignature" id="btn_firmar" onclick="abrirModal()"></button>
                                </div>
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col" class="negrita">Seleccionar</th>
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
                                                $visibleDta = true; // para control de DTA y que solo vea contratos firmados por todosy solo falte el director
                                                $obj = json_decode($docFirmar->obj_documento, true);
                                                if(!isset($obj['archivo'])) {dd($docFirmar);}
                                                $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                    // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].'(), ';
                                                    if($value['_attributes']['email_firmante'] == $email){
                                                        $curp = $value['_attributes']['curp_firmante'];
                                                    }

                                                    if(empty($value['_attributes']['firma_firmante'])){
                                                        // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (NO), ';
                                                        array_push($firmantes, $value['_attributes']['nombre_firmante'].' (NO)');
                                                        if($rol->role_id == 22 && $value['_attributes']['curp_firmante'] != $curpUser->curp) { // visible data cambia a falso cuando alguien de los contratos no ha firmado, exceptuando al Director de dTA
                                                            $visibleDta = false;
                                                        }
                                                    } else {
                                                        // $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (SI), ';
                                                        array_push($firmantes, $value['_attributes']['nombre_firmante'].' (SI)');
                                                    }
                                                }
                                                // $firmantes = substr($firmantes, 0, -2);
                                            @endphp
                                            @if($visibleDta == true)
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="chkSeleccionar{{$key}}" id="chkSeleccionar{{$key}}" class="form-check-input chkSeleccionar" value="{{ $docFirmar->id }}">
                                                    </td>
                                                    <td class="font-weight-bold">{{ isset($docFirmar->folio_grupo) ? $docFirmar->folio_grupo : '' }}</td>
                                                    <td class="font-weight-bold">{{ !in_array($docFirmar->tipo_archivo, ['supre','valsupre']) ? $docFirmar->numero_o_clave : $docFirmar->folio_validacion }}</td>
                                                    <td class="font-weight-bold">{{$nameArchivo}}</td>
                                                    <td class="text-center">
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
                                                                <p class="my-0 font-weight-bold">{{$item}}</p>
                                                            @endforeach
                                                        </small>
                                                    </td>
                                                    <td class="font-weight-bold">{{$docFirmar->created_at->format('d-m-Y')}}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-md btn-outline-danger" type="button" onclick="cancelarDocumento('{{$docFirmar->id}}', '{{$nameArchivo}}', '{{$docFirmar->tipo_archivo}}', '{{$docFirmar->numero_o_clave}}')">Cancelar</button>
                                                    </td>

                                                    {{-- <input class="d-none" value="{{$docFirmar->id}}" name="idFile{{$key}}" id="idFile{{$key}}" type="text">
                                                    <input class="d-none" value="{{$docFirmar->cadena_original}}" name="cadena{{$key}}" id="cadena{{$key}}" type="text">
                                                    <input class="d-none" value="{{$docFirmar->base64xml}}" name="xml{{$key}}" id="xml{{$key}}" type="text">
                                                    <input class="d-none" value="{{$curpUser->curp}}" name="curp{{$key}}" id="curp{{$key}}" type="text"> --}}
                                                </tr>
                                            @endif
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
                            @if($curpUser->curp == 'N/A')
                                <div class="alert alert-danger" role="alert">
                                    <strong class="d-block text-center">ERROR: No se ha encontrado una CURP relacionada a este usuario</strong>
                                </div>
                            @else
                                <div class="alert alert-primary" role="alert">
                                    <strong class="d-block text-center">No se encontraron documentos en esta sección</strong>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Firmados --}}
                    <div class="{{($seleccion == 'firmados') ? $clase_contenido : 'tab-pane fade'}}" id="nav-firmados" role="tabpanel" aria-labelledby="nav-home-tab">
                        @if (count($docsFirmados) != 0)
                            <div class="table-responsive">
                                {{-- boton sellar --}}
                                <div id="content_sellar" class="d-none text-right mb-3">
                                    <button class="btn btn-md btn-danger" id="btn_sellar" data-toggle="modal" data-target="#modalsellar"></button>
                                </div>
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col" class="negrita">Seleccionar</th>
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
                                                <td class="text-center">
                                                    @can('efirma.sellar')
                                                        @if ($sendValidation)
                                                            <input type="checkbox" name="chkSellar" id="chkSellar" class="form-check-input chkSellar" value="{{ $docFirmado->id }}">
                                                        @else
                                                            <input type="checkbox" disabled class="form-check-input" title="Faltan Firmas">
                                                        @endif
                                                    @endcan
                                                </td>
                                                <td class="font-weight-bold">{{ isset($docFirmado->folio_grupo) ? $docFirmado->folio_grupo : '' }}</td>
                                                <td class="font-weight-bold">{{ !in_array($docFirmado->tipo_archivo, ['supre','valsupre']) ? $docFirmado->numero_o_clave : $docFirmado->folio_validacion }}</td>
                                                <td class="font-weight-bold">{{$nameArchivo}}</td>
                                                <td class="text-center">
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
                                                            <p class="my-0 font-weight-bold">{{$item}}</p>
                                                        @endforeach
                                                    </small>
                                                </td>
                                                <td class="font-weight-bold">{{$docFirmado->created_at->format('d-m-Y')}}</td>
                                                @if($rol->role_id == '30' || $rol->role_id == '2' || $rol->role_id == '8' || $rol->role_id == '4')
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-md btn-outline-danger" onclick="cancelarDocumento('{{$docFirmado->id}}', '{{$nameArchivo}}', '{{$docFirmado->tipo_archivo}}', '{{$docFirmado->numero_o_clave}}')" class="btn btn-outline-danger">Cancelar</button>
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
                                    <thead class="thead-light">
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
                                                <td class="font-weight-bold">{{ isset($docValidado->folio_grupo) ? $docValidado->folio_grupo : '' }}</td>
                                                <td class="font-weight-bold">{{ !in_array($docValidado->tipo_archivo, ['supre','valsupre']) ? $docValidado->numero_o_clave : $docValidado->folio_validacion }}</td>
                                                <td class="font-weight-bold">{{$nameArchivo}}</td>
                                                <td class="text-center">
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
                                                            <p class="my-0 font-weight-bold">{{$item}}</p>
                                                        @endforeach
                                                    </small>
                                                </td>
                                                <td class="font-weight-bold">{{$docValidado->created_at->format('d-m-Y')}}</td>
                                                <td class="font-weight-bold">{{$docValidado->fecha_sellado}}</td>
                                                @if($rol->role_id == '30' || $rol->role_id == '2' || $rol->role_id == '4' || $rol->role_id == '8')
                                                    <td>
                                                        <button type="button" class="btn btn-md btn-outline-danger" onclick="cancelarDocumento('{{$docValidado->id}}', '{{$nameArchivo}}', '{{$docValidado->tipo_archivo}}', '{{$docValidado->numero_o_clave}}')" class="btn btn-outline-danger">Anular</button>
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
                                    <thead class="thead-light">
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
                                                <td class="font-weight-bold">{{$nameArchivo}}</td>
                                                <td>
                                                    <small>
                                                        @foreach ($firmantes as $item)
                                                            <p class="my-0 font-weight-bold">{{$item}}</p>
                                                        @endforeach
                                                    </small>
                                                </td>
                                                <td><small class="font-weight-bold">{{$docCancelado->created_at->format('d-m-Y')}}</small></td>
                                                @if(isset($objCancelado['fecha']))
                                                    <td><small class="font-weight-bold">{{$objCancelado['fecha']}}</small></td>
                                                    <td><small class="font-weight-bold">{{$objCancelado['motivo']}}</small></td>
                                                    <td><small class="font-weight-bold">{{$objCancelado['correo']}}</small></td>
                                                @endif

                                                @if($rol->role_id == '30' || $rol->role_id == '2')
                                                    <td>
                                                        @if ($docCancelado->status_recepcion != 'VALIDADO' || $docCancelado->status_recepcion != 'En Espera')
                                                            @if ($docCancelado->status == 'CANCELADO ICTI')
                                                                <button class="btn btn-md btn-outline-danger" onclick="anularCancelacion({{$docCancelado->id}})">DESHACER ANULADO</button>
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

            <!-- Modal -->
            <div class="modal fade" id="modalCancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="formCancel" action="{{ route('firma.cancelar') }}" method="post">
                            @csrf
                            <div class="modal-header" style="background-color: #343a40; color:#fff">
                                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" class="text-white">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="motivo">Motivo de Cancelación</label>
                                    <textarea class="form-control" name="motivo" id="motivo" rows="4" placeholder="Escribe el motivo de la cancelación del documento"></textarea>
                                </div>
                                <input class="d-none" type="text" name="txtIdCancel" id="txtIdCancel">
                                <input class="d-none" type="text" name="txtTipo" id="txtTipo">
                                <input class="d-none" type="text" name="txtClave" id="txtClave">
                            </div>
                            <div class="modal-footer justify-content-center border-0 pb-4">
                                <button type="submit" class="btn btn-danger px-4"><i class="fas fa-check-circle mr-2"></i>Cancelar Documento</button>
                                <button type="button" class="btn px-4" data-dismiss="modal" style="background-color: #343a40; color:#fff"><i class="fas fa-times-circle mr-2"></i>Cerrar</button>
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

            {{-- <form id="formUpdate" action="{{route('firma.update')}}" method="post">
                @csrf
                <input class="d-none" id="fechaFirmado" name="fechaFirmado" type="text">
                <input class="d-none" id="serieFirmante" name="serieFirmante" type="text">
                <input class="d-none" id="firma" name="firma" type="text">
                <input class="d-none" id="curp" name="curp" type="text">
                <input class="d-none" id="idFile" name="idFile" type="text">
                <input class="d-none" id="certificado" name="certificado" type="text">
            </form> --}}

            {{-- Nuevo formulario para el envio de datos de firma --}}
            <form id="formUpdfirm" action="{{route('firma.update.masivo')}}" method="post">
                @csrf
                <input type="hidden" name="respuesta" id="respuesta">
                <input type="hidden" name="curp" id="curp">
                <input type="hidden" name="correctos" id="correctos">
                <input type="hidden" name="errores" id="errores">
                <input type="hidden" name="mensaje" id="mensaje">
            </form>

            {{-- Nuevo formulario para el sellado masivo --}}
            <form id="formSellarMasivo" action="{{route('firma.sellar.masivo')}}" method="post">
                @csrf
                <input type="hidden" name="ids_sellar" id="ids_sellar">
            </form>

            {{-- Nuevo modal para sellado --}}
            <div class="modal fade" id="modalsellar" tabindex="-1" role="dialog" aria-labelledby="modalsellar" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        {{-- encabezado --}}
                        <div class="modal-header" style="background-color: #343a40; color:#fff">
                            <p class="modal-title font-weight-bold text-center" style="font-size:18px;" id="">¡Mensaje!</p>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-white">&times;</span>
                            </button>
                        </div>
                        {{-- cuerpo --}}
                        <div class="modal-body text-center py-4">
                            <p class="mb-0" style="font-size: 16px;">¿Deseas sellar los documentos seleccionados?</p>
                        </div>
                        {{-- pie --}}
                        <div class="modal-footer justify-content-center border-0 pb-4">
                            <button type="button" class="btn btn-danger px-4" onclick="sellar_documentos()"><i class="fas fa-check-circle mr-2"></i>Aceptar</button>
                            <button type="button" class="btn px-4" data-dismiss="modal" style="background-color: #343a40; color:#fff"><i class="fas fa-times-circle mr-2"></i>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mensaje de alerta --}}
            <div class="modal fade" id="modalalerta" tabindex="-1" role="dialog" aria-labelledby="modalalerta" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-top" role="document">
                    <div class="modal-content">
                        {{-- encabezado --}}
                        <div class="modal-header" style="background-color: #343a40; color:#fff">
                            <p class="modal-title font-weight-bold text-center" style="font-size:18px;" id="">¡Mensaje!</p>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-white">&times;</span>
                            </button>
                        </div>
                        {{-- cuerpo --}}
                        <div class="modal-body text-center py-4">
                            <p class="mb-0" style="font-size: 16px;" id="mensaje_alerta"></p>
                        </div>
                        {{-- pie --}}
                        <div class="modal-footer justify-content-center border-0 pb-4">
                            <button type="button" class="btn px-4" data-dismiss="modal" style="background-color: #343a40; color:#fff"><i class="fas fa-times-circle mr-2"></i>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Formulario para descargar PDF --}}
            <form id="formGenerarPDF" action="{{route('firma.generarPdf')}}" method="post">
                @csrf
                <input class="d-none" id="txtIdGenerar" name="txtIdGenerar" type="text">
            </form>
        </div>
    </div>

@endsection

@section('script_content_js')

    <!-- Importamos FirmaService -->
    <script src="{{ asset('js/firma_electronica/FirmaService.js') }}"></script>

    {{-- Todos estos links se ocupan en prueba y en producción --}}
    {{-- <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js"></script> --}}

    {{-- links viejos --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js"></script>

    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sjcl.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha1_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/llave.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/jsbn.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/jsbn2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsa.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsa2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/base64_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/crypto-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/asn1hex-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsasign-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/x509-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/tripledes_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/aes.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rc2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/asn1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/base64.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/hex_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/yahoo-min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/hex.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/base64x-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/x64-core.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/tripledes.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/core.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/md5.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha256.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/ripemd160.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha512.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/enc-base64.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/hmac.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/cipher-core.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/asn1-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsapem-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/keyutil-1.js"></script>

    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/forge-0.7.1/forge-0.7.1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/mistake.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/validate.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/access.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/dataSign.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/dataTransportSign.js"></script>

    {{-- Para el envio por arreglo --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/ChainTransport.js"></script>

    {{-- Links nuevos de firma electronica --}}

    {{-- Nueva configuracion de producción --}}
    {{-- <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic/js/firmado_produccion.js"></script> --}}

    {{-- Nueva configuración de prueba--}}
    {{-- <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic-prueba/js/firmado_prueba.js"></script> --}}


    {{-- Links viejos --}}

    {{-- Link de producción signature-spv021_doctos --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos.js"></script>

    {{-- link de prueba signature-spv021_doctos-prueba--}}
    {{-- <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos-prueba.js"></script> --}}


    <script>
        var cadena = '', xmlBase64 = '', curp = '', idFile = '';
        $(document).ready(function() {
            //Firma electronica
            $('#btnsignature').attr('onclick', 'boton_firmar();');
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

        //Abrir modal alerta
        function modal_alerta(accion, texto) {
            const mensaje = document.getElementById('mensaje_alerta');

            switch (accion) {
                case 'show':
                    // modal.style.display = 'block';
                    $('#modalalerta').modal('show');
                    mensaje.innerText = texto;
                    break;
                case 'hide':
                    // modal.style.display = 'none';
                    $('#modalalerta').modal('hide');
                    mensaje.innerText = '';
                    break;
                default:
                    console.warn('loader() solo acepta "show" o "hide"');
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

        function descargarDocumento(id) {
            $('#txtIdGenerar').val(id);
            $('#formGenerarPDF').submit();
        }

        function cancelarDocumento(id, name, tipo, clave) {
            $('#exampleModalLongTitle').html('Cancelar ' + name);
            $('#txtIdCancel').val(id);
            $('#txtTipo').val(tipo);
            $('#txtClave').val(clave);
            // $('#modalCancel').modal('toggle');
            const modalElement = document.getElementById('modalCancel');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
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

            // console.log(buspage);
            if(queryParams != '' && bustxtBus != 0 && buspage != -1){
                loader('show');
                // window.location.href = window.location.origin + window.location.pathname;
                var nuevaURL = window.location.origin + window.location.pathname;
                nuevaURL += '?seccion=' + status;
                window.location.href = nuevaURL;
            }
        }

        //////// NUEVO CODIGO ESTRUCTURADO PARA FIRMADO MASIVO ////////////

        function abrirModal() {
            $('#vHTMLSignature').removeClass('d-none');
        }

        //CODIGO PARA EL CONTEO DE CHECKBOX PARA EL SELLADO MASIVO
        var arrayCadenasG = [];
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionamos todos los checkboxes
            const checkboxes = document.querySelectorAll('.chkSeleccionar');

            checkboxes.forEach(chk => {
                chk.addEventListener('change', function() {
                    const id = this.value; // Usamos el value como ID

                    if (this.checked) {

                        // Validar máximo de 10 selecciones
                        if (arrayCadenasG.length >= 10) {
                            // alert("No se permite seleccionar más de 10 documentos.");
                            modal_alerta('show', 'No se permite seleccionar más de 10 documentos.');
                            this.checked = false; // desmarcar el checkbox
                            return; // salir sin agregarlo
                        }

                        // Agregar al array si se selecciona
                        if (!arrayCadenasG.includes(id)) {
                            arrayCadenasG.push(id);
                        }
                    } else {
                        // Quitar del array si se deselecciona
                        arrayCadenasG = arrayCadenasG.filter(item => item !== id);
                    }

                    // Actualizar contador en pantalla
                    document.getElementById('contador').innerText = arrayCadenasG.length;

                    //Actualizar texto de los botones
                    if(arrayCadenasG.length > 0) {
                        document.getElementById('btn_firmar').innerText = 'Firmar (' + arrayCadenasG.length + ')';
                        $('#content_firmar').removeClass('d-none');
                    }else{
                        $('#content_firmar').addClass('d-none');
                    }

                    // console.log(arrayCadenasG);
                });
            });
        });

        //Funcion para obtener las cadenas originales desde el backend
        async function obtenerCadenasOriginales(ids) {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const resp = await fetch('/firma/cadenas', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
                },
                body: JSON.stringify({ ids }) // { "ids": [1,2,3] }
            });

            if (!resp.ok) {
                const txt = await resp.text().catch(() => '');
                throw new Error(`Error ${resp.status}: ${txt || 'No se pudo obtener cadenas.'}`);
            }

            const data = await resp.json();
            if (!data || data.status !== 200 || !data.cadena_original) {
                throw new Error(data && data.mensaje ? data.mensaje : 'Respuesta inválida del servidor.');
            }

            // data.cadena_original = { "12": "||...||", "18": "||...||", ... }
            return data.cadena_original;
        }


        // Funcion para construir ChainTransport[]
        function generarArray(array_cadena) {
            let chainTransports = [];
            Object.entries(array_cadena).forEach(([clave, valor]) => {
                let nuevaInstancia = new ChainTransport();
                nuevaInstancia.Dataid_cadenaOriginal = clave; // id
                nuevaInstancia.DatacadenaOriginal   = valor; // cadena texto
                chainTransports.push(nuevaInstancia);
            });
            return chainTransports;
        }


        //Llamar al servicio nuevo de firmado electronico
        async function boton_firmar() {
            loader('show');
            const tokenInicial = $('#token').val();
            const curpUser = $('#curp_usuario').val();
            const password = $('#txtpassword').val();

            //Antes de llamar al servicio estructuramos el array de cadenas
            if (!Array.isArray(arrayCadenasG) || arrayCadenasG.length === 0) {
                alert('No hay documentos seleccionados para firmar.');
            return;
            }

            // 1) pedir cadenas originales por ids
            const mapaCadenas = await obtenerCadenasOriginales(arrayCadenasG);

            // 2) transformar a ChainTransport[]
            const chainTransports = generarArray(mapaCadenas);

            // Llamamos al servicio
            const resultado = await FirmaService.iniciarFirma(chainTransports, curpUser, password, tokenInicial);

            if (resultado.success) {
                // Aquí decides si mandas al backend
                $('#respuesta').val(JSON.stringify(resultado.respuesta));
                $('#correctos').val(resultado.correctos);
                $('#curp').val(resultado.curp);
                $('#errores').val(resultado.errores);
                $('#mensaje').val(resultado.message);
                $('#formUpdfirm').submit();

            } else {
                loader('hide');
                console.warn("Hubo errores en el firmado:", resultado.message);
                alert("Error en el proceso de firmado intente de nuevo:\n" + resultado.message);
                // modal_alerta('show', "Error en el proceso de firmado intente de nuevo:\n" + resultado.message);
                location.reload();
            }
        }


        //CODIGO PARA EL CONTEO DE CHECKBOX PARA EL SELLADO MASIVO
        var idsSeleccionados = [];
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionamos todos los checkboxes
            const checkboxes = document.querySelectorAll('.chkSellar');

            checkboxes.forEach(chk => {
                chk.addEventListener('change', function() {
                    const id = this.value; // Usamos el value como ID

                    if (this.checked) {

                        // Validar máximo de 10 selecciones
                        if (idsSeleccionados.length >= 10) {
                            // alert("No se permite seleccionar más de 10 documentos.");
                            modal_alerta('show', 'No se permite seleccionar más de 10 documentos.');
                            this.checked = false; // desmarcar el checkbox
                            return; // salir sin agregarlo
                        }

                        // Agregar al array si se selecciona
                        if (!idsSeleccionados.includes(id)) {
                            idsSeleccionados.push(id);
                        }
                    } else {
                        // Quitar del array si se deselecciona
                        idsSeleccionados = idsSeleccionados.filter(item => item !== id);
                    }

                    // Actualizar contador en pantalla
                    document.getElementById('contador').innerText = idsSeleccionados.length;

                    //Actualizar texto de los botones
                    if(idsSeleccionados.length > 0) {
                        document.getElementById('btn_sellar').innerText = 'Sellar (' + idsSeleccionados.length + ')';
                        $('#content_sellar').removeClass('d-none');
                    }else{
                        $('#content_sellar').addClass('d-none');
                    }
                });
            });
        });

        //Funcion para sellar masivo
        function sellar_documentos() {
            if (idsSeleccionados.length > 0) {
                loader('show');
                let idsJSON = JSON.stringify(idsSeleccionados);
                $('#ids_sellar').val(idsJSON);
                $('#formSellarMasivo').submit();
            }else{
                // $("#error_sellado").html("¡No existen datos para procesar el sellado de documentos! Intente de nuevo");
                // alert("No hay documentos seleccionados para sellar.");
                modal_alerta('show', 'No hay documentos seleccionados para sellar.');
            }
        }

    </script>

@endsection
