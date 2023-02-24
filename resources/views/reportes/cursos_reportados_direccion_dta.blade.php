<!--Creado por Julio Alcaraz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'APERTURAS | SIVyC Icatech')
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
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        .content {
            padding: 0 20px 20px 17px;
            margin-top: 0;
        }
        @media (min-width: 1200px) {
            .container{
                width: 1400px;
            }
        }
    </style>
@endsection
<!--seccion-->
@section('content')
    <div class="container-fluid px-5 g-pt-30">
        <div class="alert"></div>
        @if($errors->any())
            <div class="alert alert-danger">
                {{$errors->first()}}
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        
        <div class="row">
            <div class="col-lg-8 margin-tb">
                <div>
                    <h3><b>CURSOS REPORTADOS DE MESES ANTERIORES PARA LAS UNIDADES</b></h3>
                </div>
            </div>
        </div>

        {{ Form::open(['route' => 'cursos.reportados.historico.direccion.dta.index', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
            <div class="form-row">
                <div class="form-group col-md-3">
                    <select name="unidadseleccionado" id="unidadseleccionado" class="form-control">
                        <option value="">--SELECCIONAR UNIDAD--</option>
                        @foreach ($unidades_indice as $indexUnidad)
                            <option value="{{ $indexUnidad->ubicacion }}">{{ $indexUnidad->ubicacion }}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <select name="messeleccionado" id="messeleccionado" class="form-control">
                        <option value="">--SELECCIONAR MES--</option>
                        @foreach ($meses as $mun => $month)
                            <option value="{{ $month }}">{{ $month }}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="form-group col-md-3">
                    {{ Form::text('anio', null , ['class' => 'form-control  mr-sm-1', 'placeholder' => 'AÑO A FILTRAR']) }}
                </div>
                <div class="form-group col-md-3">
                    {!! Form::submit( 'FILTRAR', ['id'=>'formatot', 'class' => 'btn btn-outline-info my-2 my-sm-0 waves-effect waves-light', 'name' => 'submitbutton'])!!}
                </div>
                
            </div>
        {!! Form::close() !!}
            
        <hr style="border-color:dimgray">
        @if (count($cursosReporados ) > 0)
            <form id="dtaformGetDocument" method="POST" action="{{ route('formatot.send.dta') }}" target="_blank">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-8 mb-2">
                        <input type="text" name="filterClaveCurso" id="filterClaveCurso" class="form-control" placeholder="BUSQUEDA POR CLAVE DE CURSO">
                    </div> 
                </div>
                <div class="form-row">
                    <div class="table-responsive container-fluid mt-2">
                        <div class="col-sm-12">
                            <table  id="table-instructor" class="table" style='width: 100%; margin-left: -1.8em;'>
                                <caption>CURSOS REPORTADOS POR LAS UNIDADES</caption>         
                                <thead class="thead-dark">
                                    <tr align="center">
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
                                        <th scope="col" WIDTH="500">OBSERVACIONES</th>                                
                                    </tr>
                                </thead>
                                <tbody style="height: 300px; overflow-y: auto">
                                    @foreach ($cursosReporados as $datas)
                                        <tr align="center" >
                                            <td>{{ $datas->mesturnado }}</td>
                                            <td>{{ $datas->unidad }}</td>
                                            <td>{{ $datas->plantel }}</td>
                                            <td>{{ $datas->espe }}</td>
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->curso }}</div></td>
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->clave }}</div></td>
                                            <td>{{ $datas->mod }}</td>
                                            <td>{{ $datas->dura }}</td>
                                            <td>{{ $datas->turno }}</td>
                                            <td>{{ $datas->diai }}</td>
                                            <td>{{ $datas->mesi }}</td>
                                            <td>{{ $datas->diat }}</td>
                                            <td>{{ $datas->mest }}</td>
                                            <td>{{ $datas->pfin }}</td>
                                            <td>{{ $datas->horas }}</td>
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->dia }}</div></td>
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->horario }}</div></td>
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
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->cespecifico }}</div></td>
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->mvalida }}</div></td>
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->efisico }}</div></td>
                                            <td><div style = "width:200px; word-wrap: break-word">{{ $datas->nombre }}</div></td>
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
                                            <td><div style = "width:300px; word-wrap: break-word">{{ $datas->depen }}</div></td>
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
                                            <td><div style = "width:800px; word-wrap: break-word">{{ $datas->tnota }}</div></td>           
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>   
                    </div>
                </div>
            </form>  
        @else
            <h2><b>NO HAY REGISTROS PARA MOSTRAR</b></h2>
        @endif
        <br>
    </div>
    <!--MODAL-->
    <!-- ESTO MOSTRARÁ EL SPINNER -->
    <div hidden id="spinner"></div>
    <!--MODAL ENDS-->
    
@endsection
@section('script_content_js')
<script src="{{ asset('js/scripts/datepicker-es.js') }}"></script>
<script type="text/javascript">
    $(function(){
        /*
        * modificaciones de datos en filtro
        */
        $("#filterClaveCurso").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#table-instructor tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
    });
</script>
@endsection