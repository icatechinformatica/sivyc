<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Cursos | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        .nav-link {padding:0; margin:0px; width:auto;}
        #table-instructor thead th {padding:3px; margin:0px; width:auto; text-align:center; vertical-align: middle;}
        #table-instructor  tbody td {padding:3px; margin:0px; width:auto; vertical-align: middle; text-align:center;}
    </style>

@endsection
<!--seccion-->
@section('content')
    <div class="card-header">
        <h3>Cursos</h3>
    </div>
    <div class="card card-body">
        <div class="row">
            <div class="form-group col-md-6 margin-tb">
                {!! html()->form('GET', route('curso-inicio'))->class('form-inline')->open() !!}
                    <select name="tipo_curso" class="form-control mr-sm-2" id="tipo_curso">
                        <option value="">BUSCAR POR</option>
                        <option value="especialidad">ESPECIALIDAD</option>
                        <option value="curso">CURSO</option>
                        <option value="duracion">DURACIÓN</option>
                        <option value="modalidad">MODALIDAD</option>
                        <option value="clasificacion">CLASIFICACIÓN</option>
                        <option value="anio">AÑO</option>
                    </select>
                    {!! html()->text('busquedaPorCurso')
                        ->class('form-control mr-sm-2')
                        ->placeholder('BUSCAR')
                        ->attribute('aria-label', 'BUSCAR')
                        ->value("") !!}
                    {!! html()->button('BUSCAR')->id('buscar')->class('btn btn-primary rounded')->type('submit') !!}
                {!! html()->form()->close() !!}
            </div>
            <div class="form-group col-md-3">
                <div class="dropdown show">
                    <a class="btn btn-warning dropdown-toggle text-dark" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        EXPORTAR CATÁLOGO
                    </a>
                    <div class="dropdown-menu bg-warning" aria-labelledby="dropdownMenuLink">
                        @can('exportar.cursosdv')
                            <a class="dropdown-item border-bottom text-dark"  href="{{route('academico.exportar.cursos',['xls'=>'CURSOS'])}}" target="_blank">
                                <i  class="far fa-file-excel fa-1x fa-sm"></i> CURSOS
                            </a>
                            <a class="dropdown-item border-bottom text-dark"  href="{{route('academico.exportar.cursos',['xls'=>'CERTIFICACION'])}}" target="_blank">
                                <i  class="far fa-file-excel fa-1x fa-sm"></i> CERTIFICIÓN EXTRAORDINARIA
                            </a>
                            <a class="dropdown-item border-bottom  text-dark"  href="{{route('academico.exportar.cursos',['xls'=>'PROGRAMA'])}}" target="_blank">
                                <i  class="far fa-file-excel fa-1x fa-sm"></i> PROGRAMA ESTRATÉGICO
                            </a>
                        @endcan
                        @can('academico.catalogo.cursos')
                            <a class="dropdown-item border-bottom text-dark"  href="{{route('academico.exportar.cursos',['xls'=>'ACTIVOS'])}}" target="_blank">
                                <i  class="far fa-file-excel fa-1x fa-sm"></i> CURSOS ACTIVOS
                            </a>
                        @endcan
                        @can('academico.catalogo.cursosall')
                            <a class="dropdown-item text-dark"  href="{{route('academico.exportar.cursosall')}}" target="_blank">
                                <i  class="far fa-file-excel fa-1x fa-sm"></i> TODO LOS CURSOS
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            @can('cursos.create')
                <div class="form-group col-md-3">
                    <a class="btn btn-primary rounded" href="{{route('curso-crear_new')}}">NUEVO CURSO</a>
                </div>
            @endcan
        </div>

        {{-- Tabla de la lista de cursos --}}
         <table  id="table-instructor" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ESPECIALIDAD</th>
                    <th>CURSO</th>
                    <th >CAPACITACIÓN</th>
                    <th >DURA CiÓN</th>
                    <th >MODA LIDAD</th>
                    <th >CLASIFI CACIÓN</th>
                    <th >COSTO</th>
                    <th >ESTATUS</th>
                    <th >CURSO /CERTIFICACIÓN</th>
                    <th >PROG. ESTRA.</th>
                    <th >ACTUALIZADO</th>
                    @can('cursos.show')
                        <th >&nbsp;EDIT&nbsp;</th>
                    @endcan
                    <th >&nbsp;VER&nbsp;</th>
                    @can('paqueteriasdidacticas')
                    <th >PAQUE TERÍA</th>
                    @endcan
                    <th >CARTA DESCRI</th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <th scope="row">GESTION Y VENTA DE SERVICIOS TURISTICOS</th>
                        <td class="text-left pl-2"> INTRODUCCIÓN A LA HISTORIA DEL ARTE</td>
                        <td>PRESENCIAL Y A DISTANCIA</td>
                        <td>30</td>
                        <td>CAE Y EXT</td>
                        <td>B-INTERMEDIO</td>
                        <td>500.00</td>
                        <td>ACTIVO</td>
                        <td>CURSO</td>
                        <td>NO</td>
                        <td>JOSE FRANCISCO LICONA SANGEADO 29/07/2024 12:07</td>
                        @can('cursos.show')
                        <td>
                            <a class="nav-link" alt="Editar Registro" href="">
                                <i  class="fa fa-edit  fa-2x fa-lg text-success"></i>
                            </a>
                        </td>
                        @endcan
                        <td>
                            <a class="nav-link" alt="Ver Registro" href="" data-toggle="modal" data-placement="top" data-target="#fullHeightModalRight"
                                data-id="">
                                <i  class="fa fa-search  fa-2x fa-lg text-primary"></i>
                            </a>
                        </td>
                        {{-- <td>
                            @if($itemData->file_carta_descriptiva)

                                <a class="nav-link"  alt="Descargar PDF" href="{{env('APP_URL').'/'.'storage'.$itemData->file_carta_descriptiva}}" target="_blank">
                                    <i  class="fa fa-file-pdf  fa-2x fa-lg text-danger"></i>
                                </a>

                            @endif
                        </td> --}}
                        @can('paqueteriasdidacticas')
                        <td>
                            <a href="" class="nav-link" title="Paquetes">
                            <i class="fa fa-2x fa-folder text-muted"></i></a>
                        </td>
                        @endcan

                        <td>
                            <a class="" href="" target="_blank">
                                <i  class="fa fa-file-pdf  fa-2x fa-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="14">
                        {{-- {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }} --}}
                    </td>
                </tr>
            </tfoot>
        </table>
        <br>

        {{-- Modal para ver los datos del curso --}}
        <!-- Full Height Modal Right -->
        <div class="modal fade right" id="fullHeightModalRight" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-full-height modal-right" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title w-100" id="myModalLabel"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="contextoModalBody"></div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Full Height Modal Right -->


    </div>
@endsection
@section('script_content_js')
    <script>

    </script>

@endsection
