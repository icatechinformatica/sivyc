{{-- Desarrollado por MIS. Daniel Méndez Cruz --}}

<!--Navbar -->
<nav class="mb-1 navbar navbar-expand-lg navbar-dark pink darken-4">
    <a class="navbar-brand" href="#"><h4><b>Icatech</b></h4></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-555"
        aria-controls="navbarSupportedContent-555" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent-555">
        @guest
            <ul class="navbar-nav ml-auto nav-flex-icons">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Inicio de Sesión') }}</a>
                </li>
            </ul>
        @else
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link g-mx-5--lg" href="{{ route('cursos_validados.index') }}">
                        Cursos Validados
                    </a>
                </li>
                @can('supre.index')
                <li class="nav-item g-mx-5--lg">
                    <a class="nav-link g-color-white--hover" href="{{route('supre-inicio')}}">
                        Suficiencia Presupuestal
                    </a>
                </li>
                @endcan
                @can('contratos.index')
                <li class="nav-item g-mx-5--lg">
                    <a class="nav-link g-color-white--hover" href="{{route('contrato-inicio')}}">
                        Contrato
                    </a>
                </li>
                @endcan
                <!--helper-->
                @can('pagos.inicio')
                    <li class="nav-item g-mx-5--lg"><a class="nav-link g-color-white--hover" href="{{route('pago-inicio')}}">Pagos</a></li>
                @endcan
                <!--end helper-->
                <!--<li class="nav-item g-mx-5--lg">
                    <a class="nav-link g-color-white--hover" >
                        Agenda Vinculador
                    </a>
                </li>-->
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Catálogos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        @can('alumnos.inscritos.index')
                            <a class="dropdown-item" href="{{ route('alumnos.inscritos') }}">Alumnos</a>
                        @endcan
                        @can('cursos.index')
                             <a class="dropdown-item" href="{{route('curso-inicio')}}">Cursos</a>
                        @endcan
                        <a class="dropdown-item" href="{{route('instructor-inicio')}}">Instructor</a>

                        @can('convenios.index')
                            <a class="dropdown-item" href="{{route('convenios.index')}}">Convenios</a>
                        @endcan
                        @can('cerss.inicio')
                            <a class="dropdown-item" href="{{route('cerss.inicio')}}">CERSS</a>
                        @endcan
                        @can('areas.inicio')
                            <a class="dropdown-item" href="{{route('areas.inicio')}}">Áreas</a>
                        @endcan
                        @can('especialidades.inicio')
                            <a class="dropdown-item" href="{{route('especialidades.inicio')}}">Especialidades</a>
                        @endcan
                        @can('unidades.index')
                            <a class="dropdown-item" href="{{route('unidades.inicio')}}">Unidades</a>
                        @endcan
                        @can('exoneraciones.inicio')
                            <a class="dropdown-item" href="{{route('exoneraciones.inicio')}}">Exoneraciones</a>
                        @endcan
                        @can('instituto.inicio')
                            <a class="dropdown-item" href="{{route('instituto.inicio')}}">Acerca del instituto</a>
                        @endcan
                    </div>
                </li>
                @can('tablero.metas.index')
                    <li class="nav-item g-mx-5--lg">
                        <a class="nav-link g-color-white--hover" href="{{route('tablero.metas.index')}}">
                            Tablero de control
                        </a>
                    </li>
                @endcan
                <!--AGREGAR NUEVO ELEMENTO EN EL MENU-->
                @can('supervision.escolar')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Supervisiones
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('supervision.escolar')
                                <a class="dropdown-item" href="{{route('supervision.escolar')}}">Escolar</a>
                            @endcan
                        </div>
                    </li>
                @endcan
                <!--AGREGAR NUEVO ELEMENTO EN EL MENU END-->
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reportes
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        {{-- <a class="dropdown-item" href="https://datastudio.google.com/reporting/7c518e16-99ea-4cb2-8509-7064c0604e00" target="_blank">CURSOS VS OBJETIVOS</a>
                        <a class="dropdown-item" href="https://datastudio.google.com/reporting/512e11eb-babf-4476-8827-8d4243e2c219" target="_blank">STATUS PAGO INSTRUCTORES</a> --}}
                        {{-- <a class="dropdown-item" href="{{route('reportes.formatoT')}}">Reporte de Formato T</a>                         --}}
                        {{--@can('academicos.arc')
                            <a class="dropdown-item" href="{{route('reportes.vista_arc')}}">Solicitudes ARC01 y ARC02 </a>
                        @endcan--}}
                        @can('reportes.cursos')
                            <a class="dropdown-item" href="{{route('reportes.cursos.index')}}">Cursos Autorizados</a>
                        @endcan
                        @can('planeacion.reporte')
                            <a class="dropdown-item" href="{{route('planeacion.reporte')}}">Suficiencias Presupuestales</a>
                            <a class="dropdown-item" href="{{route('planeacion.reporte-cancelados')}}">Folios Cancelados</a>
                        @endcan
                        @can('financieros.reporte')
                            <a class="dropdown-item" href="{{route('financieros.reporte')}}">Estado de Contratos y Pagos</a>
                        @endcan
                        @can('vinculacion.reporte')
                            <a class="dropdown-item" href="{{route('cursosvinculador.reporte')}}">Alumno por Vinculador</a>
                        @endcan
                        @can('reportes.911')
                            <a class="dropdown-item" href="{{route('reportes.911.showForm')}}">Reporte 911</a>
                        @endcan
                        @can('reportes.rdcd08')
                            <a class="dropdown-item" href="{{route('reportes.rdcd08.index')}}">RDCD-08</a>
                        @endcan
                        @can('reportes.rcdod11')
                            <a class="dropdown-item" href="{{route('reportes.rcdod11.index')}}">RCDOD-11</a>
                        @endcan
                        @can('financieros.reportevalrec')
                        {{-- <a class="dropdown-item" data-toggle="modal" data-placement="top"
                                data-target="#ModalFinanciero">TRAMITES VALIDADOS Y RECEPCIONADOS</a>--}}
                            <a class="dropdown-item" href="{{route('docummentospago.reporte')}}">TRAMITES RECEPCIONADOS</a>
                        @endcan

                        {{-- <a class="dropdown-item" href="{{route('vista_formatot')}}">Formato T</a> --}}
                    </div>
                </li>

                @can('preinscripcion.grupo')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Preinscripci&oacute;n
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <!--alumnos.index-->
                            @can('alumnos.index')
                                <a class="dropdown-item" href="{{ route('alumnos.index') }}">Aspirantes</a>
                            @endcan
                            @can('alumnos.inscritos.index')
                                <a class="dropdown-item" href="{{ route('alumnos.inscritos') }}">Alumnos</a>
                            @endcan
                            <a class="dropdown-item" href="{{route('preinscripcion.grupo.nuevo')}}">Nuevo Grupo</a>
                            <a class="dropdown-item" href="{{route('preinscripcion.buscar')}}">Buscar Grupo</a>
                        </div>
                    </li>
                @endcan

                @can('solicitud.apertura')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitud
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{route('solicitud.apertura')}}">Clave de Apertura ARC01</a>
                            <a class="dropdown-item" href="{{route('solicitud.apertura.modificar')}}">Modificaci&oacute;n Apertura ARC02</a>
                            <a class="dropdown-item" href="{{route('solicitud.apertura.turnar')}}">Turnar Solicitud</a>
                        </div>
                    </li>
                @endcan

                {{-- Grupo calificaciones --}}
                @can('grupos.calificaciones')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Grupos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('grupos.calificaciones')
                                <a class="dropdown-item" href="{{route('grupos.calificaciones')}}">Registrar Calificaciones</a>
                            @endcan
                            @can('grupos.asignarfolios')
                                <a class="dropdown-item" href="{{route('grupos.asignarfolios')}}">Asignar Folios</a>
                            @endcan
                            @can('grupos.cancelacionfolios')
                                <a class="dropdown-item" href="{{route('grupos.cancelacionfolios')}}">Cancelar Folios</a>
                            @endcan
                            @can('grupos.consultas')
                            <a class="dropdown-item" href="{{route('grupos.consultas')}}">B&uacute;squeda</a>
                             @endcan
                        </div>
                    </li>
                @endcan
                {{-- grupo calificaciones end --}}

                @can('formatot.menu.indice')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a href="#" class="nav-link g-color-white--hover" id="navbarDropdownMenuLinkValidacion" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Formatos T
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkValidacion">
                            @can('vista.formatot.unidades.indice')
                                <a class="dropdown-item" href="{{route('vista_formatot')}}">Generación Formato T por Unidades</a>
                            @endcan
                            @can('vista.validacion.enlaces.dta')
                                <a class="dropdown-item" href="{{ route('validacion.cursos.enviados.dta') }}">Revisión de Cursos Formato T</a>
                            @endcan
                            @can('vista.validacion.direccion.dta')
                                <a class="dropdown-item" href="{{ route('validacion.dta.revision.cursos.indice') }}">Validación de Cursos Formato T DTA</a>
                            @endcan
                            @can('vista.revision.validacion.planeacion.indice')
                                <a class="dropdown-item" href="{{ route('planeacion.formatot.index') }}">Revisión y Validación Final Formato T</a>
                            @endcan
                            @can('vista.validacion.direccion.dta')
                                <a class="dropdown-item" href="{{ route('indice.dta.aperturado.indice') }}" >Formato T Aperturado</a>
                            @endcan
                            {{-- agregar nuevo elemento a menu --}}
                            @can('vista.validacion.direccion.dta')
                                <a href="{{ route('checar.memorandum.dta.mes') }}" class="dropdown-item">Memorandums Para la Dirección de Técnica Acádemica</a>
                            @endcan
                            @can('vista.validacion.enlaces.dta')
                                <a href="{{ route('checar.memorandum.dta.mes') }}" class="dropdown-item">Memorandums Enviados a DTA</a>
                            @endcan
                            {{-- agregar nuevo elemento a menu END --}}
                            {{--@can('vista.formatot.unidades.indice')
                                <a href="{{ route('cursos.reportados.historico.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endcan
                            @can('vista.validacion.enlaces.dta')
                                <a href="{{ route('cursos.reportados.historico.dta.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endcan
                            @can('vista.validacion.direccion.dta')
                                <a href="{{ route('cursos.reportados.historico.direccion.dta.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endcan
                            @can('vista.revision.validacion.planeacion.indice')
                                <a href="{{ route('cursos.reportados.historico.planeacion.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endcan--}}
                                <a class="dropdown-item" href="{{route('seguimento.avance.unidades.formatot.ejecutiva.index')}}">Seguimiento Ejecutivo a Unidades Para el Formato T</a>
                        </div>
                    </li>
                @endcan

                {{-- modificaciones en el curso del menu --}}
                {{-- consultas folios asignados --}}
                @can('solicitudes')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitudes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('solicitudes.aperturas')
                                <a class="dropdown-item" href="{{route('solicitudes.aperturas')}}">Aperturas ARC01 y ARC02</a>
                            @endcan
                            @can('solicitudes.folios')
                                <a class="dropdown-item" href="{{route('solicitudes.folios')}}">Lote de Folios</a>
                            @endcan
                            @can('solicitudes.cancelacionfolios')
                                <a class="dropdown-item" href="{{route('solicitudes.cancelacionfolios')}}">Cancelaci&oacute;n Folios</a>
                            @endcan
                        </div>
                    </li>
                @endcan

                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Consultas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        @can('consultas.folios')
                            <a class="dropdown-item" href="{{route('consultas.folios')}}">Folios Asignados</a>
                        @endcan
                        @can('consultas.lotes')
                            <a class="dropdown-item" href="{{route('consultas.lotes')}}">Actas de Folios</a>
                        @endcan
                        @can('consultas.cursosaperturados')
                            <a class="dropdown-item" href="{{route('consultas.cursosaperturados')}}">Cursos Aperturados</a>
                        @endcan
                        @can('consultas.instructor')
                            <a class="dropdown-item" href="{{route('consultas.instructor')}}">Instructores</a>
                        @endcan
                        <!--can('academico.catalogocursos')-->
                            <a class="dropdown-item" href="{{route('academico.exportar.cursos')}}">Exportar Cursos</a>
                        <!--endcan-->
                        <!--can('academico.catalogoinstructores')-->
                            <a class="dropdown-item" data-toggle="modal" data-placement="top"
                                data-target="#ModalExpIns">Exportar Instructores</a>
                        <!--endcan-->
                        @can('planeacion.estadisticas')
                            <a class="dropdown-item" href="{{route('reportes.planeacion.estadisticas')}}">Estadisticas del Formato T</a>
                        @endcan
                        @can('planeacion.grupos.vulnerables')
                            <a class="dropdown-item" href="{{route('reportes.planeacion.grupos_vulnerables')}}">Grupos Vulnerables</a>
                        @endcan
                        @can('planeacion.ingresos.propios')
                            <a class="dropdown-item" href="{{route('reportes.planeacion.ingresos_propios')}}">Ingresos Propios</a>
                        @endcan
                    </div>
                </li>

                @can('estadisticas.ecursos')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Estadísticas
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="{{route('estadisticas.ecursos')}}">Cursos</a>
                        </div>
                    </li>
                @endcan

                @can('firma.firmar')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Firma Electronica
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('firma.agregar.documento')
                                <a class="dropdown-item" href="#">Agregar Documentos</a>
                            @endcan
                            @can('firma.firmar')
                                <a class="dropdown-item" href="{{route('firma.inicio')}}">Firmar</a>
                            @endcan
                        </div>
                    </li>
                @endcan

            </ul>
            <ul class="navbar-nav ml-auto nav-flex-icons">
                <li class="nav-item g-mx-5-lg dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-55" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Notificaciones
                        @if(count(auth()->user()->unreadNotifications))
                            <span class="badge badge-pill badge-primary ml-2">
                                {{count(auth()->user()->unreadNotifications)}}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        @foreach (auth()->user()->unreadNotifications as $cadwell)
                            <a href={{$cadwell->data['url']}} class="dropdown-item">
                                <i class="fas fa-envelope mr-2"></i> {{$cadwell->data['titulo']}}
                                <br><span class="float-right text-muted text-sm">{{$cadwell->created_at->diffForHumans()}}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
                <li class="nav-item avatar dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-55" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg-right dropdown-secondary" aria-labelledby="navbarDropdownMenuLink-55">
                        <a class="dropdown-item" href="#">
                            {{ Auth::user()->name }}
                        </a>
                        <!--can('password.update')-->
                            <a class="dropdown-item" href="{{route('password.view')}}">Cambiar Contraseña</a>
                        <!--endcan-->
                        <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('Cerrar Sesión') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        @endguest
    </div>
</nav>
<!--/.Navbar -->
<!-- Modal Cancel Folio -->
<div class="modal fade" id="ModalFinanciero" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><b>Generador de Reporte de Tramites Validados/Recepcionados</b></h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('reporte_valrecep') }}" method="post" id="valrecep">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-2"></div>
                    <div class="form-group col-md-4">
                        <label for="fini"><b>Mes Inicial</b></label>
                        <select name="fini" id="fini">
                                <option value="01">ENERO</option>
                                <option value="02">FEBRERO</option>
                                <option value="03">MARZO</option>
                                <option value="04">ABRIL</option>
                                <option value="05">MAYO</option>
                                <option value="06">JUNIO</option>
                                <option value="07">JULIO</option>
                                <option value="08">AGOSTO</option>
                                <option value="09">SEPTIEMBRE</option>
                                <option value="10">OCTUBRE</option>
                                <option value="11">NOVIEMBRE</option>
                                <option value="12">DICIEMBRE</option>
                        </select>
                    </div>
                    <div class="form-group col-md-1"></div>
                    <div class="form-group col-md-4">
                        <label for="ffin"><b>Mes Final</b></label>
                        <select name="ffin" id="ffin">
                                <option value="01">ENERO</option>
                                <option value="02">FEBRERO</option>
                                <option value="03">MARZO</option>
                                <option value="04">ABRIL</option>
                                <option value="05">MAYO</option>
                                <option value="06">JUNIO</option>
                                <option value="07">JULIO</option>
                                <option value="08">AGOSTO</option>
                                <option value="09">SEPTIEMBRE</option>
                                <option value="10">OCTUBRE</option>
                                <option value="11">NOVIEMBRE</option>
                                <option value="12">DICIEMBRE</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2"></div>
                    <div class="form-group col-md-4">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                    <div class="form-group col-md-1"></div>
                    <div class="form-group col-md-4">
                        <button type="submit" class="btn btn-primary" >Aceptar</button>
                    </div>
                    <div class="form-group col-md-1"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Cancel Folio -->
<div class="modal fade" id="ModalExpIns" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><b>Exportar Instructores</b></h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="form-row">
                <div class="form-group col-md-1"></div>
                <div class="form-group col-md-5">
                    <a class="btn btn-info" href="{{route('academico.exportar.instructores')}}">Exportar Por Instructor</a>
                </div>
                <div class="form-group col-md-5">
                    <a class="btn btn-info" href="{{route('academico.exportar.instructoresByespecialidad')}}">Exportar Por Especialidad</a>
                </div>
                <div class="form-group col-md-1"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5"></div>
                <div class="form-group col-md-3">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END -->
