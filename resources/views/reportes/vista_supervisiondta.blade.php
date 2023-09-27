<!--Creado por Julio Alcaraz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Cursos enviados a Dirección Técnica Académica | SIVyC Icatech')
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

    </style>
@endsection
<!--seccion-->
@section('content')
    <div class="container-fluid px-5 g-pt-30">
        {{-- información sobre la entrega del formato t para unidades --}}
        <div class="alert {{ $diasParaEntrega <= 5 ? 'alert-warning' : 'alert-info' }}" role="alert">
            <b>LA FECHA LÍMITE DEL PERÍODO DE {{ $mesInformar }} PARA EL ENVÍO DEL FORMATO T DE LAS UNIDADES
                CORRESPONDIENTES ES EL <strong>{{ $fechaEntregaFormatoT }}</strong>; FALTAN
                <strong>{{ $diasParaEntrega }}</strong> DÍAS</b>
        </div>
        {{-- información sobre la entrega del formato t para unidades END --}}
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
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>VALIDACIÓN DE CURSOS DE DIRECCIÓN TÉCNICA ACADÉMICA <strong>(DIRECCIÓN)</strong></h2>

                    {!! Form::open(['route' => 'validacion.dta.revision.cursos.indice', 'method' => 'GET', 'class' => 'form-inline']) !!}
                        <select name="busqueda_unidad" class="form-control mr-sm-2" id="busqueda_unidad">
                            <option value="all">TODAS LAS UNIDADES</option>
                            @foreach ($unidades as $itemUnidades)
                                <option {{$itemUnidades->unidad == $unidades_busqueda ? 'selected' : ''}} value="{{ $itemUnidades->unidad }}">{{ $itemUnidades->unidad }}</option>
                            @endforeach
                        </select>

                        <select name="mesSearchD" id="mesSearchD" class="form-control mr-sm-2">
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

                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">FILTRAR</button>
                    @if (isset($mesSearch))
                        <div>
                            <a class="btn btn-danger" id="resumen_unidad" name="resumen_unidad" data-toggle="modal" data-placement="top" data-target="#resumenUnidadModal" data-id='["{{$mesSearch}}","{{$unidades_busqueda}}"]'>
                                <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>
                                &nbsp;MEMORANDUM RESPUESTA A UNIDAD
                            </a>
                            <a class="btn btn-danger" id="subir_resumen_unidad" name="subir_resumen_unidad" data-toggle="modal" data-placement="top" data-target="#subirResumenUnidadModal" data-id='["{{$mesSearch}}","{{$unidades_busqueda}}"]'>
                                <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>
                                &nbsp;CARGAR RESPUESTA A UNIDAD
                            </a>
                            @if(isset($formato_respuesta->resumen_formatot_unidad))
                                <a class="btn btn-danger" id="subir_resumen_unidad" name="subir_resumen_unidad" data-toggle="modal" data-placement="top" data-target="#subirResumenUnidadModal" data-id='["{{$mesSearch}}","{{$unidades_busqueda}}"]'>
                                    <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>
                                    &nbsp; VER PDF CARGADO
                                </a>
                            @endif
                        </div>
                    @endif
                    {!! Form::close() !!}
                </div>

                <div class="pull-right">

                </div>
            </div>
        </div>
        {{-- <div>
            <a class="btn btn-danger" id="retornar_fisico" name="retornar_fisico" data-toggle="modal" data-placement="top" data-target="#resumenUnidadModal" data-id='{{$mesSearch}}'>
                <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>
                &nbsp;MEMORANDUM REPORTE A UNIDAD
            </a>
        </div> --}}

        @if (count($cursos_validar) > 0)
            <div class="form-row my-3">
                <div class="form-group mr-3">
                    {{-- target="_self" --}}
                    <form action=" {{ route('reportes.formatot.director.dta.xls') }}" method="POST">
                        @csrf
                        <input id="mesSearch" name="mesSearch" class="d-none" type="text" value="{{$mesSearch}}">
                        <input id="unidadD" name="unidadD" class="d-none" type="text" value="{{$unidades_busqueda}}">

                        <button type="submit" class="btn btn-success my-2 my-sm-0 waves-effect waves-light"
                            id="validarDireccionDta" name="validarDireccionDta" value="generarMemoPlaneacion">
                            <i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i>&nbsp;
                            REPORTE FORMATO T
                        </button>
                    </form>
                </div>

                <div class="form-group">
                    @foreach ($memorandum as $key)
                        @if ($key->memorandum != null)
                            <a href="{{ $key->memorandum }}" target="_blank"
                                class="btn btn-danger btn-circle m-1 btn-circle-sm"
                                title="DESCARGAR MEMORANDUM N° {{ $key->num_memo }}">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;
                                MEMORANDUM {{ $key->num_memo }}
                            </a>
                            <small>{{$key->unidad}}</small>
                        @endif
                    @endforeach
                </div>
            </div>

            <form id="formSendDtaTo" method="POST" action="{{ route('validacion.dta.cursos.envio.planeacion') }}">
                @csrf

                <input class="d-none" id="txtUnity" name="txtUnity" type="text" value="{{$mesSearch}}">
                <div class="form-row">
                    <div class="form-group col-md-4 mb-3">
                        <input type="text" class="form-control mr-sm-1" name="num_memo_devolucion" id="num_memo_devolucion"
                            placeholder="NÚMERO DE MEMORANDUM PARA ENVÍO A PLANEACIÓN">
                    </div>
                    <div class="form-group col-md-4 mb-2">
                        <input type="text" name="filterClaveCurso" id="filterClaveCurso" class="form-control"
                            placeholder="BUSQUEDA POR CLAVE DE CURSO">
                    </div>
                </div>

                {{-- <div class="form-row">
                    <div class="form-group col-md-8 mb-3">
                        <input type="text" class="form-control mr-sm-1" name="num_memo_devolucion" id="num_memo_devolucion"
                            placeholder="NÚMERO DE MEMORANDUM PARA ENVÍO A PLANEACIÓN">
                    </div>
                </div> --}}

                <input class="d-none" id="totalCursos" name="totalCursos" type="text" value="{{count($cursos_validar)}}">
                <div class="form-row">
                    <div class="form-group mb-2">
                        <button type="submit" class="btn btn-danger my-2 my-sm-0 waves-effect waves-light"
                            id="validarDireccionDta" name="validarDireccionDta" value="generarMemoPlaneacion">
                            <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>
                            MEMORANDUM PLANEACIÓN
                        </button>
                    </div>

                    {{-- @can('enviar.dta.planeacion') --}}
                    <div class="form-group mb-2">
                        <button input type="button" id="btnEnviarPlaneacion" name="btnEnviarPlaneacion"
                            value="EnviarPlaneacion" class="btn btn-info my-2 my-sm-0 waves-effect waves-light"
                            data-toggle="modal" data-target="#exampleModalCenter">
                            <i class="fa fa-paper-plane fa-2x" aria-hidden="true"></i>&nbsp;
                            ENVIAR A PLANEACIÓN
                        </button>
                    </div>
                    {{-- @endcan --}}

                    <div class="form-group mb-2">
                        <button input type="submit" id="validarDireccionDta" name="validarDireccionDta"
                            value="RegresarEnlaceDta" class="btn btn-warning my-2 my-sm-0 waves-effect waves-light">
                            <i class="fa fa-retweet fa-2x" aria-hidden="true"></i>&nbsp;
                            REGRESAR A LOS ENLACES
                        </button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="table-responsive container-fluid mt-2">
                        <table id="table-instructor" class="table" style='width: 100%; margin-left: -1.1em;'>
                            <caption>CURSOS VALIDADOS PARA ENVIAR A LA DIRECCIÓN DE PLANEACIÓN</caption>
                            <thead class="thead-dark">
                                <tr align="center">
                                    <th scope="col" colspan="32" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="32" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="32" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="31" style="background-color: #621032;">FEDERAL</th>
                                    <th scope="col" colspan="33" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="34" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="33" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="34" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="33" style="background-color: #AF9A5A;">ESTATAL</th>
                                    <th scope="col" colspan="2"></th>
                                </tr>
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
                                    <th scope="col">INDIGENA</th>
                                    <th scope="col">ETNIA</th>
                                    <th scope="col">PROGRAMA ESTRATEGICO</th>
                                    <th scope="col">MUNICIPIO</th>
                                    <th scope="col">ZE</th>
                                    <th scope="col">REGION</th>
                                    <th scope="col">DEPENDENCIA BENEFICIADA</th>
                                    <th scope="col">CONVENIO GENERAL</th>
                                    <th scope="col">CONV SEC PUB O PRIV</th>
                                    <th scope="col">VALIDACION PAQUETERIA</th>
                                    <th scope="col">GRUPO VULNERABLE</th>
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

                                    <th scope="col">DESC ESCOL M1</th>
                                    <th scope="col">DESC ESCOL H1</th>
                                    <th scope="col">DESC ESCOL M2</th>
                                    <th scope="col">DESC ESCOL H2</th>
                                    <th scope="col">DESC ESCOL M3</th>
                                    <th scope="col">DESC ESCOL H3</th>
                                    <th scope="col">DESC ESCOL M4</th>
                                    <th scope="col">DESC ESCOL H4</th>
                                    <th scope="col">DESC ESCOL M5</th>
                                    <th scope="col">DESC ESCOL H5</th>
                                    <th scope="col">DESC ESCOL M6</th>
                                    <th scope="col">DESC ESCOL H6</th>
                                    <th scope="col">DESC ESCOL M7</th>
                                    <th scope="col">DESC ESCOL H7</th>
                                    <th scope="col">DESC ESCOL M8</th>
                                    <th scope="col">DESC ESCOL H8</th>
                                    <th scope="col">DESC ESCOL M9</th>
                                    <th scope="col">DESC ESCOL H9</th>

                                    {{-- RUBRO ESTATAL --}}
                                    <th scope="col">INSCRITOS</th>
                                    <th scope="col">FEM</th>
                                    <th scope="col">MASC</th>
                                    <th scope="col">LGBTTTI+</th>
                                    <th scope="col">EGRESADO</th>
                                    <th scope="col">EGRESADO FEM</th>
                                    <th scope="col">EGRESADO MASC</th>
                                    <th scope="col">EGRESADO LGBTTTI+</th>
                                    <th scope="col">EXONERACION MUJER</th>
                                    <th scope="col">EXONERACION HOMBRE</th>
                                    <th scope="col">EXONERACION LGBTTTI+</th>
                                    <th scope="col">REDUCCION CUOTA MUJER</th>
                                    <th scope="col">REDUCCION CUOTA HOMBRE</th>
                                    <th scope="col">REDUCCION CUOTA LGBTTTI+</th>

                                    <th scope="col">INSC EDAD M1</th>
                                    <th scope="col">INSC EDAD H1</th>
                                    <th scope="col">INSC EDAD L1</th>
                                    <th scope="col">INSC EDAD M2</th>
                                    <th scope="col">INSC EDAD H2</th>
                                    <th scope="col">INSC EDAD L2</th>
                                    <th scope="col">INSC EDAD M3</th>
                                    <th scope="col">INSC EDAD H3</th>
                                    <th scope="col">INSC EDAD L3</th>
                                    <th scope="col">INSC EDAD M4</th>
                                    <th scope="col">INSC EDAD H4</th>
                                    <th scope="col">INSC EDAD L4</th>

                                    <th scope="col">INSC ESCOL M1</th>
                                    <th scope="col">INSC ESCOL H1</th>
                                    <th scope="col">INSC ESCOL L1</th>
                                    <th scope="col">INSC ESCOL M2</th>
                                    <th scope="col">INSC ESCOL H2</th>
                                    <th scope="col">INSC ESCOL L2</th>
                                    <th scope="col">INSC ESCOL M3</th>
                                    <th scope="col">INSC ESCOL H3</th>
                                    <th scope="col">INSC ESCOL L3</th>
                                    <th scope="col">INSC ESCOL M4</th>
                                    <th scope="col">INSC ESCOL H4</th>
                                    <th scope="col">INSC ESCOL L4</th>
                                    <th scope="col">INSC ESCOL M5</th>
                                    <th scope="col">INSC ESCOL H5</th>
                                    <th scope="col">INSC ESCOL L5</th>
                                    <th scope="col">INSC ESCOL M6</th>
                                    <th scope="col">INSC ESCOL H6</th>
                                    <th scope="col">INSC ESCOL L6</th>
                                    <th scope="col">INSC ESCOL M7</th>
                                    <th scope="col">INSC ESCOL H7</th>
                                    <th scope="col">INSC ESCOL L7</th>
                                    <th scope="col">INSC ESCOL M8</th>
                                    <th scope="col">INSC ESCOL H8</th>
                                    <th scope="col">INSC ESCOL L8</th>
                                    <th scope="col">INSC ESCOL M9</th>
                                    <th scope="col">INSC ESCOL H9</th>
                                    <th scope="col">INSC ESCOL L9</th>

                                    <th scope="col">ACRE ESCOL M1</th>
                                    <th scope="col">ACRE ESCOL H1</th>
                                    <th scope="col">ACRE ESCOL L1</th>
                                    <th scope="col">ACRE ESCOL M2</th>
                                    <th scope="col">ACRE ESCOL H2</th>
                                    <th scope="col">ACRE ESCOL L2</th>
                                    <th scope="col">ACRE ESCOL M3</th>
                                    <th scope="col">ACRE ESCOL H3</th>
                                    <th scope="col">ACRE ESCOL L3</th>
                                    <th scope="col">ACRE ESCOL M4</th>
                                    <th scope="col">ACRE ESCOL H4</th>
                                    <th scope="col">ACRE ESCOL L4</th>
                                    <th scope="col">ACRE ESCOL M5</th>
                                    <th scope="col">ACRE ESCOL H5</th>
                                    <th scope="col">ACRE ESCOL L5</th>
                                    <th scope="col">ACRE ESCOL M6</th>
                                    <th scope="col">ACRE ESCOL H6</th>
                                    <th scope="col">ACRE ESCOL L6</th>
                                    <th scope="col">ACRE ESCOL M7</th>
                                    <th scope="col">ACRE ESCOL H7</th>
                                    <th scope="col">ACRE ESCOL L7</th>
                                    <th scope="col">ACRE ESCOL M8</th>
                                    <th scope="col">ACRE ESCOL H8</th>
                                    <th scope="col">ACRE ESCOL L8</th>
                                    <th scope="col">ACRE ESCOL M9</th>
                                    <th scope="col">ACRE ESCOL H9</th>
                                    <th scope="col">ACRE ESCOL L9</th>

                                    <th scope="col">DESC ESCOL M1</th>
                                    <th scope="col">DESC ESCOL H1</th>
                                    <th scope="col">DESC ESCOL L1</th>
                                    <th scope="col">DESC ESCOL M2</th>
                                    <th scope="col">DESC ESCOL H2</th>
                                    <th scope="col">DESC ESCOL L2</th>
                                    <th scope="col">DESC ESCOL M3</th>
                                    <th scope="col">DESC ESCOL H3</th>
                                    <th scope="col">DESC ESCOL L3</th>
                                    <th scope="col">DESC ESCOL M4</th>
                                    <th scope="col">DESC ESCOL H4</th>
                                    <th scope="col">DESC ESCOL L4</th>
                                    <th scope="col">DESC ESCOL M5</th>
                                    <th scope="col">DESC ESCOL H5</th>
                                    <th scope="col">DESC ESCOL L5</th>
                                    <th scope="col">DESC ESCOL M6</th>
                                    <th scope="col">DESC ESCOL H6</th>
                                    <th scope="col">DESC ESCOL L6</th>
                                    <th scope="col">DESC ESCOL M7</th>
                                    <th scope="col">DESC ESCOL H7</th>
                                    <th scope="col">DESC ESCOL L7</th>
                                    <th scope="col">DESC ESCOL M8</th>
                                    <th scope="col">DESC ESCOL H8</th>
                                    <th scope="col">DESC ESCOL L8</th>
                                    <th scope="col">DESC ESCOL M9</th>
                                    <th scope="col">DESC ESCOL H9</th>
                                    <th scope="col">DESC ESCOL L9</th>

                                    <th scope="col">GV AFROMEX M</th>
                                    <th scope="col">GV AFROMEX H</th>
                                    <th scope="col">GV AFROMEX L</th>
                                    <th scope="col">GV DESPLAZADAS M</th>
                                    <th scope="col">GV DESPLAZADAS H</th>
                                    <th scope="col">GV DESPLAZADAS L</th>
                                    <th scope="col">GV EMBARAZADAS M</th>
                                    <th scope="col">GV EMBARAZADAS H</th>
                                    <th scope="col">GV EMBARAZADAS L</th>
                                    <th scope="col">GV SIT CALLE M</th>
                                    <th scope="col">GV SIT CALLE H</th>
                                    <th scope="col">GV SIT CALLE L</th>
                                    <th scope="col">GV ESTUDIANTES M</th>
                                    <th scope="col">GV ESTUDIANTES H</th>
                                    <th scope="col">GV ESTUDIANTES L</th>
                                    <th scope="col">GV FAM VIC M</th>
                                    <th scope="col">GV FAM VIC H</th>
                                    <th scope="col">GV FAM VIC L</th>
                                    <th scope="col">GV INDIGENA M</th>
                                    <th scope="col">GV INDIGENA H</th>
                                    <th scope="col">GV INDIGENA L</th>
                                    <th scope="col">GV JEFA FAM M</th>
                                    <th scope="col">GV JEFA FAM H</th>
                                    <th scope="col">GV JEFA FAM L</th>
                                    <th scope="col">GV MIGRANTE M</th>
                                    <th scope="col">GV MIGRANTE H</th>
                                    <th scope="col">GV MIGRANTE L</th>
                                    <th scope="col">GV LESBIANA M</th>
                                    <th scope="col">GV LESBIANA H</th>
                                    <th scope="col">GV LESBIANA L</th>
                                    <th scope="col">GV CERSS M</th>
                                    <th scope="col">GV CERSS H</th>
                                    <th scope="col">GV CERSS L</th>
                                    <th scope="col">GV TRANS M</th>
                                    <th scope="col">GV TRANS H</th>
                                    <th scope="col">GV TRANS L</th>
                                    <th scope="col">GV TRAB HOGAR M</th>
                                    <th scope="col">GV TRAB HOGAR H</th>
                                    <th scope="col">GV TRAB HOGAR L</th>
                                    <th scope="col">GV TRAB SEX M</th>
                                    <th scope="col">GV TRAB SEX H</th>
                                    <th scope="col">GV TRAB SEX L</th>
                                    <th scope="col">GV VICT VIOLENCIA M</th>
                                    <th scope="col">GV VICT VIOLENCIA H</th>
                                    <th scope="col">GV VICT VIOLENCIA L</th>
                                    <th scope="col">GV DISC VISUAL M</th>
                                    <th scope="col">GV DISC VISUAL H</th>
                                    <th scope="col">GV DISC VISUAL L</th>
                                    <th scope="col">GV DISC AUDI M</th>
                                    <th scope="col">GV DISC AUDI H</th>
                                    <th scope="col">GV DISC AUDI L</th>
                                    <th scope="col">GV DISC HABLA M</th>
                                    <th scope="col">GV DISC HABLA H</th>
                                    <th scope="col">GV DISC HABLA L</th>
                                    <th scope="col">GV DISC MOTRIZ M</th>
                                    <th scope="col">GV DISC MOTRIZ H</th>
                                    <th scope="col">GV DISC MOTRIZ L</th>
                                    <th scope="col">GV DISC MENTAL M</th>
                                    <th scope="col">GV DISC MENTAL H</th>
                                    <th scope="col">GV DISC MENTAL L</th>
                                    {{-- FIN RUBRO ESTATAL --}}

                                    <th scope="col" style="width:50%">OBSERVACIONES</th>
                                    <th scope="col" style="width:50%">OBSERVACIONES ENLACES</th>
                                </tr>
                            </thead>
                            <tbody style="height: 300px; overflow-y: auto">
                                @foreach ($cursos_validar as $key => $datas)
                                    <tr align="center">
                                        <td>{{$key + 1}}</td>
                                        <td><input type="checkbox" id="{{ $datas->id_tbl_cursos }}" name="chkcursos[]"
                                                value="{{ $datas->id_tbl_cursos }}" class="checkbx" /></td>
                                        </td>
                                        <td>
                                            <textarea name="comentarios_direccion_dta[]"
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
                                        <td>{{ $datas->indigena }}</td>
                                        <td>{{ $datas->etnia }}</td>
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

                                        {{-- RUBRO ESTATAL --}}
                                        <td>{{ $datas->tinscritos }}</td>
                                        <td>{{ $datas->imujerest }}</td>
                                        <td>{{ $datas->ihombreest }}</td>
                                        <td>{{ $datas->ilgbt }}</td>
                                        <td>{{ $datas->egresado }}</td>
                                        <td>{{ $datas->emujer }}</td>
                                        <td>{{ $datas->ehombre }}</td>
                                        <td>{{ $datas->elgbt }}</td>
                                        <td>{{ $datas->etmujerest }}</td>
                                        <td>{{ $datas->ethombreest }}</td>
                                        <td>{{ $datas->etlgbt }}</td>
                                        <td>{{ $datas->epmujerest }}</td>
                                        <td>{{ $datas->ephombreest }}</td>
                                        <td>{{ $datas->eplgbt }}</td>

                                        <td>{{ $datas->iem1 }}</td>
                                        <td>{{ $datas->ieh1 }}</td>
                                        <td>{{ $datas->iel1 }}</td>
                                        <td>{{ $datas->iem2 }}</td>
                                        <td>{{ $datas->ieh2 }}</td>
                                        <td>{{ $datas->iel2 }}</td>
                                        <td>{{ $datas->iem3 }}</td>
                                        <td>{{ $datas->ieh3 }}</td>
                                        <td>{{ $datas->iel3 }}</td>
                                        <td>{{ $datas->iem4 }}</td>
                                        <td>{{ $datas->ieh4 }}</td>
                                        <td>{{ $datas->iel4 }}</td>

                                        <td>{{ $datas->iesmest1 }}</td>
                                        <td>{{ $datas->ieshest1 }}</td>
                                        <td>{{ $datas->ieslest1 }}</td>
                                        <td>{{ $datas->iesmest2 }}</td>
                                        <td>{{ $datas->ieshest2 }}</td>
                                        <td>{{ $datas->ieslest2 }}</td>
                                        <td>{{ $datas->iesmest3 }}</td>
                                        <td>{{ $datas->ieshest3 }}</td>
                                        <td>{{ $datas->ieslest3 }}</td>
                                        <td>{{ $datas->iesmest4 }}</td>
                                        <td>{{ $datas->ieshest4 }}</td>
                                        <td>{{ $datas->ieslest4 }}</td>
                                        <td>{{ $datas->iesmest5 }}</td>
                                        <td>{{ $datas->ieshest5 }}</td>
                                        <td>{{ $datas->ieslest5 }}</td>
                                        <td>{{ $datas->iesmest6 }}</td>
                                        <td>{{ $datas->ieshest6 }}</td>
                                        <td>{{ $datas->ieslest6 }}</td>
                                        <td>{{ $datas->iesmest7 }}</td>
                                        <td>{{ $datas->ieshest7 }}</td>
                                        <td>{{ $datas->ieslest7 }}</td>
                                        <td>{{ $datas->iesmest8 }}</td>
                                        <td>{{ $datas->ieshest8 }}</td>
                                        <td>{{ $datas->ieslest8 }}</td>
                                        <td>{{ $datas->iesmest9 }}</td>
                                        <td>{{ $datas->ieshest9 }}</td>
                                        <td>{{ $datas->ieslest9 }}</td>

                                        <td>{{ $datas->aesmest1 }}</td>
                                        <td>{{ $datas->aeshest1 }}</td>
                                        <td>{{ $datas->aeslest1 }}</td>
                                        <td>{{ $datas->aesmest2 }}</td>
                                        <td>{{ $datas->aeshest2 }}</td>
                                        <td>{{ $datas->aeslest2 }}</td>
                                        <td>{{ $datas->aesmest3 }}</td>
                                        <td>{{ $datas->aeshest3 }}</td>
                                        <td>{{ $datas->aeslest3 }}</td>
                                        <td>{{ $datas->aesmest4 }}</td>
                                        <td>{{ $datas->aeshest4 }}</td>
                                        <td>{{ $datas->aeslest4 }}</td>
                                        <td>{{ $datas->aesmest5 }}</td>
                                        <td>{{ $datas->aeshest5 }}</td>
                                        <td>{{ $datas->aeslest5 }}</td>
                                        <td>{{ $datas->aesmest6 }}</td>
                                        <td>{{ $datas->aeshest6 }}</td>
                                        <td>{{ $datas->aeslest6 }}</td>
                                        <td>{{ $datas->aesmest7 }}</td>
                                        <td>{{ $datas->aeshest7 }}</td>
                                        <td>{{ $datas->aeslest7 }}</td>
                                        <td>{{ $datas->aesmest8 }}</td>
                                        <td>{{ $datas->aeshest8 }}</td>
                                        <td>{{ $datas->aeslest8 }}</td>
                                        <td>{{ $datas->aesmest9 }}</td>
                                        <td>{{ $datas->aeshest9 }}</td>
                                        <td>{{ $datas->aeslest9 }}</td>

                                        <td>{{ $datas->naesmest1 }}</td>
                                        <td>{{ $datas->naeshest1 }}</td>
                                        <td>{{ $datas->naeslest1 }}</td>
                                        <td>{{ $datas->naesmest2 }}</td>
                                        <td>{{ $datas->naeshest2 }}</td>
                                        <td>{{ $datas->naeslest2 }}</td>
                                        <td>{{ $datas->naesmest3 }}</td>
                                        <td>{{ $datas->naeshest3 }}</td>
                                        <td>{{ $datas->naeslest3 }}</td>
                                        <td>{{ $datas->naesmest4 }}</td>
                                        <td>{{ $datas->naeshest4 }}</td>
                                        <td>{{ $datas->naeslest4 }}</td>
                                        <td>{{ $datas->naesmest5 }}</td>
                                        <td>{{ $datas->naeshest5 }}</td>
                                        <td>{{ $datas->naeslest5 }}</td>
                                        <td>{{ $datas->naesmest6 }}</td>
                                        <td>{{ $datas->naeshest6 }}</td>
                                        <td>{{ $datas->naeslest6 }}</td>
                                        <td>{{ $datas->naesmest7 }}</td>
                                        <td>{{ $datas->naeshest7 }}</td>
                                        <td>{{ $datas->naeslest7 }}</td>
                                        <td>{{ $datas->naesmest8 }}</td>
                                        <td>{{ $datas->naeshest8 }}</td>
                                        <td>{{ $datas->naeslest8 }}</td>
                                        <td>{{ $datas->naesmest9 }}</td>
                                        <td>{{ $datas->naeshest9 }}</td>
                                        <td>{{ $datas->naeslest9 }}</td>

                                        <td>{{ $datas->gv1m }}</td>
                                        <td>{{ $datas->gv1h }}</td>
                                        <td>{{ $datas->gv1l }}</td>
                                        <td>{{ $datas->gv2m }}</td>
                                        <td>{{ $datas->gv2h }}</td>
                                        <td>{{ $datas->gv2l }}</td>
                                        <td>{{ $datas->gv3m }}</td>
                                        <td>{{ $datas->gv3h }}</td>
                                        <td>{{ $datas->gv3l }}</td>
                                        <td>{{ $datas->gv4m }}</td>
                                        <td>{{ $datas->gv4h }}</td>
                                        <td>{{ $datas->gv4l }}</td>
                                        <td>{{ $datas->gv5m }}</td>
                                        <td>{{ $datas->gv5h }}</td>
                                        <td>{{ $datas->gv5l }}</td>
                                        <td>{{ $datas->gv6m }}</td>
                                        <td>{{ $datas->gv6h }}</td>
                                        <td>{{ $datas->gv6l }}</td>
                                        <td>{{ $datas->gv7m }}</td>
                                        <td>{{ $datas->gv7h }}</td>
                                        <td>{{ $datas->gv7l }}</td>
                                        <td>{{ $datas->gv8m }}</td>
                                        <td>{{ $datas->gv8h }}</td>
                                        <td>{{ $datas->gv8l }}</td>
                                        <td>{{ $datas->gv9m }}</td>
                                        <td>{{ $datas->gv9h }}</td>
                                        <td>{{ $datas->gv9l }}</td>
                                        <td>{{ $datas->gv11m }}</td>
                                        <td>{{ $datas->gv11h }}</td>
                                        <td>{{ $datas->gv11l }}</td>
                                        <td>{{ $datas->gv12m }}</td>
                                        <td>{{ $datas->gv12h }}</td>
                                        <td>{{ $datas->gv12l }}</td>
                                        <td>{{ $datas->gv13m }}</td>
                                        <td>{{ $datas->gv13h }}</td>
                                        <td>{{ $datas->gv13l }}</td>
                                        <td>{{ $datas->gv15m }}</td>
                                        <td>{{ $datas->gv15h }}</td>
                                        <td>{{ $datas->gv15l }}</td>
                                        <td>{{ $datas->gv16m }}</td>
                                        <td>{{ $datas->gv16h }}</td>
                                        <td>{{ $datas->gv16l }}</td>
                                        <td>{{ $datas->gv17m }}</td>
                                        <td>{{ $datas->gv17h }}</td>
                                        <td>{{ $datas->gv17l }}</td>
                                        <td>{{ $datas->gv18m }}</td>
                                        <td>{{ $datas->gv18h }}</td>
                                        <td>{{ $datas->gv18l }}</td>
                                        <td>{{ $datas->gv19m }}</td>
                                        <td>{{ $datas->gv19h }}</td>
                                        <td>{{ $datas->gv19l }}</td>
                                        <td>{{ $datas->gv20m }}</td>
                                        <td>{{ $datas->gv20h }}</td>
                                        <td>{{ $datas->gv20l }}</td>
                                        <td>{{ $datas->gv21m }}</td>
                                        <td>{{ $datas->gv21h }}</td>
                                        <td>{{ $datas->gv21l }}</td>
                                        <td>{{ $datas->gv22m }}</td>
                                        <td>{{ $datas->gv22h }}</td>
                                        <td>{{ $datas->gv22l }}</td>
                                        {{-- FIN RURBO ESTATAL --}}

                                        <td>
                                            <div style="width:900px; word-wrap: break-word" align="justify">
                                                {{ $datas->tnota }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width: 900px; word-wrap: break-word" align="justify">
                                                {{ $datas->observaciones_enlaces }}
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="unidad_busqueda" id="unidad_busqueda" value="{{ $unidades_busqueda }}">
            </form>
        @else
            <h2 class="mt-5"><b>NO SE ENCONTRARON REGISTROS</b></h2><br>

        @endif
        <br>
    </div>
    <br>
    <!--MODAL-->
    <!-- ESTO MOSTRARÁ EL SPINNER -->
    <div hidden id="spinner"></div>
    <!--MODAL ENDS-->
    <!--MODAL FORMULARIO-->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-info" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enviar_cursos_dta"><b>ADJUNTAR Y ENVIAR A PLANEACIÓN </b></h5>
                </div>
                <form id="formSendPlaneacion" enctype="multipart/form-data" method="POST"
                    action="{{ route('formatot.send.planeacion') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="file" name="cargar_memorandum_to_planeacion"
                                    id="cargar_memorandum_to_planeacion" class="form-control">
                            </div>
                        </div>
                        <input type="hidden" name="checkedCursos" id="checkedCursos" value="">
                        <input type="hidden" name="numeroMemo" id="numeroMemo" value="">
                        <div class="field_wrapper_direccion_dta"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="send_to_planeacion">ENVIAR</button>
                        <button type="button" id="close_btn_modal_send_dta" class="btn btn-danger">CERRAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--MODAL FORMULARIO ENDS-->
<!-- Modal Resumen a Unidad-->
<div class="modal fade" id="resumenUnidadModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resumen de Formato T para Unidad</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center">
                <div style="text-align:center" class="form-group">
                    <form method="POST" action="{{ route('resumen.unidad.formatot') }}" id="resumen_formatot_pdf">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-2"></div>
                            <div class="form-group col-md-8">
                                <label for="memo_reporte_unidad" class="form">Numero de Memorandum</label>
                                <input type="text" class="form-control" name="memo_reporte_unidad" id="memo_reporte_unidad" required>
                            </div>
                            <div class="form-group col-md-2"></div>
                        </div>
                        <input id="mes_reporte" name="mes_reporte" hidden>
                        <input id="unidad_reporte" name="unidad_reporte" hidden>
                        <button style="text-align: left; font-size: 10px; mbackground-color: #12322B;" type="button" class="btn btn" data-dismiss="modal">Cancelar</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" >Generar PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Subir Resumen a Unidad-->
<div class="modal fade" id="subirResumenUnidadModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center">
                <div style="text-align:center" class="form-group">
                    <p>¿Esta Seguro de subir el Documento?</p>
                    <form method="POST" action="{{ route('subir.resumen.unidad.formatot') }}" id="resumen_formatot_pdf" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-2"></div>
                            <div class="form-group col-md-8">
                                <label for="subir_memo_reporte_unidad" class="form">Numero de Memorandum</label>
                                <input type="file" accept="application/pdf" class="form-control" name="subir_memo_reporte_unidad" id="subir_memo_reporte_unidad" required>
                            </div>
                            <div class="form-group col-md-2"></div>
                        </div>
                        <input id="subir_mes_reporte" name="mes_reporte" hidden>
                        <input id="subir_unidad_reporte" name="unidad_reporte" hidden>
                        <button style="text-align: left; font-size: 10px; mbackground-color: #12322B;" type="button" class="btn btn" data-dismiss="modal">Cancelar</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" >Subir PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END -->
@endsection
@section('script_content_js')
    <script src="{{ asset('js/scripts/datepicker-es.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            }, 'El TAMAÑO DEL ARCHIVO DEBE SER MENOR A {0} bytes.');

            document.querySelector('#spinner').setAttribute('hidden', '');

            $('#enviardta').click(function() {
                $("#exampleModalCenter").modal("show");
            });

            $('#close_btn_modal_send_dta').click(function() {
                $("#numero_memo").rules('remove', 'required', 'extension', 'filesize');
                $("input[id*=numero_memo]").removeClass("error"); // workaround
                $("#exampleModalCenter").modal("hide");
            });

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

            /*
             * modificaciones de datos en filtro
             */
            $("#filterClaveCurso").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#table-instructor tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            // evento de la funcion click
            $('#send_to_planeacion').click(function() {
                $('#formSendPlaneacion').validate({
                    rules: {
                        "cargar_memorandum_to_planeacion": {
                            required: true,
                            extension: "pdf",
                            filesize: 2000000
                        }
                    },
                    messages: {
                        "cargar_memorandum_to_planeacion": {
                            required: "ARCHIVO REQUERIDO",
                            accept: "SÓLO SE ACEPTAN DOCUMENTOS PDF"
                        }
                    }
                });
            });
            // abrir el modal
            $('#btnEnviarPlaneacion').click(function() {
                var checkedCursos = new Array();
                // var comentarios_direcciondta = new Array();
                var wrapperDireccionDTA = $('.field_wrapper_direccion_dta');
                var numero_memo = $('#num_memo_devolucion').val();
                $('input[name="chkcursos[]"]:checked').each(function() {
                    checkedCursos.push(this.value);
                });
                $('textarea[name="comentarios_direccion_dta[]"]').each(function() {
                    if (!$(this).prop('disabled')) {
                        var fieldHTML =
                            '<input type="hidden" name="comentarios_direccionDta[]" id="comentarios_direccionDta" value="' +
                            this.value + '">';
                        $(wrapperDireccionDTA).append(fieldHTML); // Add field html
                        // comentarios_direcciondta.push(this.value);
                    }
                });
                // $('.modal-body #comentarios_direccionDta').val(comentarios_direcciondta);
                $('.modal-body #numeroMemo').val(numero_memo);
                $('.modal-body #checkedCursos').val(checkedCursos);
                $("#exampleModalCenter").modal("show");
            });

            $('#resumenUnidadModal').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget);
                var id = button.data('id');
                // console.log(id['1'])
                document.getElementById('mes_reporte').value = id['0'];
                document.getElementById('unidad_reporte').value = id['1'];
            });

            $('#subirResumenUnidadModal').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget);
                var id = button.data('id');
                // console.log(id['1'])
                document.getElementById('subir_mes_reporte').value = id['0'];
                document.getElementById('subir_unidad_reporte').value = id['1'];
            });
        });

    </script>
@endsection
