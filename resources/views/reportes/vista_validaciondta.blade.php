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

@section('content')

    <div class="container-fluid px-5 g-pt-30">
        {{-- información sobre la entrega del formato t para unidades --}}
        @switch($diasParaEntrega)
            @case(1)
                <div class="alert alert-info " role="alert">
                    <b>LA FECHA LÍMITE DEL PERÍODO DE {{ $mesInformar }} PARA EL ENVÍO DEL FORMATO T DE LAS UNIDADES
                        CORRESPONDIENTES ES EL <strong>{{ $fechaEntregaFormatoT }}</strong>; </b>
                </div>
            @break
            @case(0)
                <div class="alert alert-danger " role="alert">
                    <b>LA FECHA LÍMITE DEL PERÍODO DE {{ $mesInformar }} PARA EL ENVÍO DEL FORMATO T DE LAS UNIDADES
                        CORRESPONDIENTES ES EL <strong>{{ $fechaEntregaFormatoT }}</strong>; </b>
                </div>
            @break
            @default

        @endswitch

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
                    <h2>VALIDACIÓN DE CURSOS PARA DIRECCIÓN TÉCNICA ACADÉMICA <strong>(ENLACES)</strong> </h2>

                    {!! Form::open(['route' => 'validacion.cursos.enviados.dta', 'method' => 'GET', 'class' => 'form-inline']) !!}
                        <select name="busqueda_unidad" class="form-control mr-sm-2" id="busqueda_unidad">
                            <option value="all">TODAS LAS UNIDADES</option>
                            @foreach ($unidades as $itemUnidades)
                                <option {{$itemUnidades->unidad == $unidad ? 'selected' : ''}} value="{{ $itemUnidades->unidad }}">{{ $itemUnidades->unidad }}</option>
                            @endforeach
                        </select>

                        <select name="mesSearchE" id="mesSearchE" class="form-control mr-sm-2">
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
                    {!! Form::close() !!}
                </div>

                <div class="pull-right">

                </div>
            </div>
        </div>
        
        <hr style="border-color:dimgray">
        @if (count($cursos_validar) > 0)
            <form action="{{ route('reportes.formatot.enlaces.unidad.xls') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group mb-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i>&nbsp;
                            FORMATO T DE LA UNIDAD DE {{ $unidad }}
                        </button>
                    </div>
                    <input type="hidden" name="unidad_" id="unidad_" value="{{ $unidad }}">
                    <input type="hidden" name="mes_" id="mes_" value="{{ $mesSearch }}">
                </div>
            </form>


            <form id="formSendDtaTo" method="POST" action="{{ route('enviar.cursos.validacion.dta') }}" target="_self">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4 mb-3">
                        <input type="text" class="form-control mr-sm-1" name="num_memo_devolucion" id="num_memo_devolucion"
                            value="" placeholder="NÚMERO DE MEMORANDUM PARA REGRESO A UNIDAD">
                    </div>
                    <div class="form-group col-md-4 mb-2">
                        <input type="text" name="filterClaveCurso" id="filterClaveCurso" class="form-control"
                            placeholder="BUSQUEDA POR CLAVE DE CURSO">
                    </div>
                    <div class="form-group col-md-4 mb-2">
                        @if ($memorandum != null)
                            <a href="{{ $memorandum->memorandum }}" target="_blank"
                                class="btn btn-info btn-circle m-1 btn-circle-sm"
                                title="DESCARGAR MEMORANDUM N° {{ $memorandum->num_memo }}">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;
                                MEMORANDUM {{ $memorandum->num_memo }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="form-row">

                    @can('envio.revision.dta')
                        <div class="form-group mb-2">
                            <button input type="button" id="validacionDireccionDta" name="validacionDireccionDta"
                                value="EnviarJefaDta" class="btn btn-info">
                                <i class="fa fa-paper-plane-o fa-2x" aria-hidden="true"></i>&nbsp;
                                ENVIAR A VALIDACIÓN DIRECCIÓN DE DTA
                            </button>
                        </div>
                    @endcan

                    @can('envio.revision.dta')
                        {{-- @if ($regresar_unidad->count() > 0) --}}
                        <div class="form-group mb-2">
                            <button input type="submit" id="validarEnDta" name="validarEnDta" value="GenerarMemorandum"
                                class="btn btn-danger">
                                <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>&nbsp;
                                GENERAR MEMORANDUM DE DEVOLUCIÓN
                            </button>
                        </div>
                        {{-- @endif --}}
                    @endcan
                    {{-- cambios en la vista de validaciondta --}}
                    @can('envio.revision.dta')
                        {{-- @if ($regresar_unidad->count() > 0) --}}
                        <div class="form-group mb-2">
                            <button input type="button" id="enviardta" name="enviardta" value="RegresarUnidad"
                                class="btn btn-warning">
                                <i class="fa fa-upload fa-2x" aria-hidden="true"></i>&nbsp;
                                ENVIAR A UNIDAD
                            </button>
                        </div>
                        {{-- @endif --}}
                    @endcan
                </div>

                <div class="form-row">
                    <div class="table-responsive container-fluid mt-5">
                        <table id="table-instructor" class="table" style='width: 100%; margin-left: -1.1em;'>
                            <caption>CURSOS VALIDADOS ENVIADOS A DIRECCIÓN TÉCNICA ACADÉMICA</caption>
                            <thead class="thead-dark">
                                <tr align="center">
                                    <th scope="col">N°</th>
                                    <th scope="col">SELECCIONAR &nbsp;
                                        <input type="checkbox" id="selectAll" />
                                    </th>
                                    <th scope="col" style="width:100%">COMENTARIOS</th>
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
                                    <th scope="col">EXO TOTAL MUJER</th>
                                    <th scope="col">EXO TOTAL HOMBRE</th>
                                    <th scope="col">EXO PARCIAL MUJER</th>
                                    <th scope="col">EXO PARCIAL HOMBRE</th>
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
                                    <th scope="col">DEPENDENCIA BENEFICIADA</th>
                                    <th scope="col">CONVENIO GENERAL</th>
                                    <th scope="col">CONV SEC PUB O PRIV</th>
                                    <th scope="col">VALIDACION PAQUETERIA</th>
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
                                    <th scope="col" style="width:50%">OBSERVACIONES</th>
                                    <th scope="col" style="width: 50%">OBSERVACIONES UNIDAD</th>
                                </tr>
                            </thead>
                            <tbody style="height: 300px; overflow-y: auto">
                                @foreach ($cursos_validar as $key => $datas)
                                    <tr align="center">
                                        <td>{{ $key + 1 }}</td>
                                        <td><input type="checkbox" id="cbk_{{ $datas->id_tbl_cursos }}" class="checkbx"
                                                name="chkcursos[]" value="{{ $datas->id_tbl_cursos }}"
                                                {{ $datas->estadocurso == 'RETORNO_UNIDAD' ? 'disabled' : '' }}
                                                {{ $datas->turnados_enlaces == 'MEMO_TURNADO_RETORNO' ? 'checked' : '' }} />
                                        </td>
                                        <td>
                                            {{-- word-wrap: break-word align="justify" --}}
                                            <div style="width: 300px;" align="justify">
                                                {{-- <textarea id="comentario_{{ $datas->id_tbl_cursos }}" name="comentarios_enlaces[]" cols="45" class="form-control" rows="3">{{ json_decode($datas->comentario_enlaces_retorno, JSON_UNESCAPED_SLASHES) }}</textarea> --}}
                                                @if ($datas->turnados_enlaces == 'MEMO_TURNADO_RETORNO')
                                                    <textarea name="comentarios_enlaces[]"
                                                        id="comentario_{{ $datas->id_tbl_cursos }}" cols="45"
                                                        rows="3">{{ json_decode($datas->comentario_enlaces_retorno, JSON_UNESCAPED_SLASHES) }}</textarea>
                                                @else
                                                    <textarea name="comentarios_enlaces[]"
                                                        id="comentario_{{ $datas->id_tbl_cursos }}" cols="45" rows="3"
                                                        disabled></textarea>
                                                @endif
                                            </div>
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
                                        <td>
                                            <div style="width:300px; word-wrap: break-word">{{ $datas->depen }}</div>
                                        </td>
                                        <td>{{ $datas->cgeneral }}</td>
                                        <td>{{ $datas->sector }}</td>
                                        <td>{{ $datas->mpaqueteria }}</td>
                                        <td>{{ $datas->iem1 }}</td>
                                        <td>{{ $datas->ieh1 }}</td>
                                        <td>{{ $datas->iem2 }}</td>
                                        <td>{{ $datas->ieh2 }}</td>
                                        <td>{{ $datas->iem3 }}</td>
                                        <td>{{ $datas->ieh3 }}</td>
                                        <td>{{ $datas->iem4 }}</td>
                                        <td>{{ $datas->ieh4 }}</td>
                                        <td>{{ $datas->iem5 }}</td>
                                        <td>{{ $datas->ieh5 }}</td>
                                        <td>{{ $datas->iem6 }}</td>
                                        <td>{{ $datas->ieh6 }}</td>
                                        <td>{{ $datas->iem7 }}</td>
                                        <td>{{ $datas->ieh7 }}</td>
                                        <td>{{ $datas->iem8 }}</td>
                                        <td>{{ $datas->ieh8 }}</td>
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
                                            <div style="width:900px; word-wrap: break-word" align="justify">
                                                {{ $datas->tnota }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width: 300px; word-wrap: break-word" align="justify">
                                                {{ json_decode($datas->observaciones_unidad, JSON_UNESCAPED_SLASHES) }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="num_memo" id="num_memo" value="{{ $memorandum != null ? $memorandum->num_memo : '' }}">
                    <input type="hidden" name="unidadActual" id="unidadActual" value="{{ $unidad }}">
                </div>
            </form>
        @else
            <h2><b>NO  SE ENCONTRARON REGISTROS</b></h2>
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
                    <h5 class="modal-title" id="enviar_cursos_dta"><b>ADJUNTAR Y REGRESAR A UNIDAD </b></h5>
                </div>
                <form id="formSendUnity" enctype="multipart/form-data" method="POST"
                    action="{{ route('dta.send.unity') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="file" name="memorandum_regreso_unidad" id="memorandum_regreso_unidad"
                                    class="form-control">
                            </div>
                        </div>
                        <input type="hidden" name="check_cursos_dta" id="check_cursos_dta" value="">
                        <input type="hidden" name="numero_memo_devolucion" id="numero_memo_devolucion" value="">
                        <div class="field_wrapper_enlace_dta"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="send_to_dta">ENVIAR</button>
                        <button type="button" id="close_btn_modal_send_dta" class="btn btn-danger">CERRAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--MODAL FORMULARIO ENDS-->
@endsection

@section('script_content_js')
    <script src="{{ asset('js/scripts/datepicker-es.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            document.querySelector('#spinner').setAttribute('hidden', '');
            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            }, 'El TAMAÑO DEL ARCHIVO DEBE SER MENOR A {0} bytes.');
            
            $('#enviardta').click(function() {
                var cursosChecked = new Array();
                // var comentario_retorno = new Array();
                var wrapperEnlaceDta = $('.field_wrapper_enlace_dta');
                var numMemo = $('#num_memo_devolucion').val();
                $('input[name="chkcursos[]"]:checked').each(function() {
                    cursosChecked.push(this.value);
                });
                // se cargan los textarea en el arreglo
                $('textarea[name="comentarios_enlaces[]"]').each(function() {
                    if (!$(this).prop('disabled')) {
                        var fieldHtml =
                            '<input type="hidden" name="comentarios_enlaces[]" id="comentarios_enlaces" value="' +
                            this.value + '">';
                        $(wrapperEnlaceDta).append(fieldHtml); // Add field html
                        // comentario_retorno.push(this.value);   
                    }
                });
                $('.modal-body #numero_memo_devolucion').val(numMemo);
                $('.modal-body #check_cursos_dta').val(cursosChecked);
                // $('.modal-body #comentarios_enlaces').val(comentario_retorno);
                $("#exampleModalCenter").modal("show");
            });

            /*
            * CERRAMOS EL MODAL
            */
            $('#close_btn_modal_send_dta').click(function() {
                $("#numero_memo").rules('remove', 'required', 'extension', 'filesize');
                $("input[id*=numero_memo]").removeClass("error"); // workaround
                $("#exampleModalCenter").modal("hide");
                // quitamos lo que hay en el contenido del wrapper
                $('.field_wrapper_enlace_dta').empty();
            });

            $("#selectAll").click(function() {
                $("input[type=checkbox]").not(this).prop("checked", $(this).prop("checked"));
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
                    $('#comentario_' + id).val('');
                }
            });

            // VALIDACIONES
            $('#formSendDtaTo').validate({
                rules: {
                    num_memo_devolucion: {
                        required: true
                    },
                },
                messages: {
                    num_memo_devolucion: {
                        required: "CAMPO REQUERIDO"
                    },
                }
            });

            $('#send_to_dta').click(function() {
                $('#formSendUnity').validate({
                    rules: {
                        "cargar_archivo_formato_t": {
                            required: true,
                            extension: "pdf",
                            filesize: 2000000
                        }
                    },
                    messages: {
                        "cargar_archivo_formato_t": {
                            required: "ARCHIVO REQUERIDO",
                            accept: "SÓLO SE ACEPTAN DOCUMENTOS PDF"
                        }
                    }
                }); // configurar el validador
            });
            /**
            * Abrir el modal
            **/
            /*
            * modificaciones de datos en filtro
            */
            $("#filterClaveCurso").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#table-instructor tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            /*
            * click para iniciar el envío del formulario y deshabilitar el validador del número de memo
            */
            $('#validacionDireccionDta').click(function() {
                // deshabilitar el elemento que tiene el atributo
                $('[name="num_memo_devolucion"]').rules('remove', 'required');
                // envíamos el formuario
                // $('#formSendDtaTo').submit(function(eventObj) {
                //     $(this).append('<input type="hidden" name="validarEnDta" value="EnviarJefaDta" /> ');
                //     return true;
                // });
                var form = document.getElementById('formSendDtaTo'); //obtienes el formulario del elemento
                var input = document.createElement(
                    'input'); //preparar un nuevo elemento de entrada en el doom
                input.setAttribute('name', 'envioDireccionDta'); //se asigna el nombre
                input.setAttribute('value', 1); // asignación de la variable
                input.setAttribute('type', 'hidden') //set the type, like "hidden" or other
                form.appendChild(input); //append the input to the form
                form.submit(); //send with added input
            });

        });

    </script>
@endsection
