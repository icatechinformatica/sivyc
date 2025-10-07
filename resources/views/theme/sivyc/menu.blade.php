<!--Navbar -->
@php
    $usuario = Auth::user()->load('roles.permissions');
    $permisos['menu_solicitud'] = $usuario->canAny(['solicitud.apertura', 'solicitud.exoneracion', 'supre.index', 'contratos.index', 'pagos.inicio','metasavances.index', 'solicitud.rf001', 'vobo.rf001']);
    $permisos['solicitud_rf001'] = $usuario->canAny(['solicitud.rf001', 'vobo.rf001']);
    $permisos['menu_grupos'] = $usuario->canAny(['grupos.calificaciones', 'grupos.recibos', 'expunico.buzon.index', 'expedientes.unicos.index']);
    $permisos['memos_turnados_dta'] = $usuario->canAny(['vista.validacion.direccion.dta', 'vista.validacion.enlaces.dta']);
    $permisos['menu_catalogo'] = $usuario->canAny(['preinscripcion.alumnos', 'cursos.index', 'instructor.index', 'organismo.inicio',
                    'convenios.index', 'cerss.inicio', 'areas.inicio', 'especialidades.inicio', 'unidades.index',
                    'exoneraciones.inicio', 'instituto.inicio', 'funproc.pat.index', 'unidades.medida.index',
                    'funcionarios.inicio']);
    $permisos['menu_reporte'] = $usuario->canAny(['reportes.cursos', 'planeacion.reporte', 'financieros.reporte', 'vinculacion.reporte',
                    'reportes.911', 'reportes.rdcd08', 'reportes.rcdod11', 'reportes.rf001', 'financieros.reportevalrec',
                    'financieros.reportecursos', 'reportes.pat', 'reportes.dpa','reportes.dv','RH.tarjetatiempo']);
    $permisos['menu_consulta'] = $usuario->canAny(['consultas.folios', 'consultas.lotes', 'consultas.cursosaperturados', 'planeacion.estadisticas',
                    'planeacion.grupos.vulnerables', 'planeacion.ingresos.propios', 'consultas.cursosefisico',
                    'consultas.instructor', 'consultas.instructores.disponibles', 'consultas.poa', 'show.cursos.validados',
                    'consulta.bolsa.trabajo'.'consultas.cursos.exo','consultas.contratosfirmados']);
    $permiso['menu_estadistica'] =  $usuario->canAny(['estadisticas.ecursos', 'tablero.metas.index']);
