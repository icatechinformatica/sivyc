<!--Creado por Jose Luis Moreno Arcos luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Expediente Unico | SIVyC Icatech')
    <!--seccion-->

@section('content')
    <style>
        * {
            box-sizing: border-box;
        }
        .card-header{
            font-variant: small-caps;
            background-color: #621132;
            color: white;
            margin: 1.7% 1.7% 1% 1.7%;
            padding: 1.3% 39px 1.3% 39px;
            font-style: normal;
            font-size: 22px;
        }

        .card-body{
            margin: 1%;
            margin-left: 1.7%;
            margin-right: 1.7%;
            /* padding: 55px; */
            -webkit-box-shadow: 0 8px 6px -6px #999;
            -moz-box-shadow: 0 8px 6px -6px #999;
            box-shadow: 0 8px 6px -6px #999;
        }
        .card-body.card-msg{
            background-color: yellow;
            margin: .5% 1.7% .5% 1.7%;
            padding: .5% 5px .5% 25px;
        }

        body { background-color: #E6E6E6; }

        .btn, .btn:focus{ color: white; background: #12322b; font-size: 14px; border-color: #12322b; margin: 0 5px 0 5px; padding: 10px 13px 10px 13px; }
        .btn:hover { color: white; background:#2a4c44; border-color: #12322b; }

        .form-control { height: 40px; }

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
        table {
            width: 100%;
            border: 2px solid #b8b5b5;
        }

        th {
            background-color: #621132;
            color: #ffff;
            font-weight: bolder !important;
            text-align: center;
        }

        td {
            padding: 10px;
            color: #000;
        }

        .table-hover tbody tr {
            border-bottom: 1px solid #dee2e6; /* Línea de separación */
        }

        .titulo_tabla{
            background-color: #621132;
            color: #fff;
            font-weight:bold;
            padding:2px;
            border: 1px solid #000;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        /* Agregamos borde al contenedor de datos del curso */
        .datos-curso{
            /* border: 1px solid #000; */
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        /* CSS personalizado para un checkbox más grande */
        .form-check-input[type="radio"] {
            width: 1.5rem;
            height: 1.5rem;
        }
        /* Se usa para bloquear los campos donde el user no le corresponde */
        .blocked {
            pointer-events: none;
            opacity: 0.5;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        /* ajustar textarea */
        textarea {
            height: 40px;
        }

        /* Color de icono pdf cuando no existe */
        .text-gray{
            color: #adabab;
        }

        /* Modal para sugerencias */
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

        /* Modal para mostrar lista de alumnos */
        .modal_al {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
            background: rgba(0,0,0,0.5);
        }
        /* Clase del modal de alumnos */
        .modal-content {
            position: relative;
            margin: auto;
            padding: 0;
            width: 60%;
            max-width: 800px;
            height: 80%;
            overflow: hidden;
            background: #fff;
        }

        /* Clase del modal de alerta de archivo no existente */
        .modal-content2 {
            background-color: #fff;
            margin: 15% auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 30%;
            padding-left: 10px;
            padding-top: 7px;
            margin-top: 10%;
            height: 18%;
        }

        .modal-body {
            height: 60%;
        }

        .scrollable-list {
            max-height: 100%;
            overflow-y: auto;
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

        /* input para carga de archivos alumnos */
        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-icon {
            font-size: 16px;
            cursor: pointer;
            color: #007bff;
            transition: color 0.3s;
        }

        .file-icon.loaded {
            color: #28a745;
        }
    </style>

    <div class="card-header py-2">
        <h3>Expediente Unico</h3>
    </div>

    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    {{-- Card como contenedor --}}
    <div class="card card-body">
        {{-- style=" min-height:450px;"  para darle altura vertical --}}
        {{-- Mensaje de alerta --}}
        @if (session()->has('message') && session()->has('status'))
            <div class="alert alert-{{ session('status') }} alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ session('message') }}</strong>
            </div>
        @endif

        {{-- Alerta de retorno --}}
        @if (isset($array_rol['rol']))
            @if (($array_rol['rol'] == 1 || $array_rol['rol'] == 2 || $array_rol['rol'] == 3))
                <div class="col-12 justify-content-center d-flex align-items-start px-0">
                    @if ($array_rol['status_json'] == 'RETORNADO')
                        <div class="alert alert-warning" role="alert"><b>El Expediente Unico fue retornado, verifique los mensajes de DTA para mas detalles..</b><br></div>
                    @endif
                    @if ($array_rol['status_json'] == 'VALIDADO')
                        <div class="alert alert-success" role="alert"><b>Expediente Unico validado por DTA</b><br></div>
                    @endif
                    {{-- <b>Nota:</b> {{$array_rol['message_return']}} --}}
                </div>
            @endif
        @endif


        {{-- CAJA DE BUSQUEDA --}}
        <div class="col-12 row d-flex justify-content-between">
            <div class="row col-6 mb-3">
                <form action="" method="post" id="frmBuscarGrupo">
                    @csrf
                    <input type="text" class="form-control" name="txtbuscar" id="txtbuscar" placeholder="FOLIO DE GRUPO" value="{{(!empty($data_cursos->folio_grupo)) ? $data_cursos->folio_grupo : ''}}">
                </form>
                <button class="btn" id="btnBuscarGrupo">BUSCAR</button>
            </div>
        </div>
        {{-- CONTENDOR DE TODO EL CUERPO --}}
        @if ($data_cursos != null)

            {{-- CONTENIDO DEL ENCABEZADO --}}
            <div class="mb-2">
                <span class="badge badge-success">
                @if ($array_rol['rol'] == 1)
                    DEPARTAMENTO DE VINCULACIÓN
                @elseif($array_rol['rol'] == 2)
                    DEPARTAMENTO ACADÉMICO
                @elseif($array_rol['rol'] == 3)
                    DELEGACIÓN ADMINISTRATIVA
                @elseif($array_rol['rol'] == 4)
                    DIRECCIÓN TÉCNICA ACADÉMICA
                @endif
            </span>
            </div>
            <div class="container-fluid px-5 pt-3 bg-light datos-curso">
                <div class="row justify-content-center">
                    <div class="col-12 colorTitulo d-flex align-items-center justify-content-center border border-1 border-white p-1 mb-3">
                        <div class="font-weight-bold text-center text-dark">CONDICIONES DEL SERVICIO DE CAPACITACIÓN</div>
                    </div>
                    <div class="row col-12">
                        <div class="col-4 border-dark border-right">
                            <p><span class="negrita">TIPO DE CAPACITACION:</span> {{($data_cursos->tipo_curso == 'CURSO') ? 'CURSO' : 'CERTIFICACION EXTRAORDINARIA'}}</span></p>
                            <p><span class="negrita">NOMBRE DEL CURSO:</span> {{$data_cursos->curso}}</p>
                            <p><span class="negrita">TIPO DE CURSO:</span> {{$data_cursos->tcapacitacion}}</p>
                            <p><span class="negrita">CLAVE DEL CURSO:</span> {{$data_cursos->clave}}</p>
                        </div>
                        <div class="col-4 border-dark border-right">
                            <p><span class="negrita">NOMBRE DEL INSTRUCTOR:</span> {{$data_cursos->nombre}}</p>
                            <p><span class="negrita">ESPECIALIDAD DEL INSTRUCTOR:</span> {{$data_cursos->espe}}</p>
                            <p><span class="negrita">TIPO DE PAGO: </span> {{$data_cursos->tpago}}</p>
                            <p><span class="negrita">FOLIO DE GRUPO: </span>{{($data_cursos != null) ? $data_cursos->folio_grupo : ''}}</p>
                            {{-- <p><span class="negrita">EXONERACIÓN DE CUOTA:</span>PENDIENTE</p> --}}
                        </div>
                        <div class="col-4">
                            {{-- <p><span class="negrita">REDUCCIÓN DE CUOTA DE RECUPERACIÓN</span> PENDIENTE</p> --}}
                            {{-- <p><span class="negrita">CUOTA ORDINARIA:</span> {{$data_cursos->costo_alumnos}}</p> --}}
                            <p><span class="negrita">FECHA INICIO:</span> {{ \Carbon\Carbon::createFromFormat('Y-m-d', $data_cursos->inicio)->format('d/m/Y') }}</p>
                            <p><span class="negrita">FECHA TERMINO:</span> {{ \Carbon\Carbon::createFromFormat('Y-m-d', $data_cursos->termino)->format('d/m/Y') }}</p>
                            <p><span class="negrita">HORARIO:</span> {{date("H:i", strtotime($data_cursos->hini))}} -  {{date("H:i", strtotime($data_cursos->hfin))}} HRS</p>
                            <p><span class="negrita">CUOTA GENERAL:</span> {{$data_cursos->costo}}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NAVEGACION  --}}
            <p class="pt-3 mb-0 font-italic font-weight-bold">Navegación</p>
            <div class="col-12 d-flex justify-content-start px-0 py-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#vinculacion">Vinculación</a></li>
                    <li class="breadcrumb-item"><a href="#academico">Académico</a></li>
                    <li class="breadcrumb-item"><a href="#administrativo">Administrativa</a></li>
                    <li class="breadcrumb-item"><a href="#estatus">Estatus</a></li>
                    </ol>
                </nav>
            </div>
            @php
                $isVinc = ($array_rol['rol'] == 1) && ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO');
                $v_class = $isVinc ? '' : 'blocked';
                $isAcad = ($array_rol['rol'] == 2) && ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO');
                $a_class = $isAcad ? '' : 'blocked';
                $isAdmin = ($array_rol['rol'] == 3) && ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO');
                $d_class = $isAdmin ? '' : 'blocked';
                $dta_msg = $array_rol['rol'] == 4 ? '' : 'readonly';
            @endphp
            {{-- tabla vinculacion --}}
            <div class="col-12 px-0 mt-1" id="vinculacion">
                <div class="text-center titulo_tabla">DEPARTAMENTO DE VINCULACIÓN</div>
                <table class="table-hover">
                    <thead>
                    <tr>
                        <th width = "3%">NO.</th>
                        <th width = "30%">EVIDENCIAS</th>
                        <th width = "5%">SI</th>
                        <th width = "5%">NO</th>
                        <th width = "8%">NO APLICA</th>
                        <th width = "15%">OBSERVACIONES</th>
                        <th width = "7%">SUBIR PDF</th>
                        <th width = "6%">VER PDF</th>
                        <th width = "6%">ELIMINAR</th>
                        <th width ="15">MENSAJE DTA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>a</td>
                        <td>Convenio Especifico / Acta de acuerdo.</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion1" id="yes_req1" value="si"{{($v_radios[0]['doc_1'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion1" id="no_req1" value="no"{{($v_radios[0]['doc_1'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion1" id="na_req1" value="no_aplica"
                                {{($v_radios[0]['doc_1'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req1" id="comentario_req1" rows="1" cols="30">{{ $v_radios[0]['doc_txt1'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen 1--}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc1"
                            class="
                            @if(!empty($json_dptos->vinculacion['doc_1']['url_pdf_convenio']) || !empty($json_dptos->vinculacion['doc_1']['url_pdf_acta']))
                            d-none
                            @endif
                            ">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc1" style="display: none;" onchange="checkIcon('iconCheck1', 'pdfInputDoc1')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc1').click();">Archivo
                                    <div id="iconCheck1" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($json_dptos->vinculacion['doc_1']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$json_dptos->vinculacion['doc_1']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @elseif(!empty($json_dptos->vinculacion['doc_1']['url_pdf_convenio']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$json_dptos->vinculacion['doc_1']['url_pdf_convenio']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @elseif(!empty($json_dptos->vinculacion['doc_1']['url_pdf_acta']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$json_dptos->vinculacion['doc_1']['url_pdf_acta']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf 1 --}}
                            @if (!empty($json_dptos->vinculacion['doc_1']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion1',
                                    '{{isset($json_dptos) ? $json_dptos->vinculacion['doc_1']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta1" id="comentario_dta1" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_1'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    {{-- Soporte para convenio especifico --}}
                    <tr>
                        <td>a.1</td>
                        <td>Soporte de manifiesto de inscripción</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion_v8" id="yes_req_v8" value="si" {{($v_radios[0]['doc_8'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion_v8" id="no_req_v8" value="no" {{($v_radios[0]['doc_8'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion_v8" id="na_req_v8" value="no_aplica" {{($v_radios[0]['doc_8'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req_v8" id="comentario_req_v8" rows="1" cols="30">{{ $v_radios[0]['doc_txt8'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc_v8">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc_v8" style="display: none;" onchange="checkIcon('iconCheck_v8', 'pdfInputDoc_v8')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc_v8').click();">Archivo
                                    <div id="iconCheck_v8" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($json_dptos->vinculacion['doc_8']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$json_dptos->vinculacion['doc_8']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            @if (!empty($json_dptos->vinculacion['doc_8']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion8',
                                    '{{$json_dptos->vinculacion['doc_8']['url_documento']}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta26" id="comentario_dta26" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_8'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>b</td>
                        <td>Copia de autorización de Exoneración y/o Reducción de Cuota de Recuperación.</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion2" id="yes_req2" value="si" {{($v_radios[0]['doc_2'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion2" id="no_req2" value="no" {{($v_radios[0]['doc_2'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion2" id="na_req2" value="no_aplica" {{($v_radios[0]['doc_2'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req2" id="comentario_req2" rows="1" cols="30">{{ $v_radios[0]['doc_txt2'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc2']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$search_docs['urldoc2'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea name="comentario_dta2" id="comentario_dta2" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_2'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>c</td>
                        <td>Original de la solicitud de apertura de cursos de capacitación y/o certificación al Departamento Académico.</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion3" id="yes_req3" value="si" {{($v_radios[0]['doc_3'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion3" id="no_req3" value="no" {{($v_radios[0]['doc_3'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion3" id="na_req3" value="no_aplica" {{($v_radios[0]['doc_3'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req3" id="comentario_req3" rows="1" cols="30">{{ $v_radios[0]['doc_txt3'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen 3--}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc3">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc3" style="display: none;" onchange="checkIcon('iconCheck3', 'pdfInputDoc3')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc3').click();">Archivo
                                    <div id="iconCheck3" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($json_dptos->vinculacion['doc_3']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$json_dptos->vinculacion['doc_3']['url_documento'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf 3--}}
                            @if (!empty($json_dptos->vinculacion['doc_3']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion3',
                                    '{{isset($json_dptos) ? $json_dptos->vinculacion['doc_3']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta3" id="comentario_dta3" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_3'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>d</td>
                        <td>SID-01 solicitud de Inscripción del interesado.</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion4" id="yes_req4" value="si" {{($v_radios[0]['doc_4'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion4" id="no_req4" value="no" {{($v_radios[0]['doc_4'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion4" id="na_req4" value="no_aplica" {{($v_radios[0]['doc_4'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req4" id="comentario_req4" rows="1" cols="30">{{ $v_radios[0]['doc_txt4'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen 4--}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc4">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc4" style="display: none;" onchange="checkIcon('iconCheck4', 'pdfInputDoc4')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc4').click();">Archivo
                                    <div id="iconCheck4" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($json_dptos->vinculacion['doc_4']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$json_dptos->vinculacion['doc_4']['url_documento'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf 4--}}
                            @if (!empty($json_dptos->vinculacion['doc_4']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion4',
                                    '{{isset($json_dptos) ? $json_dptos->vinculacion['doc_4']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta4" id="comentario_dta4" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_4'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>e</td>
                        <td>CURP actualizada o Copia de Acta de Nacimiento.</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion5" id="yes_req5" value="si" {{($v_radios[0]['doc_5'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion5" id="no_req5" value="no" {{($v_radios[0]['doc_5'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion5" id="na_req5" value="no_aplica" {{($v_radios[0]['doc_5'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req5" id="comentario_req5" rows="1" cols="30">{{ $v_radios[0]['doc_txt5'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            <a class="btn-circle btn-circle-sm btn_modal_alumnos" id="" onclick="modalRequisitos(event, 'curp')"
                                href="#">
                                <i class="fa fa fa-eye fa-2x fa-lg text-dark" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta5" id="comentario_dta5" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_5'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>f</td>
                        <td>Copia de comprobante de último grado de estudios (en caso de contar con él).</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion6" id="yes_req6" value="si" {{($v_radios[0]['doc_6'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion6" id="no_req6" value="no" {{($v_radios[0]['doc_6'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion6" id="na_req6" value="no_aplica" {{($v_radios[0]['doc_6'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req6" id="comentario_req6" rows="1" cols="30">{{ $v_radios[0]['doc_txt6'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            <a class="btn-circle btn-circle-sm btn_modal_alumnos" id="" onclick="modalRequisitos(event, 'estudios')"
                                href="#">
                                <i class="fa fa fa-eye fa-2x fa-lg text-dark" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta6" id="comentario_dta6" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_6'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>g</td>
                        <td>Copia del recibo oficial de la cuota de recuperación expedido por la Delegación Administrativa y comprobante de depósito o transferencia Bancaria.</td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion7" id="yes_req7" value="si" {{($v_radios[0]['doc_7'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion7" id="no_req7" value="no" {{($v_radios[0]['doc_7'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$v_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion7" id="na_req7" value="no_aplica" {{($v_radios[0]['doc_7'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req7" id="comentario_req7" rows="1" cols="30">{{ $v_radios[0]['doc_txt7'] ?? '' }}</textarea>
                        </td>
                        <td>
                            {{-- Subir recibo--}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc7" class="
                            @if($search_docs['anio_curso'] == '2023' && $search_docs['validRecibo'] != 'digital')
                                @if($search_docs['tipo_curso'] == 'EXO')
                                    d-none
                                @endif
                            @else
                                d-none
                            @endif
                            ">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc7" style="display: none;" onchange="checkIcon('iconCheck7', 'pdfInputDoc7')">
                                    <input type="hidden" name="" id="txt_folio_recibo" value="">
                                    <input type="hidden" name="" id="txt_folio_fecha" value="">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc7').click();">Archivo
                                    <div id="iconCheck7" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                            {{-- @endif --}}
                        </td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc7']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="
                                    @if ($search_docs['validRecibo'] == 'digital')
                                        {{-- {{ route('grupos.recibos.descargar', ['folio_recibo' => $search_docs['urldoc7']]) }} --}}
                                        {{$search_docs['urldoc7']}}
                                    @else
                                        {{$path_files.$search_docs['urldoc7']}}
                                    @endif
                                    " target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                            {{-- eliminar pdf 1 --}}
                            {{-- @if (!empty($search_docs['urldoc7']) && $search_docs['validRecibo'] != 'digital')
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion7',
                                    '{{$search_docs['urldoc7']}}',
                                    {{$array_rol['rol']}}, '{{$data_cursos->id}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif --}}
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta7" id="comentario_dta7" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->vinculacion['doc_7'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                {{-- Boton de guardar vinculacion y generar pdf--}}
                @if ($array_rol['rol'] == 1)
                    @if ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO')
                        <button class="btn mt-3 float-right font-weight-bold" id="btnGuardarValores" onclick="ejecutarAsync({{$array_rol['rol']}}, {{$array_rol['idcurso']}})">GUARDAR</button>
                    @endif
                    <div class="text-center" id="divGenPdf">
                        <button class="btn mt-3 font-weight-bold float-right" onclick="genpdf_expe({{$array_rol['idcurso']}})">GENERAR PDF</button>
                    </div>
                @endif
            </div>

            {{-- tabla academico --}}
            <div class="col-12 px-0 mt-3" id="academico">
                <div class="text-center titulo_tabla">DEPARTAMENTO ACADÉMICO</div>
                <table class="table-hover">
                    <thead>
                    <tr>
                        <th width = "3%">NO.</th>
                        <th width = "30%">EVIDENCIAS</th>
                        <th width = "5%">SI</th>
                        <th width = "5%">NO</th>
                        <th width = "8%">NO APLICA</th>
                        <th width = "15%">OBSERVACIONES</th>
                        <th width = "7%">SUBIR PDF</th>
                        <th width = "6%">VER PDF</th>
                        <th width = "6%">ELIMINAR</th>
                        <th width ="15">MENSAJE DTA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>a</td>
                        <td>Original de memorándum ARC-01, solicitud de Apertura de cursos de capacitación y/o Certificación a la
                            Dirección Técnica Académica.
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion8" id="yes_req8" value="si" {{($v_radios[1]['doc_8'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion8" id="no_req8" value="no" {{($v_radios[1]['doc_8'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion8" id="na_req8" value="no_aplica" {{($v_radios[1]['doc_8'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req8" id="comentario_req8" rows="1" cols="30">{{ $v_radios[1]['doc_txt8'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc8']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$search_docs['urldoc8']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta8" id="comentario_dta8" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_8'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>b</td>
                        <td>Copia de memorándum de autorización de ARC-01, emitido por la Dirección Técnica Académica.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion9" id="yes_req9" value="si" {{($v_radios[1]['doc_9'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion9" id="no_req9" value="no" {{($v_radios[1]['doc_9'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion9" id="na_req9" value="no_aplica" {{($v_radios[1]['doc_9'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req9" id="comentario_req9" rows="1" cols="30">{{ $v_radios[1]['doc_txt9'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc9']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$search_docs['urldoc9']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta9" id="comentario_dta9" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_9'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>c</td>
                        <td>
                            Original de memorándum ARC-02, solicitud de modificación, reprogramación y/o cancelación de curso a la Dirección Técnica Académica, en caso aplicable.
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion10" id="yes_req10" value="si" {{($v_radios[1]['doc_10'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion10" id="no_req10" value="no" {{($v_radios[1]['doc_10'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion10" id="na_req10" value="no_aplica" {{($v_radios[1]['doc_10'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req10" id="comentario_req10" rows="1" cols="30">{{ $v_radios[1]['doc_txt10'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc10']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$path_files.$search_docs['urldoc10']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta10" id="comentario_dta10" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_10'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>d</td>
                        <td>Copia de memorándum de autorización de ARC-02, emitido por la Dirección Técnica Académica, en caso aplicable.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion11" id="yes_req11" value="si" {{($v_radios[1]['doc_11'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion11" id="no_req11" value="no" {{($v_radios[1]['doc_11'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion11" id="na_req11" value="no_aplica" {{($v_radios[1]['doc_11'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req11" id="comentario_req11" rows="1" cols="30">{{ $v_radios[1]['doc_txt11'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc11']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$search_docs['urldoc11']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta11" id="comentario_dta11" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_11'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>e</td>
                        <td>Copia de RIACD-02 Inscripción.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion12" id="yes_req12" value="si" {{($v_radios[1]['doc_12'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion12" id="no_req12" value="no" {{($v_radios[1]['doc_12'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion12" id="na_req12" value="no_aplica" {{($v_radios[1]['doc_12'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req12" id="comentario_req12" rows="1" cols="30">{{ $v_radios[1]['doc_txt12'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc12">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc12" style="display: none;" onchange="checkIcon('iconCheck12', 'pdfInputDoc12')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc12').click();">Archivo
                                    <div id="iconCheck12" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- mostrar pdf --}}
                            @if (!empty($json_dptos->academico['doc_12']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_12"
                                    href="{{$path_files.$json_dptos->academico['doc_12']['url_documento'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_12']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion12',
                                    '{{isset($json_dptos) ? $json_dptos->academico['doc_12']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta12" id="comentario_dta12" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_12'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>f</td>
                        <td>Copia de RIACD-02 Acreditación.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion13" id="yes_req13" value="si" {{($v_radios[1]['doc_13'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion13" id="no_req13" value="no" {{($v_radios[1]['doc_13'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion13" id="na_req13" value="no_aplica" {{($v_radios[1]['doc_13'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req13" id="comentario_req13" rows="1" cols="30">{{ $v_radios[1]['doc_txt13'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc13">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc13" style="display: none;" onchange="checkIcon('iconCheck13', 'pdfInputDoc13')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc13').click();">Archivo
                                    <div id="iconCheck13" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- mostrar pdf --}}
                            @if (!empty($json_dptos->academico['doc_13']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_13"
                                    href="{{$path_files.$json_dptos->academico['doc_13']['url_documento'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_13']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion13',
                                    '{{isset($json_dptos) ? $json_dptos->academico['doc_13']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta13" id="comentario_dta13" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_13'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>g</td>
                        <td>Copia de RIACD-02 Certificación.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion14" id="yes_req14" value="si" {{($v_radios[1]['doc_14'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion14" id="no_req14" value="no" {{($v_radios[1]['doc_14'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion14" id="na_req14" value="no_aplica" {{($v_radios[1]['doc_14'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req14" id="comentario_req14" rows="1" cols="30">{{ $v_radios[1]['doc_txt14'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc14">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc14" style="display: none;" onchange="checkIcon('iconCheck14', 'pdfInputDoc14')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc14').click();">Archivo
                                    <div id="iconCheck14" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- mostrar pdf --}}
                            @if (!empty($json_dptos->academico['doc_14']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_14"
                                    href="{{$path_files.$json_dptos->academico['doc_14']['url_documento'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_14']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion14',
                                    '{{isset($json_dptos) ? $json_dptos->academico['doc_14']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta14" id="comentario_dta14" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_14'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    {{-- esto es un extra se formato de entrega de constancias --}}
                    <tr>
                        <td>g.1</td>
                        <td>
                            Soportes de entrega de constancias de capacitación (si es el caso).
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion25" id="yes_req25" value="si" {{($v_radios[1]['doc_25'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion25" id="no_req25" value="no" {{($v_radios[1]['doc_25'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion25" id="na_req25" value="no_aplica" {{($v_radios[1]['doc_25'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req25" id="comentario_req25" rows="1" cols="30">{{ $v_radios[1]['doc_txt25'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc25">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc25" style="display: none;" onchange="checkIcon('iconCheck25', 'pdfInputDoc25')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc25').click();">Archivo
                                    <div id="iconCheck25" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- mostrar pdf --}}
                            @if (!empty($json_dptos->academico['doc_25']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Oficio de entrega de constancias" id="verpdf_25"
                                    href="{{$path_files.$json_dptos->academico['doc_25']['url_documento'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encuentra el oficio de entrega de constancias" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                            {{-- Otro soportes --}}
                            @if (!empty($json_dptos->academico['doc_25']['url_soporte']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver PDF" id="verpdf_25"
                                    href="{{$path_files.$json_dptos->academico['doc_25']['url_soporte'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Archivo no encontrado" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_25']['url_soporte']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion25',
                                    '{{isset($json_dptos) ? $json_dptos->academico['doc_25']['url_soporte'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta25" id="comentario_dta25" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_25'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>h</td>
                        <td>Copia de LAD-04 Lista de Asistencia.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion15" id="yes_req15" value="si" {{($v_radios[1]['doc_15'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion15" id="no_req15" value="no" {{($v_radios[1]['doc_15'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion15" id="na_req15" value="no_aplica" {{($v_radios[1]['doc_15'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req15" id="comentario_req15" rows="1" cols="30">{{ $v_radios[1]['doc_txt15'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc15" class="{{(!empty($search_docs['urldoc15'])) ? 'd-none' : ''}}">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc15" style="display: none;" onchange="checkIcon('iconCheck15', 'pdfInputDoc15')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc15').click();">Archivo
                                    <div id="iconCheck15" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- Mostrar lista de asistencia --}}
                            @if (!empty($search_docs['urldoc15']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="
                                    @if (is_numeric($search_docs['urldoc15']))
                                        {{route('asistencia-pdf', ['id' => $search_docs['urldoc15']])}}
                                    @else
                                        {{$search_docs['urldoc15']}}
                                    @endif
                                    " target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @elseif(!empty($json_dptos->academico['doc_15']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_15"
                                    href="{{$path_files.$json_dptos->academico['doc_15']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado, es necesario cargar el archivo en el modulo correspondiente.')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_15']['url_documento']) && empty($search_docs['urldoc15']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion15',
                                    '{{$json_dptos->academico['doc_15']['url_documento']}}',
                                    {{$array_rol['rol']}}, '{{$data_cursos->id}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta15" id="comentario_dta15" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_15'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>i</td>
                        <td>Copia de RESD-05 Registro de Evaluación por Sub - objetivos.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion16" id="yes_req16" value="si" {{($v_radios[1]['doc_16'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion16" id="no_req16" value="no" {{($v_radios[1]['doc_16'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion16" id="na_req16" value="no_aplica" {{($v_radios[1]['doc_16'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req16" id="comentario_req16" rows="1" cols="30">{{ $v_radios[1]['doc_txt16'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc16" class="{{(!empty($search_docs['urldoc16'])) ? 'd-none' : ''}}">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc16" style="display: none;" onchange="checkIcon('iconCheck16', 'pdfInputDoc16')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc16').click();">Archivo
                                    <div id="iconCheck16" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- mostrar pdf --}}
                            @if (!empty($search_docs['urldoc16']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="
                                    @if (is_numeric($search_docs['urldoc16']))
                                        {{route('calificacion-pdf', ['id' => $search_docs['urldoc16']])}}
                                    @else
                                        {{$search_docs['urldoc16']}}
                                    @endif
                                    " target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @elseif(!empty($json_dptos->academico['doc_16']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_16"
                                    href="{{$path_files.$json_dptos->academico['doc_16']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado, es necesario cargar el archivo en el modulo correspondiente.')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif

                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_16']['url_documento']) && empty($search_docs['urldoc16']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion16',
                                    '{{$json_dptos->academico['doc_16']['url_documento']}}',
                                    {{$array_rol['rol']}}, '{{$data_cursos->id}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta16" id="comentario_dta16" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_16'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>j</td>
                        <td>Originales o Copia de las Evaluaciones y/o Reactivos de aprendizaje del alumno y/o resumen de actividades.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion17" id="yes_req17" value="si" {{($v_radios[1]['doc_17'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion17" id="no_req17" value="no" {{($v_radios[1]['doc_17'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion17" id="na_req17" value="no_aplica" {{($v_radios[1]['doc_17'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req17" id="comentario_req17" rows="1" cols="30">{{ $v_radios[1]['doc_txt17'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc17">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc17" style="display: none;" onchange="checkIcon('iconCheck17', 'pdfInputDoc17')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc17').click();">Archivo
                                    <div id="iconCheck17" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- mostrar pdf --}}
                            @if (!empty($json_dptos->academico['doc_17']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_17"
                                    href="{{$path_files.$json_dptos->academico['doc_17']['url_documento'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_17']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion17',
                                    '{{isset($json_dptos) ? $json_dptos->academico['doc_17']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta17" id="comentario_dta17" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_17'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>k</td>
                        <td>Original o Copia de las Evaluaciones al Docente y Evaluación del Curso y/o resumen de actividades.</td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion18" id="yes_req18" value="si" {{($v_radios[1]['doc_18'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion18" id="no_req18" value="no" {{($v_radios[1]['doc_18'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion18" id="na_req18" value="no_aplica" {{($v_radios[1]['doc_18'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req18" id="comentario_req18" rows="1" cols="30">{{ $v_radios[1]['doc_txt18'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir Imagen --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc18">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc18" style="display: none;" onchange="checkIcon('iconCheck18', 'pdfInputDoc18')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc18').click();">Archivo
                                    <div id="iconCheck18" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            {{-- mostrar pdf --}}
                            @if (!empty($json_dptos->academico['doc_18']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_18"
                                    href="{{$path_files.$json_dptos->academico['doc_18']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_18']['url_documento']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion18',
                                    '{{isset($json_dptos) ? $json_dptos->academico['doc_18']['url_documento'] : ''}}',
                                    {{$array_rol['rol']}}, '{{($data_cursos != null) ? $data_cursos->id : ''}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta18" id="comentario_dta18" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_18'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>l</td>
                        <td>Reporte fotográfico, como mínimo dos fotografías. </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion19" id="yes_req19" value="si" {{($v_radios[1]['doc_19'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion19" id="no_req19" value="no" {{($v_radios[1]['doc_19'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$a_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion19" id="na_req19" value="no_aplica" {{($v_radios[1]['doc_19'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req19" id="comentario_req19" rows="1" cols="30">{{ $v_radios[1]['doc_txt19'] ?? '' }}</textarea>
                        </td>
                        <td class="text-center">
                            {{-- Subir pdf --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc19" class="
                            {{-- {{(!empty($search_docs['urldoc19']) && $search_docs['anio_curso'] != '2023') ? 'd-none' : ''}} --}}
                            @if($search_docs['anio_curso'] == '2023' && empty($search_docs['urldoc19']))
                            @elseif(!empty($search_docs['urldoc19']))
                                d-none
                            @endif
                            ">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc19" style="display: none;" onchange="checkIcon('iconCheck19', 'pdfInputDoc19')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc19').click();">Archivo
                                    <div id="iconCheck19" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc19']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="
                                    @if (is_numeric($search_docs['urldoc19']))
                                        {{route('reportefoto-pdf', ['id' => $search_docs['urldoc19']])}}
                                    @else
                                        {{$search_docs['urldoc19']}}
                                    @endif
                                    " target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @elseif(!empty($json_dptos->academico['doc_19']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_19"
                                    href="{{$path_files.$json_dptos->academico['doc_19']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->academico['doc_19']['url_documento']) && empty($search_docs['urldoc19']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion19',
                                    '{{$json_dptos->academico['doc_19']['url_documento']}}',
                                    {{$array_rol['rol']}}, '{{$data_cursos->id}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta19" id="comentario_dta19" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->academico['doc_19'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                {{-- Boton de guardar Academico y generar PDF--}}
                @if ($array_rol['rol'] == 2)
                    @if ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO')
                        <button class="btn mt-3 float-right font-weight-bold" id="btnGuardarValores" onclick="ejecutarAsync({{$array_rol['rol']}}, {{$array_rol['idcurso']}})">GUARDAR</button>
                    @endif
                    <div class="text-center" id="divGenPdf">
                        <button class="btn mt-3 font-weight-bold float-right" onclick="genpdf_expe({{$array_rol['idcurso']}})">GENERAR PDF</button>
                    </div>
                @endif
            </div>

            {{-- tabla administrativo table-bordered para bordes --}}
            <div class="col-12 px-0 mt-3" id="administrativo">
                <div class="text-center titulo_tabla">DELEGACIÓN ADMINISTRATIVA</div>
                <table class="table-hover">
                    <thead>
                    <tr>
                        <th width = "3%">NO.</th>
                        <th width = "30%">EVIDENCIAS</th>
                        <th width = "5%">SI</th>
                        <th width = "5%">NO</th>
                        <th width = "8%">NO APLICA</th>
                        <th width = "15%">OBSERVACIONES</th>
                        <th width = "7%">SUBIR PDF</th>
                        <th width = "6%">VER PDF</th>
                        <th width = "6%">ELIMINAR</th>
                        <th width ="15">MENSAJE DTA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>a</td>
                        <td>Memorándum de solicitud de Suficiencia Presupuestal.</td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion20" id="yes_req20" value="si" {{($v_radios[2]['doc_20'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion20" id="no_req20" value="no" {{($v_radios[2]['doc_20'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion20" id="na_req20" value="no_aplica" {{($v_radios[2]['doc_20'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req20" id="comentario_req20" rows="1" cols="30">{{ $v_radios[2]['doc_txt20'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc20']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$search_docs['urldoc20'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado.')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta20" id="comentario_dta20" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->administrativo['doc_20'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>b</td>
                        <td>Copia de formato de autorización de suficiencia Presupuestal.</td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion21" id="yes_req21" value="si" {{($v_radios[2]['doc_21'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion21" id="no_req21" value="no" {{($v_radios[2]['doc_21'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion21" id="na_req21" value="no_aplica" {{($v_radios[2]['doc_21'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req21" id="comentario_req21" rows="1" cols="30">{{ $v_radios[2]['doc_txt21'] ?? '' }}</textarea>
                        </td>
                        <td></td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc21']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$search_docs['urldoc21'] ?? ''}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td></td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta21" id="comentario_dta21" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->administrativo['doc_21'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>c</td>
                        <td>Original de Contrato de prestación de curso de Capacitación y/o Certificación del Instructor externo, con firma autógrafa o firma electrónica.</td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion22" id="yes_req22" value="si" {{($v_radios[2]['doc_22'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion22" id="no_req22" value="no" {{($v_radios[2]['doc_22'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion22" id="na_req22" value="no_aplica" {{($v_radios[2]['doc_22'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req22" id="comentario_req22" rows="1" cols="30">{{ $v_radios[2]['doc_txt22'] ?? '' }}</textarea>
                        </td>
                        <td>
                            {{-- subir pdf --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc22" class="
                            {{-- {{(!empty($search_docs['urldoc22'])) ? 'd-none' : ''}} --}}
                            @if($search_docs['anio_curso'] == '2023')
                                @if(!empty($search_docs['urldoc22']))
                                    d-none
                                @endif
                            @elseif(!empty($search_docs['urldoc23']))
                                d-none
                            @endif
                            ">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc22" style="display: none;" onchange="checkIcon('iconCheck22', 'pdfInputDoc22')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc22').click();">Archivo
                                    <div id="iconCheck22" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc22']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="
                                    @if (is_numeric($search_docs['urldoc22']))
                                        {{route('contrato-pdf', ['id' => $search_docs['urldoc22']])}}
                                    @else
                                        {{$search_docs['urldoc22']}}
                                    @endif
                                    " target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @elseif(!empty($json_dptos->administrativo['doc_22']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_22"
                                    href="{{$path_files.$json_dptos->administrativo['doc_22']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                {{-- <button class="btn-circle btn-circle-sm border-0"><i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i></button> --}}
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->administrativo['doc_22']['url_documento']) && empty($search_docs['urldoc22']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion22',
                                    '{{$json_dptos->administrativo['doc_22']['url_documento']}}',
                                    {{$array_rol['rol']}}, '{{$data_cursos->id}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta22" id="comentario_dta22" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->administrativo['doc_22'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>d</td>
                        <td>Copia de memorándum de solicitud de pago al Instructor externo.</td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion23" id="yes_req23" value="si" {{($v_radios[2]['doc_23'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion23" id="no_req23" value="no" {{($v_radios[2]['doc_23'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion23" id="na_req23" value="no_aplica" {{($v_radios[2]['doc_23'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req23" id="comentario_req23" rows="1" cols="30">{{ $v_radios[2]['doc_txt23'] ?? '' }}</textarea>
                        </td>
                        <td>
                            {{-- subir pdf --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc23" class="
                            @if($search_docs['anio_curso'] == '2023')
                                @if(!empty($search_docs['urldoc23']))
                                    d-none
                                @endif
                            @elseif(!empty($search_docs['urldoc23']))
                                d-none
                            @endif
                            ">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc23" style="display: none;" onchange="checkIcon('iconCheck23', 'pdfInputDoc23')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc23').click();">Archivo
                                    <div id="iconCheck23" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc23']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$search_docs['urldoc23']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @elseif(!empty($json_dptos->administrativo['doc_23']['url_documento']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id="verpdf_23"
                                    href="{{$path_files.$json_dptos->administrativo['doc_23']['url_documento']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                            {{-- eliminar pdf --}}
                            @if (!empty($json_dptos->administrativo['doc_23']['url_documento']) && empty($search_docs['urldoc23']))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion23',
                                    '{{$json_dptos->administrativo['doc_23']['url_documento']}}',
                                    {{$array_rol['rol']}}, '{{$data_cursos->id}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta23" id="comentario_dta23" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->administrativo['doc_23'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>e</td>
                        <td>Comprobante Fiscal Digital por Internet del Instructor externo.</td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion24" id="yes_req24" value="si" {{($v_radios[2]['doc_24'] == 'si') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion24" id="no_req24" value="no" {{($v_radios[2]['doc_24'] == 'no') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="{{$d_class}}">
                            <div class="form-check d-flex justify-content-center align-items-center">
                                <input class="form-check-input" type="radio" name="opcion24" id="na_req24" value="no_aplica" {{($v_radios[2]['doc_24'] == 'no_aplica') ? 'checked' : ''}}>
                            </div>
                        </td>
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_req24" id="comentario_req24" rows="1" cols="30">{{ $v_radios[2]['doc_txt24'] ?? '' }}</textarea>
                        </td>
                        <td>
                            {{-- subir pdf --}}
                            <form method="POST" enctype="multipart/form-data" action="" id="form_doc24" class="
                            @if($search_docs['anio_curso'] == '2023' && (empty($search_docs['urldoc24']) || empty($search_docs['doc_xml']) ))
                            @else
                                d-none
                            @endif
                            ">
                                <div class="d-flex row justify-content-center">
                                    <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc24" style="display: none;" onchange="checkIcon('iconCheck24', 'pdfInputDoc24')">
                                    <button class="btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc24').click();">Archivo
                                    <div id="iconCheck24" style="display:none;"><i class="fas fa-check-circle"></i></div></button>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            @if (!empty($search_docs['urldoc24']))
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="Ver pdf" id=""
                                    href="{{$search_docs['urldoc24']}}" target="_blank">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @else
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="No se encontro el archivo" id="" href="#"
                                    onclick="showModal(event, 'Archivo no encontrado')">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-gray" aria-hidden="true"></i>
                                </a>
                            @endif

                            @if ((empty($search_docs['urldoc24']) || empty($search_docs['doc_xml'])) && !empty($json_dptos->administrativo['doc_24']['url_documento']))
                            <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                data-placement="top" title="Ver Archivo" id=""
                                href="{{$path_files.$json_dptos->administrativo['doc_24']['url_documento']}}" target="_blank">
                                <i class="fa fa fa-file-text fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                            </a>
                            @endif

                            @if (!empty($search_docs['doc_xml']))
                                <a class="btn-circle btn-circle-sm ml-2" data-toggle="tooltip"
                                    data-placement="top" title="Ver xml" id=""
                                    href="{{$search_docs['doc_xml']}}" target="_blank">
                                    <i class="fa fa fa-file-text fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                             {{-- eliminar pdf --}}
                             @if (!empty($json_dptos->administrativo['doc_24']['url_documento']) && (empty($search_docs['urldoc24']) || empty($search_docs['doc_xml']) ))
                                <button class="ml-2 bg-transparent border-0" onclick="delete_pdf(event, 'opcion24',
                                    '{{$json_dptos->administrativo['doc_24']['url_documento']}}',
                                    {{$array_rol['rol']}}, '{{$data_cursos->id}}')">
                                    <i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                        {{-- observacion dta --}}
                        <td class="text-center my-0 py-0">
                            <textarea class="" name="comentario_dta24" id="comentario_dta24" rows="1" cols="30" {{$dta_msg}}>{{ data_get($json_dptos->administrativo['doc_24'], 'mensaje_dta', '')}}</textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- APARTADO DEL STATUS DE SEGUIMIENTO Y BOTONES--}}
            <div class="col-12 ml-1 row d-flex justify-content-between mt-3" id="estatus">
                {{-- status --}}
                <div class="col-3 bg-light p-2">
                    <span class="d-block font-weight-bold text-center">SEGUIMIENTO DE STATUS</span>
                    <ul class="list-group mt-3">
                        <li class="list-group-item font-weight-bold
                        @if ($st_general[0] == 'CAPTURA' && $st_general[1] == 'CAPTURA' && $st_general[2] == 'CAPTURA') active @endif">
                            EN CAPTURA
                        </li>
                        <li class="list-group-item font-weight-bold
                        @if ($st_general[0] == 'ENVIADO' && $st_general[1] == 'ENVIADO' && $st_general[2] == 'ENVIADO') active @endif">
                            ENVIADO A DTA
                        </li>
                        <li class="list-group-item font-weight-bold
                        @if ($st_general[0] == 'RETORNADO' && $st_general[1] == 'RETORNADO' && $st_general[2] == 'RETORNADO') active @endif">
                            RETORNADO POR DTA
                            </li>
                        <li class="list-group-item font-weight-bold
                        @if ($st_general[0] == 'VALIDADO' && $st_general[1] == 'VALIDADO' && $st_general[2] == 'VALIDADO') active @endif">
                            VALIDADO POR DTA
                        </li>
                    </ul>
                </div>

                {{-- Botones de guardar y enviar que podra ver delegacion. --}}
                <div class="col-5 d-flex justify-content-end align-items-start">
                    {{-- Generar pdf dele admin --}}
                    @if ($array_rol['rol'] == 3)
                        <div class="text-center" id="divGenPdf">
                            <button class="btn font-weight-bold" onclick="genpdf_expe({{$array_rol['idcurso']}})">GENERAR PDF</button>
                        </div>
                    @endif

                    {{-- Movemos de lugar este boton --}}
                    @if ($array_rol['rol'] == 3)
                        @if ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO')
                            <button class="btn font-weight-bold" id="btnGuardarValores" onclick="ejecutarAsync({{$array_rol['rol']}}, {{$array_rol['idcurso']}})">GUARDAR</button>
                        @endif
                    @endif

                    {{-- solo se muestra si es delegacion y si todos los json estan como guardados --}}
                    @if ($array_rol['btn_envio_dta'] && $array_rol['rol'] == 3)
                        @if ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO')
                            <button class="btn btn-danger" onclick="validar_form({{$array_rol['idcurso']}})">ENVIAR A DTA</button>
                        @endif
                    @endif
                    {{-- DTA --}}
                    {{-- Gen PDF --}}
                    <div class="row">
                        @if ($array_rol['rol'] == 4)
                            <div class="mt-2 text-center" id="divGenPdf">
                                <button class="btn font-weight-bold" onclick="genpdf_expe({{$array_rol['idcurso']}})">GENERAR PDF</button>
                            </div>
                        @endif
                        @if ($array_rol['rol'] == 4)
                            <div class="d-flex d-row">
                                <select name="" id="select_mov" class="form-control mt-2" onchange="combo_movi_dta()">
                                    <option value="0">-- MOVIMIENTOS --</option>
                                    <option {{($array_rol['status_json'] == 'ENVIADO') ? '' : 'disabled'}} value="1">VALIDAR</option>
                                    <option {{($array_rol['status_json'] == 'ENVIADO') ? '' : 'disabled'}} value="2">RETORNAR</option>
                                    {{-- <option value="3">GENERAR PDF</option> --}}
                                </select>
                                <div class="mt-2 text-center d-none" id="divValid">
                                    <button class="btn btn-danger font-weight-bold" onclick="valid_return_dta('validar', {{$array_rol['idcurso']}}, {{$array_rol['rol']}})">ACEPTAR</button>
                                </div>

                                <div class="mt-2 text-center d-none" id="divReturn">
                                    <button class="btn btn-danger font-weight-bold" onclick="valid_return_dta('retornar', {{$array_rol['idcurso']}}, {{$array_rol['rol']}})">ACEPTAR</button>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        @else
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        @endif

        {{-- Modal para alerta --}}
        <div id="myModal" class="modal_del">
            <div class="modal-content2">
                <p class="mb-1"><b>Mensaje</b></p>
                <span class="" id="mensajeModal"></span>
                <div class="col-12 float-right">
                    <button onclick="closeModal('myModal')" class="float-right btn-sm btn-sm-primary">Cerrar</button>
                </div>
            </div>
        </div>


        {{-- Modal v2 alumnos curp--}}
        <div id="modalAlumnosCurp" class="modal_al">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <div class="alert alert-danger mt-1 p-2 mb-0" role="alert">
                        <strong>Nota!</strong> Cargue un solo PDF por alumno, donde contenga los requisitos seleccionados.
                    </div>
                    <button type="button" class="close" onclick="closeModal('modalAlumnosCurp')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-0">
                    <p class="font-weight-bold text-center mb-1" style="font-size: 16px;">Alumnos</p>
                    <div class="scrollable-list">
                        @if (isset($search_docs['alumnos_req']) && count($search_docs['alumnos_req']) > 0)
                            <form action="" method="post" enctype="multipart/form-data" id="frmRequisitos">
                                @csrf
                                <input type="hidden" name="folioG" value="{{(!empty($data_cursos->folio_grupo)) ? $data_cursos->folio_grupo : ''}}">
                                <input type="hidden" name="checksCurp" value="" id="checksCurp">
                                <input type="hidden" name="checksEstudios" value="" id="checksEstudios">
                                <input type="hidden" name="checksActaNacim" value="" id="checksActaNacim">
                                <table class="table-hover">
                                    <thead>
                                        <tr>
                                            {{-- <th colspan="1">Documentos</th> --}}
                                            <th>Nombre del Alumno</th>
                                            <th>PDF</th>
                                            <th colspan="4">Subir PDF</th>
                                        </tr>
                                        <tr>
                                            {{-- <th>Curp</th> --}}
                                            {{-- <th></th> --}}
                                            <th></th>
                                            <th></th>
                                            <th>Curp</th>
                                            <th>C.Estudio</th>
                                            <th>Acta Nacim.</th>
                                            <th>Subir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach ($search_docs['alumnos_req'] as $key => $valor)
                                                <tr>
                                                    {{-- <td class="text-center">
                                                        @if ($valor['curp'] == 'true' && !empty($valor['documento']))
                                                            <i class="fa fa-check text-success" aria-hidden="true"></i>
                                                        @else
                                                            <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                                        @endif
                                                    </td> --}}
                                                    <td class="text-left">
                                                        <input type="hidden" name="alumnosId[]" value="{{$valor['id']}}">
                                                        <input type="hidden" name="docAlumnos[{{ $valor['id'] }}]" value="{{$valor['documento']}}">
                                                        <input type="hidden" name="identPre[{{ $valor['id'] }}]" value="{{$valor['id_pre']}}">
                                                        <span>{{($key+1).'.- '.$valor['alumno']}}</span></td>
                                                    <td class="text-center">
                                                        @if (!empty($valor['documento']))
                                                            <a href="{{$valor['documento']}}" target="_blank"><i class="fa fa-file-pdf-o fa-1x fa-lg text-danger" aria-hidden="true"></i></a>
                                                        @else
                                                            <i class="fa fa-file-pdf-o fa-1x fa-lg text-light" aria-hidden="true"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check mb-4">
                                                            <input class="form-check-input checkbox-curp" type="checkbox" value="true" id="" name=""
                                                            @if ($valor['curp'] == 'true' && !empty($valor['documento']))
                                                                checked
                                                            @endif
                                                            >
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check mb-4">
                                                            <input class="form-check-input checkbox-estudios" type="checkbox" value="true" id="" name=""
                                                            @if ($valor['estudio'] == 'true' && !empty($valor['documento']))
                                                                checked
                                                            @endif
                                                            >
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check mb-4">
                                                            <input class="form-check-input checkbox-acta" type="checkbox" value="true" id="" name=""
                                                            @if ($valor['acta_nacimiento'] == 'true' && !empty($valor['documento']))
                                                                checked
                                                            @endif
                                                            >
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{-- @if (($valor['curp'] == 'false' || $valor['estudio'] == 'false' || $valor['acta_nacimiento'] == 'false') ||
                                                            (empty($valor['curp'] ) || empty($valor['estudio']) || empty($valor['acta_nacimiento']) )) --}}
                                                            <div class="file-input-wrapper">
                                                                <i class="fas fa-upload file-icon"></i>
                                                                <input type="file" class="file-input" name="documentos[{{ $valor['id'] }}]" id="docAlumno{{ $valor['id'] }}">
                                                            </div>
                                                        {{-- @endif --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        {{-- @endif --}}
                                    {{-- @endfor --}}
                                </table>
                            </form>
                        @else
                            <p>No se encontraron documentos de los alumnos.</p>
                        @endif
                    </div>
                </div>
                <div class="modal-footer mt-4">
                    @if ($data_cursos != null)
                        @if ($array_rol['rol'] == 1 && ($array_rol['status_json'] == 'CAPTURA' || $array_rol['status_json'] == 'RETORNADO'))
                            <button class="btn py-1" id="btnSaveAlumnos">GUARDAR</button>
                        @endif
                    @endif

                    <button class="btn btn-info py-1" onclick="closeModal('modalAlumnosCurp')">Cerrar</button>
                </div>
            </div>
        </div>


        {{-- Modal v2 alumnos estudios--}}
        <div id="modalAlumnosEstudios" class="modal_al">
            <div class="modal-content">
                <div class="modal-header py-2">
                    {{-- <h5 class="modal-title font-weight-bold">Alumnos</h5> --}}
                    <button type="button" class="close" onclick="closeModal('modalAlumnosEstudios')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-0">
                    <p class="font-weight-bold text-center mb-1" style="font-size: 16px;">Alumnos</p>
                    <div class="scrollable-list">
                        @if (isset($search_docs['alumnos_req']) && count($search_docs['alumnos_req']) > 0)
                            <table class="table-hover">
                                <thead>
                                    <tr>
                                        <th>Comprobante de Estudios</th>
                                        <th>Nombre del Alumno</th>
                                        <th>PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($search_docs['alumnos_req'] as $key => $valor)
                                        <tr>
                                            <td class="text-center">
                                                @if ($valor['estudio'] == 'true' && !empty($valor['documento']))
                                                    <i class="fa fa-check text-success" aria-hidden="true"></i>
                                                @else
                                                    <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                            <td class="text-left"><span>{{($key+1).'.- '.$valor['alumno']}}</span></td>
                                            <td class="text-center">
                                                @if (!empty($valor['documento']))
                                                    <a href="{{$valor['documento']}}" target="_blank"><i class="fa fa-file-pdf-o fa-1x fa-lg text-danger" aria-hidden="true"></i></a>
                                                @else
                                                    <i class="fa fa-file-pdf-o fa-1x fa-lg text-light" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No se encontraron documentos de los alumnos.</p>
                        @endif
                    </div>
                </div>
                <div class="modal-footer mt-4">
                    <button class="btn btn-info py-1" onclick="closeModal('modalAlumnosEstudios')">Cerrar</button>
                </div>
            </div>
        </div>



    </div>
    {{-- fin del contenedor card --}}


    @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){

                /*Deshabilitamos la prate de convertir a mayusculas*/
                $("input[type=text], textarea, select").off("keyup");

                // Buscar grupo
                $("#btnBuscarGrupo" ).click(function(){
                    if ($("#txtbuscar").val().trim()) {
                        loader('show');
                        $("#frmBuscarGrupo").attr('action', "{{ route('expunico.principal.mostrar.post')}}");
                        $("#frmBuscarGrupo").attr("target", '_self');
                        $("#frmBuscarGrupo").submit();
                    } else {
                        alert('POR FAVOR, INGRESE UN VALOR VÁLIDO');
                    }
                });

                // Buscar grupo
                $("#btnSaveAlumnos" ).click(function(){
                    loader('show');
                    let valoresCheckboxCurp = [];
                    $('.checkbox-curp').each(function() {
                        let valor = $(this).is(':checked') ? $(this).val() : null; // Si está marcado, toma el valor; de lo contrario, establece null
                        valoresCheckboxCurp.push(valor);
                    });

                    let valoresCheckboxEstu = [];
                    $('.checkbox-estudios').each(function() {
                        let valor = $(this).is(':checked') ? $(this).val() : null; // Si está marcado, toma el valor; de lo contrario, establece null
                        valoresCheckboxEstu.push(valor);
                    });

                    let valoresCheckboxActaN = [];
                    $('.checkbox-acta').each(function() {
                        let valor = $(this).is(':checked') ? $(this).val() : null; // Si está marcado, toma el valor; de lo contrario, establece null
                        valoresCheckboxActaN.push(valor);
                    });

                    $("#checksCurp").val(JSON.stringify(valoresCheckboxCurp));
                    $("#checksEstudios").val(JSON.stringify(valoresCheckboxEstu));
                    $("#checksActaNacim").val(JSON.stringify(valoresCheckboxActaN));

                    $("#frmRequisitos").attr('action', "{{ route('expunico.save.requisitos')}}");
                    $("#frmRequisitos").attr("target", '_self');
                    $("#frmRequisitos").submit();
                });

            });
            function showModal(event, mensaje) {
                event.preventDefault();
                document.getElementById("myModal").style.display = "block";
                let span = document.getElementById("mensajeModal");
                span.textContent = mensaje;
            }

            function closeModal(id) {
                document.getElementById(id).style.display = "none";
            }


            // ocultar y mostrar botones al usar el select
            function combo_movi_dta() {
                let select_mov = $("#select_mov").val();

                switch (select_mov) {
                    case "0":
                        $("#divValid").addClass("d-none");
                        $("#divArea").addClass("d-none");
                        $("#divReturn").addClass("d-none");
                        break;
                    case "1":
                        $("#divValid").removeClass("d-none");
                        $("#divArea").addClass("d-none");
                        $("#divReturn").addClass("d-none");
                        break;
                    case "2":
                        $("#divArea").removeClass("d-none");
                        $("#divReturn").removeClass("d-none");
                        $("#divValid").addClass("d-none");
                        break;
                    case "3":;
                        $("#divValid").addClass("d-none");
                        $("#divArea").addClass("d-none");
                        $("#divReturn").addClass("d-none");
                        break;
                    default:
                        console.log('default select');
                }

            }

            //Obtener valores del formulario
            function valores_por_rol(rol, idcurso) {
                let valores = {};
                if (rol == 1) {
                    let radio1 = $('input[name="opcion1"]:checked').val(); let txtarea1 = $('#comentario_req1').val();
                    let radio2 = $('input[name="opcion2"]:checked').val(); let txtarea2 = $('#comentario_req2').val();
                    let radio3 = $('input[name="opcion3"]:checked').val(); let txtarea3 = $('#comentario_req3').val();
                    let radio4 = $('input[name="opcion4"]:checked').val(); let txtarea4 = $('#comentario_req4').val();
                    let radio5 = $('input[name="opcion5"]:checked').val(); let txtarea5 = $('#comentario_req5').val();
                    let radio6 = $('input[name="opcion6"]:checked').val(); let txtarea6 = $('#comentario_req6').val();
                    let radio7 = $('input[name="opcion7"]:checked').val(); let txtarea7 = $('#comentario_req7').val();
                    let radio8 = $('input[name="opcion_v8"]:checked').val(); let txtarea8 = $('#comentario_req_v8').val();
                    valores = {'radio1': radio1, 'radio2': radio2, 'radio3': radio3, 'radio4': radio4, 'radio5': radio5,
                    'radio6': radio6, 'radio7': radio7, 'radio8': radio8, 'txtarea1' : txtarea1, 'txtarea2' : txtarea2, 'txtarea3' : txtarea3,
                    'txtarea4' : txtarea4, 'txtarea5' : txtarea5, 'txtarea6' : txtarea6, 'txtarea7' : txtarea7, 'txtarea8' : txtarea8};
                }else if(rol == 2){
                    let radio8 = $('input[name="opcion8"]:checked').val(); let txtarea8 = $('#comentario_req8').val();
                    let radio9 = $('input[name="opcion9"]:checked').val(); let txtarea9 = $('#comentario_req9').val();
                    let radio10 = $('input[name="opcion10"]:checked').val(); let txtarea10 = $('#comentario_req10').val();
                    let radio11 = $('input[name="opcion11"]:checked').val(); let txtarea11 = $('#comentario_req11').val();
                    let radio12 = $('input[name="opcion12"]:checked').val(); let txtarea12 = $('#comentario_req12').val();
                    let radio13 = $('input[name="opcion13"]:checked').val(); let txtarea13 = $('#comentario_req13').val();
                    let radio14 = $('input[name="opcion14"]:checked').val(); let txtarea14 = $('#comentario_req14').val();
                    let radio15 = $('input[name="opcion15"]:checked').val(); let txtarea15 = $('#comentario_req15').val();
                    let radio16 = $('input[name="opcion16"]:checked').val(); let txtarea16 = $('#comentario_req16').val();
                    let radio17 = $('input[name="opcion17"]:checked').val(); let txtarea17 = $('#comentario_req17').val();
                    let radio18 = $('input[name="opcion18"]:checked').val(); let txtarea18 = $('#comentario_req18').val();
                    let radio19 = $('input[name="opcion19"]:checked').val(); let txtarea19 = $('#comentario_req19').val();
                    let radio25 = $('input[name="opcion25"]:checked').val(); let txtarea25 = $('#comentario_req25').val();
                    valores = {'radio8': radio8, 'radio9': radio9, 'radio10': radio10, 'radio11': radio11, 'radio12': radio12,
                    'radio13': radio13, 'radio14': radio14, 'radio15': radio15, 'radio16': radio16, 'radio17': radio17, 'radio18': radio18,
                    'radio19': radio19, 'radio25': radio25, 'txtarea8': txtarea8, 'txtarea9': txtarea9, 'txtarea10': txtarea10,
                    'txtarea11': txtarea11, 'txtarea12': txtarea12,'txtarea13': txtarea13, 'txtarea14': txtarea14, 'txtarea15': txtarea15,
                    'txtarea16': txtarea16, 'txtarea17': txtarea17, 'txtarea18': txtarea18, 'txtarea19': txtarea19, 'txtarea25': txtarea25};
                }else if(rol == 3){
                    let radio20 = $('input[name="opcion20"]:checked').val(); let txtarea20 = $('#comentario_req20').val();
                    let radio21 = $('input[name="opcion21"]:checked').val(); let txtarea21 = $('#comentario_req21').val();
                    let radio22 = $('input[name="opcion22"]:checked').val(); let txtarea22 = $('#comentario_req22').val();
                    let radio23 = $('input[name="opcion23"]:checked').val(); let txtarea23 = $('#comentario_req23').val();
                    let radio24 = $('input[name="opcion24"]:checked').val(); let txtarea24 = $('#comentario_req24').val();
                    valores = {'radio20': radio20, 'radio21': radio21, 'radio22': radio22, 'radio23': radio23, 'radio24': radio24,
                    'txtarea20': txtarea20, 'txtarea21': txtarea21, 'txtarea22' : txtarea22, 'txtarea23' : txtarea23, 'txtarea24' : txtarea24};
                }

                //Enviar los datos por Ajax los valores
                if (rol == 1 || rol == 2 || rol == 3) {
                    let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "valor_form": valores,
                    "rol_user" : rol,
                    'idcurso' : idcurso
                    }
                    $.ajax({
                        type:"post",
                        url: "{{ route('expunico.principal.guardar') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            // console.log(response);
                            loader('hide');
                            alert(response.mensaje);
                            if(response.status == 200){
                                location.reload();
                            }
                        }
                    });
                }else{
                    alert("Error, No cuenta con permisos para guardar datos");
                }

            }

            //Funcion Await para ejecutar funcion de guardar y validar al mismo tiempo
            async function ejecutarAsync(rol, idcurso) {
                if (rol == 1 || rol == 2 || rol == 3) {
                    loader('show');
                    try {
                        // Ejecutar subirPdfServidor y esperar a que termine
                        const resultadoSubida = await subirPdfServidor(event, rol, idcurso);
                        if (resultadoSubida ==  'Success') {
                            valores_por_rol(rol, idcurso); // Validar y guardar los radios
                        }
                    } catch (error) {
                        console.error("Error:", error);
                    }
                }else if(rol == 3){
                    valores_por_rol(rol, idcurso);
                }

            }

            //Validar formulario por departamento
            function validar_form(idcurso) {
                if (confirm("¡AL ENVIAR, NO PODRÁ REALIZAR NINGUNA MODIFICACIÓN! ¿ESTÁS SEGURO DE ENVIAR LOS DATOS A DTA PARA SU VALIDACIÓN? ")) {
                }else return;
                loader('show');
                let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        'idcurso' : idcurso
                    }
                    $.ajax({
                        type:"post",
                        url: "{{ route('expunico.envio.valid') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            //console.log(response);
                            loader('hide');
                            alert(response.mensaje);
                            if (response.status === 200) {
                                location.reload();
                            }
                        }
                    });
            }

            //Ajax subir documento pdf de los departamentos
            function subirPdfServidor(event, rol, idcurso) {
                event.preventDefault();
                return new Promise((resolve, reject) => {
                    let inputFiles = {};

                    //VINCULACION
                    if (rol == 1) {
                        let mb5 = 5 * 1024 * 1024;
                        let mb20 = 20 * 1024 * 1024;
                        let maxSize;
                        let arrayDocs = [1,3,4,'_v8']; //Documentos que van a ser obtenidos
                        let docs = [1,3,4,8];
                        for (let i = 0; i < arrayDocs.length; i++) {
                            let inputFile = document.getElementById('pdfInputDoc' + arrayDocs[i]);
                            inputFiles['doc_'+docs[i]] = inputFile;

                            //Validamos el tamaño de los documentos
                            if (inputFile.files.length > 0) {
                                let fileSize = inputFile.files[0].size;
                                maxSize = mb5;
                                if (i == 0 || i == 2) {maxSize = mb20}
                                if (fileSize > maxSize) {
                                    // alert('El archivo ' + inputFile.files[0].name + ' excede el tamaño permitido de 5 megabytes.');
                                    alert('El archivo ' + inputFile.files[0].name + ' excede el tamaño permitido de '+(maxSize / (1024 * 1024)) +' megabytes.');
                                    loader('hide');
                                    return false; //Detenemos el proceso
                                }
                            }
                        }
                        //Ejecutamos la carga de archivos de recibo de pago
                        uploadPdfRecibo(event, rol, idcurso);

                    }

                    //ACADEMICO
                    if (rol == 2) {
                        //Obtenemos los campos file
                        let fileSize;
                        let mb5 = 5 * 1024 * 1024;
                        let mb20 = 20 * 1024 * 1024;
                        let maxSize;
                        for (let i = 12; i <= 20; i++) {
                            inputFile = document.getElementById('pdfInputDoc' + i);
                            if(i == 20){
                                inputFile = document.getElementById('pdfInputDoc25');
                            }
                            inputFiles['doc_'+i] = inputFile;

                            //Validamos el tamaño de los documentos
                            if (inputFile.files.length > 0) {
                                fileSize = inputFile.files[0].size;
                                maxSize = mb5;
                                if (i == 17 || i == 18) {maxSize = mb20}

                                if (fileSize > maxSize) {
                                    alert('El archivo ' + inputFile.files[0].name + ' excede el tamaño permitido de '+(maxSize / (1024 * 1024)) +' megabytes.');
                                    loader('hide');
                                    return false; //Detenemos el proceso
                                }
                            }
                        }
                    }

                    if (rol == 3) { //Delegado
                        let maxSize = 5 * 1024 * 1024;
                        for (let i = 22; i <= 24; i++) {
                            let inputFile = document.getElementById('pdfInputDoc' + i);
                            inputFiles['doc_'+i] = inputFile;

                            //Validamos el tamaño de los documentos
                            if (inputFile.files.length > 0) {
                                let fileSize = inputFile.files[0].size;
                                if (fileSize > maxSize) {
                                    alert('El archivo ' + inputFile.files[0].name + ' excede el tamaño permitido de 5 megabytes.');
                                    loader('hide');
                                    return false; //Detenemos el proceso
                                }
                            }
                        }
                    }

                    //ENVIAMOS LOS DATOS POR AJAX
                    if (rol == 2 || rol == 1 || rol == 3) {
                        // loader('show');
                        let formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        for (let key in inputFiles) {
                            if (inputFiles.hasOwnProperty(key)) {
                                formData.append(key, inputFiles[key].files[0]);
                            }
                        }
                        formData.append('rol', rol);
                        formData.append('id_curso', idcurso);

                        $.ajax({
                            type: "POST",
                            url: "{{ route('expunico.save.pdfs') }}",
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                console.log(response);
                                if (response.status == 500) {
                                    alert(response.mensaje);
                                }
                                if (response.status == 200) {
                                    resolve("Success");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                                alert("Error al enviar el archivo. Revise su conexión a Internet");
                            }
                        });
                    }else{
                        alert("Error, No cuenta con permisos para guardar datos");
                    }

                    // Supongamos que al finalizar la subida, llamas a resolve

                });

            }

            function checkIcon(idIcon, inputPdfId) {
                let iconIndic = document.getElementById(idIcon);
                let pdfInput = document.getElementById(inputPdfId);
                if (pdfInput.files.length > 0) {
                    iconIndic.style.display = 'inline-block';

                    if(idIcon === 'iconCheck7'){ //Solo mostrar cuando es recibo de pago provisional
                        //Folio de recibo
                        let folio_recibo = prompt("Por favor, ingresa el folio de recibo:");
                        if (folio_recibo !== null) {$("#txt_folio_recibo").val(folio_recibo);}
                        else {alert("Debe ingresar el folio del recibo")}

                        //Fecha de recibo
                        let fecha_recibo = prompt("Por favor, ingresa la fecha de recibo con formato (YYYY-MM-DD)");
                        // if (fecha_recibo !== null) {$("#txt_folio_fecha").val(fecha_recibo);}
                        // else {alert("Debe ingresar una fecha.")}

                        if (fecha_recibo !== null) {
                            if (esFechaValida(fecha_recibo)) {
                                $("#txt_folio_fecha").val(fecha_recibo);
                            } else {
                                alert("Debe ingresar una fecha válida con el formato (YYYY-MM-DD).");
                            }
                        } else {
                            alert("Debe ingresar una fecha.");
                        }
                    }

                } else {
                    iconIndic.style.display = 'none';
                }
            }

            function esFechaValida(fecha) {
                const regex = /^\d{4}-\d{2}-\d{2}$/;
                if (!regex.test(fecha)) {
                    return false;
                }
                const [year, month, day] = fecha.split('-').map(Number);
                const fechaObjeto = new Date(year, month - 1, day);
                return fechaObjeto.getFullYear() === year && (fechaObjeto.getMonth() + 1) === month && fechaObjeto.getDate() === day;
            }

            //Ajax para eliminar documento
            function delete_pdf(event, radioP, url_doc, rol, idcurso) {
                event.preventDefault();
                if (confirm("¿ESTAS SEGURO DE ELIMINAR EL ARCHIVO?")) {
                }else return;

                if (rol == 1 || rol == 2 || rol == 3) {
                    loader('show');
                    let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        "urlImg": url_doc,
                        "rol_user" : rol,
                        'idcurso' : idcurso,
                        'radio' : radioP
                    }
                    $.ajax({
                        type:"post",
                        url: "{{ route('expunico.delete.pdf') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            // console.log(response);
                            loader('hide');
                            alert(response.mensaje);
                            if(response.status === 200)
                            location.reload();
                        }
                    });
                }else{
                    alert("¡ERROR, NO CUENTA CON PERMISOS PARA ELIMINAR ARCHIVO!");
                }

            }

            //Valida o retorna DTA
            function valid_return_dta(accion, idcurso, rol) {
                //let valor_area = "";
                let resul_dta = "";
                if (accion == 'retornar') {
                    //valor_area = document.getElementById('area_retorno').value;
                    //Obtenemos todos los valores de el textarea mensaje DTA
                    resul_dta = get_mensaje_dta();
                    // console.log(resul_dta);
                    if (resul_dta.conta_texto == 0) {
                        alert("Los campos 'Mensaje DTA' de las evidencias están vacíos. \nPara continuar, debe haber al menos un campo con el motivo del retorno.");
                        return false;
                    }
                    // console.log(mensajes_dta);
                }

                if (confirm("¿ESTAS SEGURO DE REALIZAR ESTA ACCIÓN?")) {
                }else return;

                if (rol == 4) {
                    loader('show');
                    let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        "rol_user" : rol,
                        'idcurso' : idcurso,
                        //'valor_area' : valor_area,
                        'accion' : accion,
                        'mensajes_dta' : resul_dta.valores_dta
                    }
                    $.ajax({
                        type:"post",
                        url: "{{ route('expunico.valid.dta') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            // console.log(response);
                            loader('hide');
                            alert(response.mensaje);
                            if(response.status === 200) location.reload();
                        }
                    });
                }else{
                    alert("¡ERROR, NO CUENTA CON PERMISOS PARA REALIZAR ESTA ACCIÓN!");
                }
            }

            function modalRequisitos(event, tipo) {
                event.preventDefault();
                console.log("entro");
                if (tipo == 'curp') {
                    document.getElementById("modalAlumnosCurp").style.display = "block";
                }else if(tipo == 'estudios'){
                    document.getElementById("modalAlumnosEstudios").style.display = "block";
                }
            }

            //Generar pdf espedientes unicos
            function genpdf_expe(id_curso) {
                let url = "{{ route('expunico.gen.pdfexpe', [':folio']) }}";
                url = url.replace(':folio', id_curso);
                window.open(url, "_blank");
            }

            function get_mensaje_dta() {
                let valores_dta = {};
                let conta_texto = 0;
                for (let i = 1; i <= 26; i++) {
                    let txtarea = $('#comentario_dta' + i).val();
                    valores_dta['txtarea' + i] = txtarea;
                    if (txtarea && txtarea.trim() !== '') {
                        conta_texto++;
                    }
                }
                return { valores_dta, conta_texto };
            }

            function loader(make) {
                if(make == 'hide') make = 'none';
                if(make == 'show') make = 'block';
                document.getElementById('loader-overlay').style.display = make;
            }

            function modalUploadDoc(event, id) {
                event.preventDefault();
                document.getElementById("modalSubirDoc").style.display = "block";
            }

            // Escucha los cambios en todo el documento para los inputs de archivos
            document.addEventListener('DOMContentLoaded', function() {
                // Escucha los cambios en todo el documento para los inputs de archivos
                document.addEventListener('change', function(event) {
                    if (event.target.classList.contains('file-input')) {
                        var icon = event.target.previousElementSibling;
                        if (event.target.files && event.target.files.length > 0) {
                            icon.classList.add('loaded');
                        } else {
                            icon.classList.remove('loaded');
                        }
                    }
                });
            });

            function uploadPdfRecibo(event, rol, idcurso) {
                event.preventDefault();
                let maxSize = 5 * 1024 * 1024;
                let inputFileRecibo = document.getElementById('pdfInputDoc7');
                let folio_recibo = document.getElementById('txt_folio_recibo').value;
                let fecha_recibo = document.getElementById('txt_folio_fecha').value;
                //Validamos que pese menos de 5 mb
                if (inputFileRecibo.files.length > 0) {
                    let fileSize = inputFileRecibo.files[0].size;
                    if (fileSize > maxSize) {
                        alert('El archivo ' + inputFileRecibo.files[0].name + ' excede el tamaño permitido de 5 megabytes.');
                        loader('hide');
                        return false; // Detenemos el proceso
                    }
                }
                //Envio de recibo
                if(folio_recibo != "" && fecha_recibo != "" && inputFileRecibo.files.length > 0){
                    let formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('file', inputFileRecibo.files[0]);
                    formData.append('rol', rol);
                    formData.append('id_curso', idcurso);
                    formData.append('folio_recibo', folio_recibo);
                    formData.append('fecha_recibo', fecha_recibo);

                    $.ajax({
                        type: "POST",
                        url: "{{ route('expunico.upload.recibo') }}",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            console.log(response);
                            if (response.status == 200) {
                                // console.log("Recibo cargado exitosamente");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            alert("Error al enviar el archivo.");
                        }
                    });

                }

            }

        </script>
    @endsection
@endsection
