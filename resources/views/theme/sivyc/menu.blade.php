<!--Navbar -->
<nav class="mb-1 navbar navbar-expand-lg navbar-dark pink2 darken-4">    
    <a href="https://sivyc.icatech.gob.mx" class="navbar-brand g-text-underline--hover">
        <img src="{{asset('img/sivyc.png') }}" alt="SIVyC" height="40">
    </a>    
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
                {{--  <!--SUPERVISIONES
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
                @endcan-->  --}}
                <!--AGREGAR NUEVO ELEMENTO EN EL MENU END-->
                @can('preinscripcion.grupo')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Preinscripci&oacute;n
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('alumnos.index')
                                <a class="dropdown-item" href="{{ route('alumnos.index') }}">Aspirantes</a>
                            @endcan
                            @can('preinscripcion.alumnos')
                                <a class="dropdown-item" href="{{ route('preinscripcion.alumnos') }}">Alumnos</a>
                            @endcan
                            @can('preinscripcion.grupo.save')
                                <a class="dropdown-item" href="{{ route('preinscripcion.grupo.nuevo') }}">Nuevo Grupo</a>
                                <a class="dropdown-item" href="{{ route('preinscripcion.buscar') }}">Buscar Grupo</a>
                            @endcan
                        </div>
                    </li>
                @endcan
                @canany(['solicitud.apertura', 'solicitud.exoneracion', 'supre.index', 'contratos.index', 'pagos.inicio',
                    'prevalidar_index-instructor', 'metasavances.index', 'solicitud.rf001','vobo.rf001'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitud
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('solicitud.apertura')
                                <a class="dropdown-item" href="{{ route('solicitud.apertura') }}">Clave de Apertura ARC01</a>                                
                                <a class="dropdown-item" href="{{ route('solicitud.apertura.modificar') }}">Modificaci&oacute;n Apertura ARC02</a>                                
                                <a class="dropdown-item" href="{{ route('solicitud.apertura.turnar') }}">Turnar  Solicitud ARC</a>
                                <a class="dropdown-item" href="{{ route('solicitud.apertura.search') }}">Buscar ARC01</a>
                            @endcan
                            @can('solicitud.exoneracion')
                                <a class="dropdown-item" href="{{ route('solicitud.exoneracion') }}">Exoneración / Reducción de Cuotas</a>
                                <a class="dropdown-item" href="{{ route('solicitud.exoneracion.search') }}">Buscar Exoneración</a>
                            @endcan
                            @can('supre.index')
                                <a class="dropdown-item" href="{{ route('supre-inicio') }}">Suficiencia Presupuestal</a>
                            @endcan
                            @can('contratos.index')
                                <a class="dropdown-item" href="{{ route('contrato-inicio') }}">Contratos</a>
                            @endcan
                            @can('pagos.inicio')
                                <a class="dropdown-item" href="{{ route('pago-inicio') }}">Pagos</a>
                            @endcan
                            @can('prevalidar_index-instructor')
                                <a class="dropdown-item" href="{{ route('prevalidar-ins') }}">Turnar Instructor</a>
                            @endcan
                            @can('metasavances.index')
                                <a class="dropdown-item" href="{{ route('pat.metavance.mostrar') }}">Registro de Metas y Avances PAT</a>
                            @endcan
                            @can('efirma.index')
                                <a class="dropdown-item" href="{{ route('firma.inicio') }}">eFirma Instructores</a>
                            @endcan
                            @canany(['solicitud.rf001','vobo.rf001'])
                                <a class="dropdown-item" href="{{ route('reporte.rf001.sent') }}">Solicitud RF001</a>
                            @endcanany
                            @can('buzon.efirma.constancias')
                                <a class="dropdown-item" href="{{ route('grupo.efirma.index') }}">eFirma Constancias</a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                @canany(['grupos.calificaciones', 'grupos.recibos', 'expunico.buzon.index', 'expedientes.unicos.index'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Grupos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('grupos.calificaciones')
                                <a class="dropdown-item" href="{{ route('grupos.calificaciones') }}">Registrar Calificaciones</a>
                            @endcan
                            @can('grupos.asignarfolios')
                                <a class="dropdown-item" href="{{ route('grupos.asignarfolios') }}">Asignar Folios</a>
                            @endcan
                            @can('grupos.cancelacionfolios')
                                <a class="dropdown-item" href="{{ route('grupos.cancelacionfolios') }}">Cancelar Folios</a>
                            @endcan
                            @can('grupos.consultas')
                                <a class="dropdown-item" href="{{ route('grupos.consultas') }}">Buscar Grupo</a>
                            @endcan
                            @can('grupos.recibos')
                                <a class="dropdown-item" href="{{ route('grupos.recibos') }}">Asignar Recibo de Pago</a>
                            @endcan
                            @can('grupos.recibos')
                                <a class="dropdown-item" href="{{ route('grupos.recibos.buscar') }}">Buscar Recibo de Pago</a>
                            @endcan
                            @can('expedientes.unicos.index')
                                <a class="dropdown-item" href="{{ route('expunico.principal.mostrar.get') }}">Registro de Expedientes Unicos</a>
                            @endcan
                            @can('expunico.buzon.index')
                                <a class="dropdown-item" href="{{ route('buzon.expunico.index') }}">Buzon de Expediente Unicos</a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                @can('formatot.menu.indice')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a href="#" class="nav-link g-color-2025--hover" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            FormatoT
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkValidacion">
                            @can('vista.formatot.unidades.indice')
                                <a class="dropdown-item" href="{{ route('vista_formatot') }}">Gestión de FormatoT</a>
                            @endcan
                            <a class="dropdown-item" href="{{ route('formatot.consulta.index') }}">Consulta FormatoT</a>
                            @can('vista.validacion.enlaces.dta')
                                <a class="dropdown-item" href="{{ route('validacion.cursos.enviados.dta') }}">Revisión FormatoT</a>
                            @endcan
                            @can('vista.validacion.direccion.dta')
                                <a class="dropdown-item" href="{{ route('validacion.dta.revision.cursos.indice') }}">Validación FormatoT DTA</a>
                                <a class="dropdown-item" href="{{ route('indice.dta.aperturado.indice') }}">Cursos Aperturado FormatoT</a>
                            @endcan
                            @can('vista.revision.validacion.planeacion.indice')
                                <a class="dropdown-item" href="{{ route('planeacion.formatot.index') }}">Validación FormatoT DP</a>
                            @endcan                           
                            @canany(['vista.validacion.direccion.dta','vista.validacion.enlaces.dta'])
                                <a href="{{ route('checar.memorandum.dta.mes') }}" class="dropdown-item">Memos Turnados a DTA</a>
                            @endcanany

                            {{-- agregar nuevo elemento a menu END --}}
                            {{-- @can('vista.formatot.unidades.indice')
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
                            @endcan --}}
                            {{-- <a class="dropdown-item" href="{{route('reportes.formatoT')}}">Reporte de Formato T</a>                         --}}
                            <a class="dropdown-item" href="{{ route('seguimento.avance.unidades.formatot.ejecutiva.index') }}">Seguimiento Ejecutivo</a>
                        </div>
                    </li>
                @endcan
                @can('solicitudes')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitudes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('solicitudes.aperturas')
                                <a class="dropdown-item" href="{{ route('solicitudes.aperturas') }}">Aperturas ARC01 y ARC02</a>
                                <a class="dropdown-item" href="{{ route('solicitudes.aperturas.search') }}">Busqueda Aperturas ARC01</a>
                            @endcan
                            @can('solicitudes.exoneracion')
                                <a class="dropdown-item" href="{{ route('solicitudes.exoneracion') }}">Exoneración y/o Reducción de Cuotas</a>
                                <a class="dropdown-item" href="{{ route('solicitudes.exoneracion.search') }}">Buscar Exoneración</a>
                            @endcan
                            @can('solicitudes.folios')
                                <a class="dropdown-item" href="{{ route('solicitudes.folios') }}">Lote de Folios</a>
                            @endcan
                            @can('solicitudes.cancelacionfolios')
                                <a class="dropdown-item" href="{{ route('solicitudes.cancelacionfolios') }}">Cancelaci&oacute;n Folios</a>
                            @endcan
                            @can('prevalidar_index-instructor')
                                <a class="dropdown-item" href="{{ route('prevalidar-ins') }}">Validación de Instructores</a>
                            @endcan
                            @can('buzon.plane.pat')
                                <a class="dropdown-item" href="{{ route('pat.buzon.index') }}">Validación de Metas y Avances PAT</a>
                            @endcan
                            @can('fechaspat.index')
                                <a class="dropdown-item" href="{{ route('pat.fechaspat.mostrar') }}">Programación de Fechas de Entrega PAT</a>
                            @endcan
                            @can('solicitudes.contratos.index')
                                <a class="dropdown-item" href="{{ route('contrato-inicio') }}">Contratos</a>
                            @endcan
                            @can('solicitudes.pagos.inicio')
                                <a class="dropdown-item" href="{{ route('pago-inicio') }}">Recepci&oacute;n Digital para Pagos</a>
                            @endcan
                            @can('solicitudes.transferencia')
                                <a class="dropdown-item" href="{{ route('solicitudes.transferencia.index') }}">Transferencia BANCARIA</a>
                            @endcan

                            @can('validacion.rf001')
                                <a href="{{ route('administrativo.index') }}" class="dropdown-item">Solicitudes de validación RF001</a>
                            @endcan
                        </div>
                    </li>
                @endcan
                @canany(['preinscripcion.alumnos', 'cursos.index', 'instructor.index', 'organismo.inicio',
                    'convenios.index', 'cerss.inicio', 'areas.inicio', 'especialidades.inicio', 'unidades.index',
                    'exoneraciones.inicio', 'instituto.inicio', 'funproc.pat.index', 'unidades.medida.index', 'funcionarios.inicio'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Catálogos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('preinscripcion.alumnos')
                                <a class="dropdown-item" href="{{ route('preinscripcion.alumnos') }}">Alumnos</a>
                            @endcan
                            @can('cursos.index')
                                <a class="dropdown-item" href="{{ route('curso-inicio') }}">Cursos</a>
                            @endcan
                            @can('instructor.index')
                                <a class="dropdown-item" href="{{ route('instructor-inicio') }}">Instructor</a>
                            @endcan
                            @can('organismo.inicio')
                                <a class="dropdown-item" href="{{ route('organismos.index') }}">Organismos Publicos</a>
                            @endcan
                            @can('convenios.index')
                                <a class="dropdown-item" href="{{ route('convenios.index') }}">Convenios</a>
                            @endcan
                            @can('cerss.inicio')
                                <a class="dropdown-item" href="{{ route('cerss.inicio') }}">CERSS</a>
                            @endcan
                            @can('areas.inicio')
                                <a class="dropdown-item" href="{{ route('areas.inicio') }}">Áreas</a>
                            @endcan
                            @can('especialidades.inicio')
                                <a class="dropdown-item" href="{{ route('especialidades.inicio') }}">Especialidades</a>
                            @endcan
                            @can('unidades.index')
                                <a class="dropdown-item" href="{{ route('unidades.inicio') }}">Unidades</a>
                            @endcan
                            @can('exoneraciones.inicio')
                                <a class="dropdown-item" href="{{ route('exoneraciones.inicio') }}">Exoneraciones</a>
                            @endcan
                            @can('instituto.inicio')
                                <a class="dropdown-item" href="{{ route('instituto.inicio') }}">Acerca del instituto</a>
                            @endcan
                            @can('funproc.pat.index')
                                <a class="dropdown-item" href="{{ route('pat.funciones.mostrar') }}">Funciones y Procedimientos</a>
                            @endcan
                            @can('unidades.medida.index')
                                <a class="dropdown-item" href="{{ route('pat.unidadesmedida.mostrar') }}">Unidades de Medida</a>
                            @endcan
                            @can('funcionarios.inicio')
                                <a class="dropdown-item" href="{{ route('catalogos.funcionarios.inicio') }}">Funcionarios</a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                @canany(['reportes.cursos', 'planeacion.reporte', 'financieros.reporte', 'vinculacion.reporte',
                    'reportes.911', 'reportes.rdcd08', 'reportes.rcdod11', 'reportes.rf001', 'financieros.reportevalrec',
                    'financieros.reportecursos', 'reportes.pat', 'reportes.dpa'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Reportes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('reportes.cursos')
                                <a class="dropdown-item" href="{{ route('reportes.cursos.index') }}">Cursos Autorizados</a>
                            @endcan
                            @can('planeacion.reporte')
                                <a class="dropdown-item" href="{{ route('planeacion.reporte') }}">Suficiencias Presupuestales</a>
                                <a class="dropdown-item" href="{{ route('planeacion.reporte.costeo') }}">Costeo Suficiencias Presupuestales</a>
                                <a class="dropdown-item" href="{{ route('planeacion.reporte-cancelados') }}">FoliosCancelados</a>
                            @endcan
                            @can('financieros.reporte')
                                <a class="dropdown-item" href="{{ route('financieros.reporte') }}">Estado de Contratos y Pagos</a>
                            @endcan
                            @can('vinculacion.reporte')
                                <a class="dropdown-item" href="{{ route('cursosvinculador.reporte') }}">Alumno por Vinculador</a>
                            @endcan
                            @can('reportes.911')
                                <a class="dropdown-item" href="{{ route('reportes.911.showForm') }}">Reporte 911</a>
                            @endcan
                            @can('reportes.rdcd08')
                                <a class="dropdown-item" href="{{ route('reportes.rdcd08.index') }}">RDCD-08</a>
                            @endcan
                            @can('reportes.rcdod11')
                                <a class="dropdown-item" href="{{ route('reportes.rcdod11.index') }}">RCDOD-11</a>
                            @endcan
                            {{-- @can('reportes.rf001')
                                <a class="dropdown-item" href="{{ route('reportes.concentradoingresos') }}">RF-001</a>
                            @endcan --}}
                            @can('financieros.reportevalrec')
                                <a class="dropdown-item" href="{{ route('docummentospago.reporte') }}">Trámites Recepcionados</a>
                            @endcan
                            @can('financieros.reportecursos')
                                <a class="dropdown-item" href="{{ route('financieros-reporte-cursos') }}">Reporte de Cursos</a>
                            @endcan
                            @can('reportes.pat')
                                <a class="dropdown-item" href="{{ route('reportes.pat') }}">PAT - Concentrado</a>
                            @endcan
                            @can('reportes.dpa')
                                <a class="dropdown-item" href="{{ route('reportes.dpa') }}">DPA - Nómina de Instructores</a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                @canany(['consultas.folios', 'consultas.lotes', 'consultas.cursosaperturados', 'planeacion.estadisticas',
                    'planeacion.grupos.vulnerables', 'planeacion.ingresos.propios', 'consultas.cursosefisico',
                    'consultas.instructor', 'consultas.instructores.disponibles', 'consultas.poa', 'show.cursos.validados',
                    'consulta.bolsa.trabajo'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Consultas
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('consultas.folios')
                                <a class="dropdown-item" href="{{ route('consultas.folios') }}">Folios Asignados</a>
                            @endcan
                            @can('consultas.lotes')
                                <a class="dropdown-item" href="{{ route('consultas.lotes') }}">Actas de Folios</a>
                            @endcan
                            @can('consultas.cursosaperturados')
                                <a class="dropdown-item" href="{{ route('consultas.cursosaperturados') }}">Cursos Aperturados</a>
                            @endcan
                            @can('planeacion.estadisticas')
                                <a class="dropdown-item" href="{{ route('reportes.planeacion.estadisticas') }}">Estadisticas del  Formato T</a>
                            @endcan
                            @can('planeacion.grupos.vulnerables')
                                <a class="dropdown-item" href="{{ route('reportes.planeacion.grupos_vulnerables') }}">Grupos Vulnerables</a>
                            @endcan
                            @can('planeacion.ingresos.propios')
                                <a class="dropdown-item" href="{{ route('reportes.planeacion.ingresos_propios') }}">Ingresos Propios</a>
                            @endcan
                            @can('consultas.cursosefisico')
                                <a class="dropdown-item" href="{{ route('consultas.cursosefisico') }}">Cursos EFisico</a>
                            @endcan
                            @can('consultas.instructor')
                                <a class="dropdown-item" href="{{ route('consultas.instructor') }}">Instructores Asignados</a>
                            @endcan
                            @can('consultas.instructores.disponibles')
                                <a class="dropdown-item" href="{{ route('consultas.instructores.disponibles') }}">Instructores Disponibles</a>
                            @endcan
                            @can('consultas.poa')
                                <a class="dropdown-item" href="{{ route('consultas.poa') }}">POA&Autorizados</a>
                            @endcan
                            @can('show.cursos.validados')
                                <a class="dropdown-item" href="{{ route('cursos_validados.index') }}">Cursos Validados</a>
                            @endcan
                            @can('consulta.bolsa.trabajo')
                                <a class="dropdown-item" href="{{ route('consultas.bolsa.index') }}">Bolsa de trabajo</a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                @canany(['estadisticas.ecursos', 'tablero.metas.index'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Estadísticas
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @can('estadisticas.ecursos')
                                <a class="dropdown-item" href="{{ route('estadisticas.ecursos') }}">Cursos Impartidos</a>
                            @endcan
                            @can('tablero.metas.index')
                                <a class="dropdown-item" href="{{ route('tablero.metas.index') }}">Tablero de Control</a>
                            @endcan
                        </div>
                    </li>
                @endcanany
            </ul>
            <ul class="navbar-nav ml-auto nav-flex-icons">
                <li class="nav-item g-mx-5-lg dropdown">
                    <a class="nav-link g-color-2025--hover dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Notificaciones
                        @if (count(auth()->user()->unreadNotifications))
                            <span class="badge badge-pill badge-primary ml-2">
                                {{ count(auth()->user()->unreadNotifications) }}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right g-color-2025--hover">
                        @foreach (auth()->user()->unreadNotifications as $cadwell)
                            <a href={{ $cadwell->data['url'] }} class="dropdown-item">
                                <i class="fas fa-envelope mr-2"></i> {{ $cadwell->data['titulo'] }}
                                <br>{{ $cadwell->data['cuerpo'] }}
                                <br><span
                                    class="float-right text-muted text-sm">{{ $cadwell->created_at->diffForHumans() }}</span>
                            </a>
                        @endforeach
                        <a href='#' class="dropdown-item">
                            <i class="fas fa-history mr-2"></i> Historial de Notificaciónes
                        </a>
                    </div>
                </li>
                <li class="nav-item avatar dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg-right dropdown-secondary"
                        aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="#">
                            {{ Auth::user()->name }}
                        </a>
                        <!--can('password.update')-->
                        <a class="dropdown-item" href="{{ route('password.view') }}">Cambiar Contraseña</a>
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
