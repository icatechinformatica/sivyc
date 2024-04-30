{{-- creado por MIS DANIEL MÉNDEZ CRUZ --}}
@extends('theme.sivyc.layout')
{{-- llamar a la plantilla principal --}}
@section('title', 'Cursos de Formato T enviados a Dirección de Planeación | SIVYC ICATECH')
    {{-- sección del titutlo --}}
@section('content_script_css')
    <style>
        #spinner:not([hidden]) {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #spinner::after {
            content: "";
            width: 80px;
            height: 80px;
            border: 2px solid #f3f3f3;
            border-top: 3px solid #f25a41;
            border-radius: 100%;
            will-change: transform;
            animation: spin 1s infinite linear
        }

        table tr td {
            border: 1px solid #ccc;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @media all and (max-width:500px) {
            table {
                width: 100%;
            }

            td {
                display: block;
                width: 100%;
            }

            tr {
                display: block;
                margin-bottom: 30px;
            }
        }

        /* thead tr th {
                position: sticky;
                top: 0;
                z-index: 10;
                background-color: #ffffff;
            }

            .table-responsive {
                height:600px;
                overflow:scroll;
            } */

    </style>
@endsection
{{-- seccion de un contenido css para estilos definidos del  archivo --}}
@section('content')
    <div class="container-fluid px-5">
        <div class="alert"></div>
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        {{-- row --}}
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>VALIDACIÓN DE CURSOS POR LA DIRECCIÓN DE PLANEACIÓN <strong>(FORMATO T)</strong></h2>
                    {{-- formulario de busqueda en index --}}
                    {!! Form::open(['route' => 'planeacion.formatot.index', 'method' => 'GET', 'class' => 'form-inline']) !!}
                    <select name="busqueda_unidad" id="busqueda_unidad" class="form-control mr-sm-2">
                        <option value="all">TODAS LAS UNIDADES</option>
                        @foreach ($unidadesIndex as $itemUnidadesIndex)
                            <option {{$itemUnidadesIndex->ubicacion == $unidades ? 'selected' : ''}} value="{{ $itemUnidadesIndex->ubicacion }}">{{ $itemUnidadesIndex->ubicacion }}
                            </option>
                        @endforeach
                    </select>

                    <select name="mesSearch" id="mesSearch" class="form-control mr-sm-2">
                        <option value="">-- MES A BUSCAR --</option>
                        <option {{$mesSearch == '01' ? 'selected' : ''}} value="01">ENERO</option>
                        <option {{$mesSearch == '02' ? 'selected' : ''}} value="02">FEBRERO</option>
                        <option {{$mesSearch == '03' ? 'selected' : ''}} value="03">MARZO</option>
                        <option {{$mesSearch == '04' ? 'selected' : ''}} value="04">ABRIL</option>
                        <option {{$mesSearch == '05' ? 'selected' : ''}} value="05">MAYO</option>
                        <option {{$mesSearch == '06' ? 'selected' : ''}} value="06">JUNIO</option>
                        <option {{$mesSearch == '07' ? 'selected' : ''}} value="07">JULIO</option>
                        <option {{$mesSearch == '08' ? 'selected' : ''}} value="08">AGOSTO</option>
                        <option {{$mesSearch == '09' ? 'selected' : ''}} value="09">SEPTIEMBRE</option>
                        <option {{$mesSearch == '10' ? 'selected' : ''}} value="10">OCTUBRE</option>
                        <option {{$mesSearch == '11' ? 'selected' : ''}} value="11">NOVIEMBRE</option>
                        <option {{$mesSearch == '12' ? 'selected' : ''}} value="12">DICIEMBRE</option>
                    </select>
                    {{-- formulario de busqueda en index END --}}
                    {!! Form::submit('FILTRAR', ['class' => 'btn btn-outline-info my-2 my-sm-0']) !!}
                    {!! Form::close() !!}
                </div>
                {{-- <div class="pull-right">
                </div> --}}
            </div>
        </div>
        {{-- <hr style="border-color:dimgray"> --}}
        {{-- vamos a checar los datos que enviamos a la consulta --}}
        @if (count($cursos_unidades_planeacion) > 0)
            {{-- formulario para la creación del formato t excel --}}
            <form action="{{ route('reportes.planeacion.formatot.xls') }}" method="POST" target="_self">
                @csrf
                <div class="form-row my-2">
                    <div class="form-group mb-2">
                        <button input type="submit" id="generarReporteT" name="generarReporteT" class="btn btn-success">
                            <i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i>&nbsp;
                            EXPORTAR FORMATO T
                        </button>
                    </div>
                </div>
            </form>

            {{-- formulario --}}
            <form action="{{ route('planeacion.generate.memo') }}" method="post" target="_blank" name="formPlaneacion"
                id="formPlaneacion">
                @csrf

                <input class="d-none" id="mesReport" name="mesReport" type="text" value="{{$mesSearch}}">
                <div class="form-row">
                    <div class="form-group col-md-4 mb-3">
                        <input type="text" class="form-control mr-sm-1" name="num_memo" id="num_memo"
                            placeholder="NÚMERO DE MEMORANDUM">
                    </div>
                    <div class="form-group col-md-4 mb-2">
                        <input type="text" name="filterCursos" id="filterCursos" class="form-control"
                            placeholder="BUSQUEDA POR CLAVE DE CURSO">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group mb-2">
                        <button input type="submit" id="memorandumGenerado" name="memorandumGenerado"
                            value="memorandumPositivo" class="btn btn-success">
                            <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>&nbsp;
                            MEMORANDUM POSITIVO
                        </button>
                    </div>

                    <div class="form-group mb-2">
                        <button input type="button" id="terminarProceso" name="terminarProceso" class="btn btn-info"
                            data-toggle="modal" data-target="#modalFinish">
                            <i class="fa fa-check fa-2x" aria-hidden="true"></i>&nbsp;
                            TERMINAR
                        </button>
                    </div>

                    <div class="form-group mb-2">
                        <button input type="submit" id="memorandumGenerado" name="memorandumGenerado"
                            value="memorandumNegativo" class="btn btn-danger">
                            <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>&nbsp;
                            MEMORANDUM NEGATIVO
                        </button>
                    </div>

                    <div class="form-group mb-2">
                        <button input type="button" id="regresarDTA" name="regresarDTA" class="btn btn-warning"
                            data-toggle="modal" data-target="#modalGoBackDTA">
                            <i class="fa fa-retweet fa-2x" aria-hidden="true"></i>&nbsp;
                            REGRESAR DTA
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <div class="table-responsive container-fluid mt-2">
                        <table id="table-instructor" class="table" style='width: 100%; margin-left: -1.1em;'>
                            <caption>CURSOS ENVIADOS A LA DIRECCIÓN DE PLANEACIÓN</caption>
                            <thead class="thead-dark">
                                {{-- <tr align="center">
                                    <th scope="col" colspan="33" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="32" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="32" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="31" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="33" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="34" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="33" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="34" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="33" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="2"></th>
                                </tr> --}}
                                <tr align="center">
                                    <th scope="col">N°</th>
                                    <th scope="col">SELECCIONAR &nbsp;
                                        <input type="checkbox" id="selectAll" />
                                    </th>
                                    <th scope="col" style="width: 50%">COMENTARIOS</th>
                                    <th scope="col">MES REPORTADO</th>
                                    <th scope="col">UNIDAD DE CAPACITACION</th>
                                    <th scope="col">PLANTEL</th>
                                    <th scope="col">ESPECIALIDAD</th>
                                    <th scope="col">CURSO</th>
                                    <th scope="col">CLAVE</th>
                                    <th scope="col">MOD</th>
                                    <th scope="col">DURACIÓN TOTAL EN HORAS</th>
                                    <th scope="col">TURNO</th>
                                    <th scope="col">DIA INICIO</th>
                                    <th scope="col">MES INICIO</th>
                                    <th scope="col">DIA TERMINO</th>
                                    <th scope="col">MES TERMINO</th>
                                    <th scope="col">PERIODO</th>
                                    <th scope="col">HORAS DIARIAS</th>
                                    <th scope="col">DIAS</th>
                                    <th scope="col">HORARIO</th>
                                    <th scope="col">INSCRITOS</th>
                                    <th scope="col">FEM</th>
                                    <th scope="col">MASC</th>
                                    <th scope="col">EGRESADO</th>
                                    <th scope="col">EGRESADO FEM</th>
                                    <th scope="col">EGRESADO MASC</th>
                                    <th scope="col">DESERCIÓN</th>
                                    <th scope="col">C TOTAL CURSO PERSONA</th>
                                    <th scope="col">INGRESO TOTAL</th>
                                    <th scope="col">CUOTA MIXTAL</th>
                                    <th scope="col">EXONERACION MUJER</th>
                                    <th scope="col">EXONERACION HOMBRE</th>
                                    <th scope="col">REDUCCION CUOTA MUJER</th>
                                    <th scope="col">REDUCCION CUOTA HOMBRE</th>
                                    <th scope="col">CONVENIO ESPECIFICO</th>
                                    <th scope="col">MEMO VALIDA CURSO</th>
                                    <th scope="col">ESPACIO FISICO</th>
                                    <th scope="col">INSTRUCTOR</th>
                                    <th scope="col">ESCOLARIDAD INSTRUCTOR</th>
                                    <th scope="col">DOCUMENTO ADQ</th>
                                    <th scope="col">SEXO</th>
                                    <th scope="col">MEMO VALIDACION</th>
                                    <th scope="col">MEMO EXONERACION</th>
                                    <th scope="col">EMPLEADOS</th>
                                    <th scope="col">DESEMPLEADOS</th>
                                    <th scope="col">DISCAPACITADOS</th>
                                    <th scope="col">MIGRANTE</th>
                                    <th scope="col">ADOLECENTES EN CONDICION DE CALLE</th>
                                    <th scope="col">MUJERES JEFAS DE FAMILIA</th>
                                    <th scope="col">INDIGENA</th>
                                    <th scope="col">RECLUSOS</th>
                                    <th scope="col">PROGRAMA ESTRATEGICO</th>
                                    <th scope="col">MUNICIPIO</th>
                                    <th scope="col">ZE</th>
                                    <th scope="col">REGION</th>
                                    <th scope="col">DEPENDENCIA BENEFICIADA</th>
                                    <th scope="col">CONVENIO GENERAL</th>
                                    <th scope="col">CONV SEC PUB O PRIV</th>
                                    <th scope="col">VALIDACION PAQUETERIA</th>
                                    <th scope="col">GRUPO VULNNERABLE</th>
                                    {{-- RUBRO FEDERAL--}}
                                    <th scope="col">INSC EDAD M1</th>
                                    <th scope="col">INSC EDAD H1</th>
                                    <th scope="col">INSC EDAD M2</th>
                                    <th scope="col">INSC EDAD H2</th>
                                    <th scope="col">INSC EDAD M3</th>
                                    <th scope="col">INSC EDAD H3</th>
                                    <th scope="col">INSC EDAD M4</th>
                                    <th scope="col">INSC EDAD H4</th>
                                    <th scope="col">INSC EDAD M5</th>
                                    <th scope="col">INSC EDAD H5</th>
                                    <th scope="col">INSC EDAD M6</th>
                                    <th scope="col">INSC EDAD H6</th>
                                    <th scope="col">INSC EDAD M7</th>
                                    <th scope="col">INSC EDAD H7</th>
                                    <th scope="col">INSC EDAD M8</th>
                                    <th scope="col">INSC EDAD H8</th>

                                    <th scope="col">INSC ESCOL M1</th>
                                    <th scope="col">INSC ESCOL H1</th>
                                    <th scope="col">INSC ESCOL M2</th>
                                    <th scope="col">INSC ESCOL H2</th>
                                    <th scope="col">INSC ESCOL M3</th>
                                    <th scope="col">INSC ESCOL H3</th>
                                    <th scope="col">INSC ESCOL M4</th>
                                    <th scope="col">INSC ESCOL H4</th>
                                    <th scope="col">INSC ESCOL M5</th>
                                    <th scope="col">INSC ESCOL H5</th>
                                    <th scope="col">INSC ESCOL M6</th>
                                    <th scope="col">INSC ESCOL H6</th>
                                    <th scope="col">INSC ESCOL M7</th>
                                    <th scope="col">INSC ESCOL H7</th>
                                    <th scope="col">INSC ESCOL M8</th>
                                    <th scope="col">INSC ESCOL H8</th>
                                    <th scope="col">INSC ESCOL M9</th>
                                    <th scope="col">INSC ESCOL H9</th>

                                    <th scope="col">ACRE ESCOL M1</th>
                                        <th scope="col">ACRE ESCOL H1</th>
                                        <th scope="col">ACRE ESCOL M2</th>
                                        <th scope="col">ACRE ESCOL H2</th>
                                        <th scope="col">ACRE ESCOL M3</th>
                                        <th scope="col">ACRE ESCOL H3</th>
                                        <th scope="col">ACRE ESCOL M4</th>
                                        <th scope="col">ACRE ESCOL H4</th>
                                        <th scope="col">ACRE ESCOL M5</th>
                                        <th scope="col">ACRE ESCOL H5</th>
                                        <th scope="col">ACRE ESCOL M6</th>
                                        <th scope="col">ACRE ESCOL H6</th>
                                        <th scope="col">ACRE ESCOL M7</th>
                                        <th scope="col">ACRE ESCOL H7</th>
                                        <th scope="col">ACRE ESCOL M8</th>
                                        <th scope="col">ACRE ESCOL H8</th>
                                        <th scope="col">ACRE ESCOL M9</th>
                                        <th scope="col">ACRE ESCOL H9</th>

                                        <th scope="col">DESER ESCOL M1</th>
                                        <th scope="col">DESER ESCOL H1</th>
                                        <th scope="col">DESER ESCOL M2</th>
                                        <th scope="col">DESER ESCOL H2</th>
                                        <th scope="col">DESER ESCOL M3</th>
                                        <th scope="col">DESER ESCOL H3</th>
                                        <th scope="col">DESER ESCOL M4</th>
                                        <th scope="col">DESER ESCOL H4</th>
                                        <th scope="col">DESER ESCOL M5</th>
                                        <th scope="col">DESER ESCOL H5</th>
                                        <th scope="col">DESER ESCOL M6</th>
                                        <th scope="col">DESER ESCOL H6</th>
                                        <th scope="col">DESER ESCOL M7</th>
                                        <th scope="col">DESER ESCOL H7</th>
                                        <th scope="col">DESER ESCOL M8</th>
                                        <th scope="col">DESER ESCOL H8</th>
                                        <th scope="col">DESER ESCOL M9</th>
                                        <th scope="col">DESER ESCOL H9</th>

                                    <th scope="col" style="width:50%">OBSERVACIONES</th>
                                    <th scope="col" style="width: 50%">OBSERVACIONES DTA</th>
                                </tr>
                            </thead>
                            <tbody style="height: 300px; overflow-y: auto">
                                @foreach ($cursos_unidades_planeacion as $key => $datas)
                                    <tr align="center">
                                        <td>{{ $key + 1 }}</td>
                                        <td><input type="checkbox" id="{{ $datas->id_tbl_cursos }}" name="chkcursos[]"
                                                value="{{ $datas->id_tbl_cursos }}" class="checkbx" />
                                        </td>
                                        <td>
                                            <textarea name="comentarios_planeacion[]"
                                                id="comentario_{{ $datas->id_tbl_cursos }}" cols="45" rows="3"
                                                disabled></textarea>
                                        </td>
                                        <td>{{ $datas->fechaturnado }}</td>
                                        <td>{{ $datas->unidad }}</td>
                                        <td>{{ $datas->plantel }}</td>
                                        <td>{{ $datas->espe }}</td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->curso }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->clave }}</div>
                                        </td>
                                        <td>{{ $datas->mod }}</td>
                                        <td>{{ $datas->dura }}</td>
                                        <td>{{ $datas->turno }}</td>
                                        <td>{{ $datas->diai }}</td>
                                        <td>{{ $datas->mesi }}</td>
                                        <td>{{ $datas->diat }}</td>
                                        <td>{{ $datas->mest }}</td>
                                        <td>{{ $datas->pfin }}</td>
                                        <td>{{ $datas->horas }}</td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->dia }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->horario }}</div>
                                        </td>
                                        <td>{{ $datas->tinscritos }}</td>
                                        <td>{{ $datas->imujer }}</td>
                                        <td>{{ $datas->ihombre }}</td>
                                        <td>{{ $datas->egresado }}</td>
                                        <td>{{ $datas->emujer }}</td>
                                        <td>{{ $datas->ehombre }}</td>
                                        <td>{{ $datas->desertado }}</td>
                                        <td>{{ $datas->costo }}</td>
                                        <td>{{ $datas->ctotal }}</td>
                                        <td>{{ $datas->cuotamixta }}</td>
                                        <td>{{ $datas->etmujer }}</td>
                                        <td>{{ $datas->ethombre }}</td>
                                        <td>{{ $datas->epmujer }}</td>
                                        <td>{{ $datas->ephombre }}</td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->cespecifico }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->mvalida }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->efisico }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $datas->nombre }}</div>
                                        </td>
                                        <td>{{ $datas->grado_profesional }}</td>
                                        <td>{{ $datas->estatus }}</td>
                                        <td>{{ $datas->sexo }}</td>
                                        <td>{{ $datas->memorandum_validacion }}</td>
                                        <td>{{ $datas->mexoneracion }}</td>
                                        <td>{{ $datas->empleado }}</td>
                                        <td>{{ $datas->desempleado }}</td>
                                        <td>{{ $datas->discapacidad }}</td>
                                        <td>{{ $datas->migrante }}</td>
                                        <td>{{ $datas->adolescente_calle }}</td>
                                        <td>{{ $datas->jefa_familia }}</td>
                                        <td>{{ $datas->indigena }}</td>
                                        <td>{{ $datas->cerss_nombre }}</td>
                                        <td>{{ $datas->programa }}</td>
                                        <td>{{ $datas->muni }}</td>
                                        <td>{{ $datas->ze }}</td>
                                        <td>{{ $datas->region }}</td>
                                        <td>
                                            <div style="width:300px; word-wrap: break-word">{{ $datas->depen }}</div>
                                        </td>
                                        <td>{{ $datas->cgeneral }}</td>
                                        <td>{{ $datas->sector }}</td>
                                        <td>{{ $datas->mpaqueteria }}</td>
                                        @if ($datas->grupo != NULL)
                                            <td>{{ $datas->grupo }}</td>
                                        @else
                                            <td>NINGUNO</td>
                                        @endif
                                        {{-- RUBRO FEDERAL --}}
                                        <td>{{ $datas->iem1f }}</td>
                                        <td>{{ $datas->ieh1f }}</td>
                                        <td>{{ $datas->iem2f }}</td>
                                        <td>{{ $datas->ieh2f }}</td>
                                        <td>{{ $datas->iem3f }}</td>
                                        <td>{{ $datas->ieh3f }}</td>
                                        <td>{{ $datas->iem4f }}</td>
                                        <td>{{ $datas->ieh4f }}</td>
                                        <td>{{ $datas->iem5f }}</td>
                                        <td>{{ $datas->ieh5f }}</td>
                                        <td>{{ $datas->iem6f }}</td>
                                        <td>{{ $datas->ieh6f }}</td>
                                        <td>{{ $datas->iem7f }}</td>
                                        <td>{{ $datas->ieh7f }}</td>
                                        <td>{{ $datas->iem8f }}</td>
                                        <td>{{ $datas->ieh8f }}</td>

                                        <td>{{ $datas->iesm1 }}</td>
                                        <td>{{ $datas->iesh1 }}</td>
                                        <td>{{ $datas->iesm2 }}</td>
                                        <td>{{ $datas->iesh2 }}</td>
                                        <td>{{ $datas->iesm3 }}</td>
                                        <td>{{ $datas->iesh3 }}</td>
                                        <td>{{ $datas->iesm4 }}</td>
                                        <td>{{ $datas->iesh4 }}</td>
                                        <td>{{ $datas->iesm5 }}</td>
                                        <td>{{ $datas->iesh5 }}</td>
                                        <td>{{ $datas->iesm6 }}</td>
                                        <td>{{ $datas->iesh6 }}</td>
                                        <td>{{ $datas->iesm7 }}</td>
                                        <td>{{ $datas->iesh7 }}</td>
                                        <td>{{ $datas->iesm8 }}</td>
                                        <td>{{ $datas->iesh8 }}</td>
                                        <td>{{ $datas->iesm9 }}</td>
                                        <td>{{ $datas->iesh9 }}</td>

                                        <td>{{ $datas->aesm1 }}</td>
                                            <td>{{ $datas->aesh1 }}</td>
                                            <td>{{ $datas->aesm2 }}</td>
                                            <td>{{ $datas->aesh2 }}</td>
                                            <td>{{ $datas->aesm3 }}</td>
                                            <td>{{ $datas->aesh3 }}</td>
                                            <td>{{ $datas->aesm4 }}</td>
                                            <td>{{ $datas->aesh4 }}</td>
                                            <td>{{ $datas->aesm5 }}</td>
                                            <td>{{ $datas->aesh5 }}</td>
                                            <td>{{ $datas->aesm6 }}</td>
                                            <td>{{ $datas->aesh6 }}</td>
                                            <td>{{ $datas->aesm7 }}</td>
                                            <td>{{ $datas->aesh7 }}</td>
                                            <td>{{ $datas->aesm8 }}</td>
                                            <td>{{ $datas->aesh8 }}</td>
                                            <td>{{ $datas->aesm9 }}</td>
                                            <td>{{ $datas->aesh9 }}</td>

                                            <td>{{ $datas->naesm1 }}</td>
                                            <td>{{ $datas->naesh1 }}</td>
                                            <td>{{ $datas->naesm2 }}</td>
                                            <td>{{ $datas->naesh2 }}</td>
                                            <td>{{ $datas->naesm3 }}</td>
                                            <td>{{ $datas->naesh3 }}</td>
                                            <td>{{ $datas->naesm4 }}</td>
                                            <td>{{ $datas->naesh4 }}</td>
                                            <td>{{ $datas->naesm5 }}</td>
                                            <td>{{ $datas->naesh5 }}</td>
                                            <td>{{ $datas->naesm6 }}</td>
                                            <td>{{ $datas->naesh6 }}</td>
                                            <td>{{ $datas->naesm7 }}</td>
                                            <td>{{ $datas->naesh7 }}</td>
                                            <td>{{ $datas->naesm8 }}</td>
                                            <td>{{ $datas->naesh8 }}</td>
                                            <td>{{ $datas->naesm9 }}</td>
                                            <td>{{ $datas->naesh9 }}</td>

                                        <td>
                                            <div style="width:900px; word-wrap: break-word">{{ $datas->tnota }}</div>
                                        </td>
                                        <td>
                                            <div style="width: 300px; word-wrap: break-word">
                                                {{ $datas->observacion_envio_to_planeacion }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="unidad_busqueda" id="unidad_busqueda" value="{{ $unidades }}">
            </form>
            {{-- formulario END --}}
        @else
            <h2 class="my-5"><b>NO SE ENCONTRARON REGISTROS</b></h2>
        @endif
        {{-- checamos que haya datos en la consulta end --}}
    </div>
    <br>
    {{-- spinner --}}
    <div hidden id="spinner"></div>
    {{-- spinner END --}}

    {{-- MODAL DE ENVIO --}}
    <div class="modal fade" id="modalGoBackDTA" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-info" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enviar_cursos_dta"><b>ADJUNTAR Y REGRESAR A DIRECCIÓN TÉCNICA ACADÉMICA </b>
                    </h5>
                </div>
                <form id="formGoBackDTA" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="file" name="memorandumNegativoFile" id="memorandumNegativoFile"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="send_to_dta">ENVIAR</button>
                        <button type="button" id="close_btn_modal_modal_go_back_dta" class="btn btn-danger">CERRAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DE TERMINO --}}
    <div class="modal fade" id="modalFinish" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-info" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enviar_cursos_dta"><b>ADJUNTAR Y TERMINAR PROCESO </b></h5>
                </div>
                <form id="formFinish" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="file" name="memorandumPositivoFile" id="memorandumPositivoFile"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="field_wrapper_planeacion_terminar"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="send_to_finish">ENVIAR</button>
                        <button type="button" id="close_btn_modal_modal_finish" class="btn btn-danger">CERRAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
{{-- contenido js --}}
@section('script_content_js')
    <script type="text/javascript">
        $(function() {
            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            }, 'El TAMAÑO DEL ARCHIVO DEBE SER MENOR A {0} bytes.');

            document.querySelector('#spinner').setAttribute('hidden', '');
            // se agrega el método
            // CHECAR TODOS LOS CHECKBOX AL MOMENTO
            $("#selectAll").click(function() {
                $("input[type=checkbox]").not(this).prop("checked", this.checked);
                $("input[type=checkbox]").each(function() {
                    if ($(this).is(":checked")) {
                        if ($(this).attr("id") != 'selectAll') {
                            var id = $(this).attr("id").split("_");
                            id = id[id.length - 1];
                            $('#comentario_' + id).attr('disabled', false);
                        }
                    } else {
                        if ($(this).attr("id") != 'selectAll') {
                            var id = $(this).attr("id").split("_");
                            id = id[id.length - 1];
                            $('#comentario_' + id).attr('disabled', true);
                        }
                    }
                })
            });

            // trabajar con el checkbox
            $("input.checkbx").change(function() {
                if (this.checked) {
                    var id = $(this).attr("id").split("_");
                    id = id[id.length - 1];
                    $('#comentario_' + id).attr('disabled', false);
                } else {
                    var id = $(this).attr("id").split("_");
                    id = id[id.length - 1];
                    $('#comentario_' + id).attr('disabled', true);
                }
            });

            // filtrado de clave del curso
            $("#filterCursos").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#table-instructor tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            // filtrado de la clave del curso
            $('#close_btn_modal_modal_go_back_dta').click(function() {
                $("#modalGoBackDTA").modal("hide");
            });
            $('#close_btn_modal_modal_finish').click(function() {
                $("#modalFinish").modal("hide");
            });

            $('#send_to_dta').click(function() {
                $('#formGoBackDTA').validate({
                    rules: {
                        "memorandumNegativoFile": {
                            required: true,
                            extension: "pdf",
                            filesize: 2000000
                        }
                    },
                    messages: {
                        "memorandumNegativoFile": {
                            required: "ARCHIVO REQUERIDO",
                            accept: "SÓLO SE ACEPTAN DOCUMENTOS PDF"
                        }
                    },
                    submitHandler: function(form, event) {
                        event.preventDefault();
                        var chkCursos = new Array();
                        var formData = new FormData(form);
                        $('input[name="chkcursos[]"]:checked').each(function() {
                            chkCursos.push(this.value);
                        });
                        $('textarea[name="comentarios_planeacion[]"]').each(function() {
                            if (!$(this).prop('disabled')) {
                                formData.append("comentariosPlaneacion[]", this.value);
                            }
                        });
                        var numero_memo = $('#num_memo').val();

                        formData.append("_token", $("meta[name='csrf-token']").attr("content"));
                        formData.append("checkCursos", chkCursos);
                        formData.append("numero_memo", numero_memo);
                        var _url = "{{ route('planeacion.send.to.dta') }}";
                        var requested = $.ajax({
                            url: _url,
                            method: 'POST',
                            data: formData,
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            beforeSend: function() {
                                $("#modalGoBackDTA").modal("hide");
                                document.querySelector("#spinner").removeAttribute(
                                    'hidden');
                            },
                            success: function(response) {

                                if (response === 'DONE') {
                                    $("#dtaform").trigger("reset");
                                    $(".alert").addClass("alert-success");
                                    $(".alert").append(
                                        "<b>CURSOS ENVIADOS A DIRECCIÓN TÉCNICA ACADÉMICA PARA VALIDACIÓN</b>"
                                    );
                                    // redireccionar después de 5 segundos
                                    setTimeout(function() {
                                        window.location.href =
                                            "{{ route('planeacion.formatot.index') }}";
                                    }, 2000);
                                } else if (response === 'EMPTYCURSOS') {
                                    $(".alert").addClass("alert-danger");
                                    $(".alert").append(
                                        "<b>LOS CURSOS NO ESTÁN SELECCIONADOS Y NO SE PUEDE REALIZAR EL PROCESO</b>"
                                    );
                                } else if (response === 'EMPTYNUMMEMO') {
                                    $(".alert").addClass("alert-danger");
                                    $(".alert").append(
                                        "<b>EL PROCESO NO SE PUEDE REALIZAR DEBIDO A QUE NO SE AGREGO EN NÚMERO DE MEMORANDUM</b>"
                                    );
                                }
                            },
                            complete: function(data) {
                                // escondemos el modales
                                document.querySelector('#spinner').setAttribute(
                                    'hidden', '');
                            },
                            error: function(jqXHR, textStatus) {
                                //jsonValue = jQuery.parseJSON( jqXHR.responseText );
                                //document.querySelector('#spinner').setAttribute('hidden', '');
                                console.log(jqXHR.responseText);
                                alert("Hubo un error: " + jqXHR.status);
                            }
                        });
                        $.when(requested).then(function(data, textStatus, jqXHR) {
                            if (jqXHR.status === 200) {
                                document.querySelector('#spinner').setAttribute(
                                    'hidden', '');
                            }
                        });
                    }
                });
            });
            // ENVIAR Y TERMINAR PROCESO CLICK
            $('#send_to_finish').click(function() {
                $('#formFinish').validate({
                    rules: {
                        "memorandumPositivoFile": {
                            required: true,
                            extension: "pdf",
                            filesize: 2000000
                        }
                    },
                    messages: {
                        "memorandumPositivoFile": {
                            required: "ARCHIVO REQUERIDO",
                            accept: "SÓLO SE ACEPTAN DOCUMENTOS PDF"
                        }
                    },
                    submitHandler: function(forms, events) {
                        events.preventDefault();
                        var chkCursos = new Array();
                        var formData = new FormData(forms);
                        $('input[name="chkcursos[]"]:checked').each(function() {
                            chkCursos.push(this.value);
                        });
                        $('textarea[name="comentarios_planeacion[]"]').each(function() {
                            if (!$(this).prop('disabled')) {
                                formData.append("comentariosPlaneacionTerminar[]", this
                                    .value);
                            }
                        });
                        var numero_memo = $('#num_memo').val();

                        formData.append("_token", $("meta[name='csrf-token']").attr("content"));
                        formData.append("checkCursos", chkCursos);
                        formData.append("numero_memo", numero_memo);
                        var _urlFinish = "{{ route('planeacion.finish') }}";
                        var requested = $.ajax({
                            url: _urlFinish,
                            method: 'POST',
                            data: formData,
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            beforeSend: function() {
                                $("#modalFinish").modal("hide");
                                document.querySelector("#spinner").removeAttribute(
                                    'hidden');
                            },
                            success: function(response) {
                                console.log(response);
                                if (response === 'DONE') {
                                    $("#dtaform").trigger("reset");
                                    $(".alert").addClass("alert-success");
                                    $(".alert").append(
                                        "<b>CURSOS ENVIADOS A DIRECCIÓN TÉCNICA ACADÉMICA PARA VALIDACIÓN</b>"
                                    );
                                    // redireccionar después de 5 segundos
                                    setTimeout(function() {
                                        window.location.href =
                                            "{{ route('planeacion.formatot.index') }}";
                                    }, 2000);
                                } else if (response === 'EMPTYCURSOS') {
                                    $(".alert").addClass("alert-danger");
                                    $(".alert").append(
                                        "<b>LOS CURSOS NO ESTÁN SELECCIONADOS Y NO SE PUEDE REALIZAR EL PROCESO</b>"
                                    );
                                } else if (response === 'EMPTYNUMMEMOs') {
                                    $(".alert").addClass("alert-danger");
                                    $(".alert").append(
                                        "<b>EL PROCESO NO SE PUEDE REALIZAR DEBIDO A QUE NO SE AGREGO EN NÚMERO DE MEMORANDUM</b>"
                                    );
                                }
                            },
                            complete: function(data) {
                                // escondemos el modales
                                document.querySelector('#spinner').setAttribute(
                                    'hidden', '');
                            },
                            error: function(jqXHR, textStatus) {
                                //jsonValue = jQuery.parseJSON( jqXHR.responseText );
                                //document.querySelector('#spinner').setAttribute('hidden', '');
                                console.log(jqXHR.responseText);
                                alert("Hubo un error: " + jqXHR.responseText);
                            }
                        });
                        $.when(requested).then(function(data, textStatus, jqXHR) {
                            if (jqXHR.status === 200) {
                                document.querySelector('#spinner').setAttribute(
                                    'hidden', '');
                            }
                        });
                    }
                });
            });

            $('#formPlaneacion').validate({
                rules: {
                    num_memo: {
                        required: true
                    },
                },
                messages: {
                    num_memo: {
                        required: "CAMPO REQUERIDO"
                    },
                }
            })
        });

    </script>
@endsection
{{-- contenido js END --}}