@endphp
<nav class="mb-1 navbar navbar-expand-lg navbar-dark pink2 darken-4">
    <a href="https://sivyc.icatech.gob.mx" class="navbar-brand g-text-underline--hover">
        <img src="{{ asset('img/sivyc.png') }}" alt="SIVyC" height="45">
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
                @if($usuario->can('preinscripcion.grupo'))
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Preinscripci&oacute;n
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('alumnos.index'))
                                <a class="dropdown-item" href="{{ route('alumnos.index') }}">Aspirantes</a>
                            @endif
                            @if($usuario->can('preinscripcion.alumnos'))
                                <a class="dropdown-item" href="{{ route('preinscripcion.alumnos') }}">Alumnos</a>
                            @endif
                            @if($usuario->can('preinscripcion.grupo.save'))
                                <a class="dropdown-item" href="{{ route('preinscripcion.grupo.nuevo') }}">Nuevo Grupo</a>
                                {{-- <a class="dropdown-item" href="{{ route('preinscripcion.grupovobo') }}">Nuevo Grupo VoBo</a> --}}
                                <a class="dropdown-item" href="{{ route('preinscripcion.buscar') }}">Buscar Grupo</a>
                            @endif
                        </div>
                    </li>
                @endif
                @if($permisos['menu_solicitud'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitud
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('solicitud.apertura'))
                                <a class="dropdown-item" href="{{ route('solicitud.apertura') }}">Clave de Apertura ARC01</a>
                                <a class="dropdown-item" href="{{ route('solicitud.apertura.modificar') }}">Modificaci&oacute;n
                                    Apertura ARC02</a>
                                <a class="dropdown-item" href="{{ route('solicitud.apertura.turnar') }}">Turnar Solicitud ARC</a>
                                <a class="dropdown-item" href="{{ route('solicitud.apertura.search') }}">Buscar ARC01</a>
                            @endif
                            @if($usuario->can('solicitud.exoneracion'))
                                <a class="dropdown-item" href="{{ route('solicitud.exoneracion') }}">Exoneración / Reducción de
                                    Cuotas</a>
                                <a class="dropdown-item" href="{{ route('solicitud.exoneracion.search') }}">Buscar Exoneración</a>
                            @endif
                            @if($usuario->can('supre.index'))
                                <a class="dropdown-item" href="{{ route('supre-inicio') }}">Suficiencia Presupuestal</a>
                            @endif
                            @if($usuario->can('contratos.index'))
                                <a class="dropdown-item" href="{{ route('contrato-inicio') }}">Contratos</a>
                            @endif
                            @if($usuario->can('pagos.inicio'))
                                <a class="dropdown-item" href="{{ route('pago-inicio') }}">Pagos</a>
                            @endif
                            @if($usuario->can('metasavances.index'))
                                <a class="dropdown-item" href="{{ route('pat.metavance.mostrar') }}">Registro de Metas y Avances
                                    PAT</a>
                            @endif
                            @if($usuario->can('efirma.index'))
                                <a class="dropdown-item" href="{{ route('firma.inicio') }}">eFirma Instructores</a>
                            @endif
                            @if($permisos['solicitud_rf001'])
                                <a class="dropdown-item" href="{{ route('reporte.rf001.sent') }}">Solicitud RF001</a>
                            @endif
                            @if($usuario->can('buzon.efirma.constancias'))
                                <a class="dropdown-item" href="{{ route('grupo.efirma.index') }}">eFirma Constancias</a>
                            @endif
                            @if($usuario->can('prevalidar_index-instructor'))
                                <a class="dropdown-item" href="{{ route('prevalidar-ins') }}">Turnar Instructor</a>
                            @endif
                        </div>
                    </li>
                @endif
                @if($permisos['menu_grupos'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Grupos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('grupos.calificaciones'))
                                <a class="dropdown-item" href="{{ route('grupos.calificaciones') }}">Registrar Calificaciones</a>
                            @endif
                            @if($usuario->can('grupos.asignarfolios'))
                                <a class="dropdown-item" href="{{ route('grupos.asignarfolios') }}">Asignar Folios</a>
                            @endif
                            @if($usuario->can('grupos.cancelacionfolios'))
                                <a class="dropdown-item" href="{{ route('grupos.cancelacionfolios') }}">Cancelar Folios</a>
                            @endif
                            @if($usuario->can('grupos.consultas'))
                                <a class="dropdown-item" href="{{ route('grupos.consultas') }}">Buscar Grupo</a>
                            @endif
                            @if($usuario->can('grupos.recibos'))
                                <a class="dropdown-item" href="{{ route('grupos.recibos') }}">Asignar Recibo de Pago</a>
                            @endif
                            @if($usuario->can('grupos.recibos'))
                                <a class="dropdown-item" href="{{ route('grupos.recibos.buscar') }}">Buscar Recibo de Pago</a>
                            @endif
                            @if($usuario->can('expedientes.unicos.index'))
                                <a class="dropdown-item" href="{{ route('expunico.principal.mostrar.get') }}">Registro de
                                    Expedientes Unicos</a>
                            @endif
                            @if($usuario->can('expunico.buzon.index'))
                                <a class="dropdown-item" href="{{ route('buzon.expunico.index') }}">Buzon de Expediente Unicos</a>
                            @endif
                        </div>
                    </li>
                @endif
                @if($usuario->can('formatot.menu.indice'))
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a href="#" class="nav-link g-color-2025--hover" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            FormatoT
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkValidacion">
                            @if($usuario->can('vista.formatot.unidades.indice'))
                                <a class="dropdown-item" href="{{ route('vista_formatot') }}">Gestión de FormatoT</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('formatot.consulta.index') }}">Consulta FormatoT</a>
                            @if($usuario->can('vista.validacion.enlaces.dta'))
                                <a class="dropdown-item" href="{{ route('validacion.cursos.enviados.dta') }}">Revisión
                                    FormatoT</a>
                            @endif
                            @if($usuario->can('vista.validacion.direccion.dta'))
                                <a class="dropdown-item" href="{{ route('validacion.dta.revision.cursos.indice') }}">Validación
                                    FormatoT DTA</a>
                                <a class="dropdown-item" href="{{ route('indice.dta.aperturado.indice') }}">Cursos Aperturado
                                    FormatoT</a>
                            @endif
                            @if($usuario->can('vista.revision.validacion.planeacion.indice'))
                                <a class="dropdown-item" href="{{ route('planeacion.formatot.index') }}">Validación FormatoT
                                    DP</a>
                            @endif
                            @if($permisos['memos_turnados_dta'])
                                <a href="{{ route('checar.memorandum.dta.mes') }}" class="dropdown-item">Memos Turnados a DTA</a>
                            @endif

                            {{-- agregar nuevo elemento a menu END --}}
                            {{-- @if($usuario->can('vista.formatot.unidades.indice'))
                                <a href="{{ route('cursos.reportados.historico.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endif
                            @if($usuario->can('vista.validacion.enlaces.dta'))
                                <a href="{{ route('cursos.reportados.historico.dta.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endif
                            @if($usuario->can('vista.validacion.direccion.dta'))
                                <a href="{{ route('cursos.reportados.historico.direccion.dta.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endif
                            @if($usuario->can('vista.revision.validacion.planeacion.indice'))
                                <a href="{{ route('cursos.reportados.historico.planeacion.index') }}" class="dropdown-item">Cursos Reportados de Meses Anteriores Para la Unidad</a>
                            @endif --}}
                            {{-- <a class="dropdown-item" href="{{route('reportes.formatoT')}}">Reporte de Formato T</a>                         --}}
                            <a class="dropdown-item"
                                href="{{ route('seguimento.avance.unidades.formatot.ejecutiva.index') }}">Seguimiento
                                Ejecutivo</a>
                        </div>
                    </li>
                @endif
                @if($usuario->can('solicitudes'))
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitudes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('solicitudes.aperturas'))
                                <a class="dropdown-item" href="{{ route('solicitudes.aperturas') }}">Aperturas ARC01 y ARC02</a>
                                <a class="dropdown-item" href="{{ route('solicitudes.aperturas.search') }}">Busqueda Aperturas
                                    ARC01</a>
                            @endif
                            @if($usuario->can('solicitudes.exoneracion'))
                                <a class="dropdown-item" href="{{ route('solicitudes.exoneracion') }}">Exoneración y/o Reducción
                                    de Cuotas</a>
                                <a class="dropdown-item" href="{{ route('solicitudes.exoneracion.search') }}">Buscar
                                    Exoneración</a>
                            @endif
                            @if($usuario->can('solicitudes.folios'))
                                <a class="dropdown-item" href="{{ route('solicitudes.folios') }}">Lote de Folios</a>
                            @endif
                            @if($usuario->can('solicitudes.cancelacionfolios'))
                                <a class="dropdown-item" href="{{ route('solicitudes.cancelacionfolios') }}">Cancelaci&oacute;n
                                    Folios</a>
                            @endif
                            @if($usuario->can('prevalidar_index-instructor'))
                                <a class="dropdown-item" href="{{ route('prevalidar-ins') }}">Validación de Instructores</a>
                            @endif
                            @if($usuario->can('solicitudes.buzon.asiparantesinstructor'))
                                <a class="dropdown-item" href="{{ route('aspirante.instructor.index') }}">Prevalidacion de Aspirantes a Instructores</a>
                            @endif
                            @if($usuario->can('buzon.plane.pat'))
                                <a class="dropdown-item" href="{{ route('pat.buzon.index') }}">Validación de Metas y Avances
                                    PAT</a>
                            @endif
                            @if($usuario->can('fechaspat.index'))
                                <a class="dropdown-item" href="{{ route('pat.fechaspat.mostrar') }}">Programación de Fechas de
                                    Entrega PAT</a>
                            @endif
                            @if($usuario->can('solicitudes.contratos.index'))
                                <a class="dropdown-item" href="{{ route('contrato-inicio') }}">Contratos</a>
                            @endif
                            @if($usuario->can('solicitudes.pagos.inicio'))
                                <a class="dropdown-item" href="{{ route('pago-inicio') }}">Recepci&oacute;n Digital para
                                    Pagos</a>
                            @endif
                            @if($usuario->can('solicitudes.transferencia'))
                                <a class="dropdown-item" href="{{ route('solicitudes.transferencia.index') }}">Transferencia
                                    BANCARIA</a>
                            @endif

                            @if($usuario->can('validacion.rf001'))
                                <a href="{{ route('administrativo.index') }}" class="dropdown-item">Solicitudes de validación
                                    RF001</a>
                            @endif
                            @if($usuario->can('listado.credencial'))
                                <a href="{{ route('credencial.indice') }}">Credencialización de Funcionarios</a>
                            @endif
                            @if($usuario->can('solicitudes.vb.grupos'))
                                <a href="{{ route('solicitudes.vb.grupos') }}">VB.- Grupos de Capacitación</a>
                            @endif
                            @if($usuario->can('prevalidar_index-instructor'))
                                <a class="dropdown-item" href="{{ route('prevalidar-ins') }}">Turnar Instructor</a>
                            @endif
                        </div>
                    </li>
                @endif
                @if($permisos['menu_catalogo'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#"
                            id="navbarDropdownMenuLink"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Catálogos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('preinscripcion.alumnos'))
                                <a class="dropdown-item" href="{{ route('preinscripcion.alumnos') }}">Alumnos</a>
                            @endif
                            @if($usuario->can('cursos.index'))
                                <a class="dropdown-item" href="{{ route('curso-inicio') }}">Cursos</a>
                            @endif
                            @if($usuario->can('instructor.index'))
                                <a class="dropdown-item" href="{{ route('instructor-inicio') }}">Instructor</a>
                            @endif
                            @if($usuario->can('organismo.inicio'))
                                <a class="dropdown-item" href="{{ route('organismos.index') }}">Organismos Publicos</a>
                            @endif
                            @if($usuario->can('convenios.index'))
                                <a class="dropdown-item" href="{{ route('convenios.index') }}">Convenios</a>
                            @endif
                            @if($usuario->can('cerss.inicio'))
                                <a class="dropdown-item" href="{{ route('cerss.inicio') }}">CERSS</a>
                            @endif
                            @if($usuario->can('areas.inicio'))
                                <a class="dropdown-item" href="{{ route('areas.inicio') }}">Áreas</a>
                            @endif
                            @if($usuario->can('especialidades.inicio'))
                                <a class="dropdown-item" href="{{ route('especialidades.inicio') }}">Especialidades</a>
                            @endif
                            @if($usuario->can('unidades.index'))
                                <a class="dropdown-item" href="{{ route('unidades.inicio') }}">Unidades</a>
                            @endif
                            @if($usuario->can('exoneraciones.inicio'))
                                <a class="dropdown-item" href="{{ route('exoneraciones.inicio') }}">Exoneraciones</a>
                            @endif
                            @if($usuario->can('instituto.inicio'))
                                <a class="dropdown-item" href="{{ route('instituto.inicio') }}">Acerca del instituto</a>
                            @endif
                            @if($usuario->can('funproc.pat.index'))
                                <a class="dropdown-item" href="{{ route('pat.funciones.mostrar') }}">Funciones y
                                    Procedimientos</a>
                            @endif
                            @if($usuario->can('unidades.medida.index'))
                                <a class="dropdown-item" href="{{ route('pat.unidadesmedida.mostrar') }}">Unidades de Medida</a>
                            @endif
                            @if($usuario->can('funcionarios.inicio'))
                                <a class="dropdown-item" href="{{ route('catalogos.funcionarios.inicio') }}">Funcionarios</a>
                            @endif
                        </div>
                    </li>
                @endif
                @if($permisos['menu_reporte'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Reportes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('reportes.cursos'))
                                <a class="dropdown-item" href="{{ route('reportes.cursos.index') }}">Cursos Autorizados</a>
                            @endif
                            @if($usuario->can('planeacion.reporte'))
                                <a class="dropdown-item" href="{{ route('planeacion.reporte') }}">Suficiencias Presupuestales</a>
                                <a class="dropdown-item" href="{{ route('planeacion.reporte.costeo') }}">Costeo Suficiencias Presupuestales</a>

                                {{-- DAR DE BAJA <a class="dropdown-item" href="{{ route('planeacion.reporte-cancelados') }}">FoliosCancelados</a> --}}
                            @endif
                            @if($usuario->can('financieros.reporte'))
                                <a class="dropdown-item" href="{{ route('financieros.reporte') }}">Estado de Contratos y
                                    Pagos</a>
                            @endif
                            @if($usuario->can('vinculacion.reporte'))
                                <a class="dropdown-item" href="{{ route('cursosvinculador.reporte') }}">Alumno por Vinculador</a>
                            @endif
                            @if($usuario->can('reportes.911'))
                                <a class="dropdown-item" href="{{ route('reportes.911.showForm') }}">Reporte 911</a>
                            @endif
                            @if($usuario->can('reportes.rdcd08'))
                                <a class="dropdown-item" href="{{ route('reportes.rdcd08.index') }}">RDCD-08</a>
                            @endif
                            @if($usuario->can('reportes.rcdod11'))
                                <a class="dropdown-item" href="{{ route('reportes.rcdod11.index') }}">RCDOD-11</a>
                            @endif
                            {{-- @if($usuario->can('reportes.rf001'))
                                <a class="dropdown-item" href="{{ route('reportes.concentradoingresos') }}">RF-001</a>
                            @endif --}}
                            @if($usuario->can('financieros.reportevalrec'))
                                <a class="dropdown-item" href="{{ route('docummentospago.reporte') }}">Trámites Recepcionados</a>
                            @endif
                            @if($usuario->can('financieros.reportecursos'))
                                <a class="dropdown-item" href="{{ route('financieros-reporte-cursos') }}">Reporte de Cursos</a>
                            @endif
                            @if($usuario->can('reportes.pat'))
                                <a class="dropdown-item" href="{{ route('reportes.pat') }}">PAT - Concentrado</a>
                            @endif
                            @if($usuario->can('reportes.dpa'))
                                <a class="dropdown-item" href="{{ route('reportes.dpa') }}">DPA - Nómina de Instructores</a>
                            @endif
                            @if($usuario->can('reportes.dv'))
                                <a class="dropdown-item" href="{{ route('reportes.dv') }}">DV - Operación con Convenios</a>
                            @endif
                            @if($usuario->can('RH.tarjetatiempo'))
                                <a class="dropdown-item" href="{{ route('rh.reporte.quincenal') }}">RH - Tarjeta de Tiempo</a>
                            @endif
                            @if($usuario->can('RH.tarjetatiempo'))
                                <a class="dropdown-item" href="{{ route('rh.index') }}">RH - Registro de Checado</a>
                            @endif
                        </div>
                    </li>
                @endif
                @if($permisos['menu_consulta'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Consultas
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('consultas.lotes'))
                                <a class="dropdown-item" href="{{ route('consultas.lotes') }}">Actas de Folios</a>
                            @endif
                            @if($usuario->can('consulta.bolsa.trabajo'))
                                <a class="dropdown-item" href="{{ route('consultas.bolsa.index') }}">Incorporación Laboral</a>
                            @endif
                            @if($usuario->can('consultas.cursosaperturados'))
                                <a class="dropdown-item" href="{{ route('consultas.cursosaperturados') }}">Cursos Aperturados</a>
                            @endif
                            @if($usuario->can('consultas.contratosfirmados'))
                                <a class="dropdown-item" href="{{ route('consultas.contratosfirmados') }}"> DTA - Contratos</a>
                            @endif
                            @if($usuario->can('consultas.cursosefisico'))
                                <a class="dropdown-item" href="{{ route('consultas.cursosefisico') }}">Cursos EFisico</a>
                            @endif
                            @if($usuario->can('show.cursos.validados'))
                                <a class="dropdown-item" href="{{ route('cursos_validados.index') }}">Cursos Validados</a>
                            @endif
                            @if($usuario->can('planeacion.estadisticas'))
                                <a class="dropdown-item" href="{{ route('reportes.planeacion.estadisticas') }}">Estadisticas FormatoT</a>
                            @endif
                            @if($usuario->can('consultas.folios'))
                                <a class="dropdown-item" href="{{ route('consultas.folios') }}">Folios Asignados</a>
                            @endif
                            @if($usuario->can('planeacion.grupos.vulnerables'))
                                <a class="dropdown-item" href="{{ route('reportes.planeacion.grupos_vulnerables') }}">Grupos Vulnerables</a>
                            @endif
                            @if($usuario->can('planeacion.ingresos.propios'))
                                <a class="dropdown-item" href="{{ route('reportes.planeacion.ingresos_propios') }}">Ingresos Propios</a>
                            @endif

                            @if($usuario->can('consultas.instructor'))
                                <a class="dropdown-item" href="{{ route('consultas.instructor') }}">Instructores Asignados</a>
                            @endif
                            @if($usuario->can('consultas.instructores.disponibles'))
                                <a class="dropdown-item" href="{{ route('consultas.instructores.disponibles') }}">Instructores Disponibles</a>
                            @endif
                            @if($usuario->can('consultas.poa'))
                                <a class="dropdown-item" href="{{ route('consultas.poa') }}">POA&Autorizados</a>
                            @endif

                        </div>
                    </li>
                @endif
                @if($permiso['menu_estadistica'])
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Estadísticas
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @if($usuario->can('estadisticas.ecursos'))
                                <a class="dropdown-item" href="{{ route('estadisticas.ecursos') }}">Cursos Impartidos</a>
                            @endif
                            @if($usuario->can('tablero.metas.index'))
                                <a class="dropdown-item" href="{{ route('tablero.metas.index') }}">Tablero de Control</a>
                            @endif
                        </div>
                    </li>
                @endif
            </ul>
            <ul class="navbar-nav ml-auto nav-flex-icons">
                <li class="nav-item g-mx-5-lg dropdown">
                    <a class="nav-link g-color-2025--hover dropdown-toggle" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                        <i class="fas fa-user" style="color:rgb(216, 2, 109)"></i>
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
