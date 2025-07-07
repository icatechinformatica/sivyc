<!--Navbar -->
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
                {{--  <!--SUPERVISIONES
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Supervisiones
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{route('supervision.escolar')}}">Escolar</a>
                    </div>
                </li>
                -->  --}}
                <!--AGREGAR NUEVO ELEMENTO EN EL MENU END-->
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Preinscripci&oacute;n
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('alumnos.index') }}">Aspirantes</a>
                        <a class="dropdown-item" href="{{ route('preinscripcion.alumnos') }}">Alumnos</a>
                        <a class="dropdown-item" href="{{ route('preinscripcion.grupo.nuevo') }}">Nuevo Grupo</a>
                        <a class="dropdown-item" href="{{ route('preinscripcion.grupovobo') }}">Nuevo Grupo VoBo</a>
                        <a class="dropdown-item" href="{{ route('preinscripcion.buscar') }}">Buscar Grupo</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Solicitud
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('solicitud.apertura') }}">Clave de Apertura ARC01</a>
                        <a class="dropdown-item" href="{{ route('solicitud.apertura.modificar') }}">Modificaci&oacute;n
                            Apertura ARC02</a>
                        <a class="dropdown-item" href="{{ route('solicitud.apertura.turnar') }}">Turnar Solicitud ARC</a>
                        <a class="dropdown-item" href="{{ route('solicitud.apertura.search') }}">Buscar ARC01</a>
                        <a class="dropdown-item" href="{{ route('solicitud.exoneracion') }}">Exoneración / Reducción de
                            Cuotas</a>
                        <a class="dropdown-item" href="{{ route('solicitud.exoneracion.search') }}">Buscar Exoneración</a>
                        <a class="dropdown-item" href="{{ route('supre-inicio') }}">Suficiencia Presupuestal</a>
                        <a class="dropdown-item" href="{{ route('contrato-inicio') }}">Contratos</a>
                        <a class="dropdown-item" href="{{ route('pago-inicio') }}">Pagos</a>
                        <a class="dropdown-item" href="{{ route('prevalidar-ins') }}">Turnar Instructor</a>
                        <a class="dropdown-item" href="{{ route('pat.metavance.mostrar') }}">Registro de Metas y Avances
                            PAT</a>
                        <a class="dropdown-item" href="{{ route('firma.inicio') }}">eFirma Instructores</a>
                        <a class="dropdown-item" href="{{ route('reporte.rf001.sent') }}">Solicitud RF001</a>
                        <a class="dropdown-item" href="{{ route('grupo.efirma.index') }}">eFirma Constancias</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Grupos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('grupos.calificaciones') }}">Registrar Calificaciones</a>
                        <a class="dropdown-item" href="{{ route('grupos.asignarfolios') }}">Asignar Folios</a>
                        <a class="dropdown-item" href="{{ route('grupos.cancelacionfolios') }}">Cancelar Folios</a>
                        <a class="dropdown-item" href="{{ route('grupos.consultas') }}">Buscar Grupo</a>
                        <a class="dropdown-item" href="{{ route('grupos.recibos') }}">Asignar Recibo de Pago</a>
                        <a class="dropdown-item" href="{{ route('grupos.recibos.buscar') }}">Buscar Recibo de Pago</a>
                        <a class="dropdown-item" href="{{ route('expunico.principal.mostrar.get') }}">Registro de
                            Expedientes Unicos</a>
                        <a class="dropdown-item" href="{{ route('buzon.expunico.index') }}">Buzon de Expediente Unicos</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a href="#" class="nav-link g-color-2025--hover" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        FormatoT
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkValidacion">
                        <a class="dropdown-item" href="{{ route('vista_formatot') }}">Gestión de FormatoT</a>
                        <a class="dropdown-item" href="{{ route('formatot.consulta.index') }}">Consulta FormatoT</a>
                        <a class="dropdown-item" href="{{ route('validacion.cursos.enviados.dta') }}">Revisión
                            FormatoT</a>
                        <a class="dropdown-item" href="{{ route('validacion.dta.revision.cursos.indice') }}">Validación
                            FormatoT DTA</a>
                        <a class="dropdown-item" href="{{ route('indice.dta.aperturado.indice') }}">Cursos Aperturado
                            FormatoT</a>
                        <a class="dropdown-item" href="{{ route('planeacion.formatot.index') }}">Validación FormatoT
                            DP</a>
                        <a href="{{ route('checar.memorandum.dta.mes') }}" class="dropdown-item">Memos Turnados a DTA</a>
                        <a class="dropdown-item"
                            href="{{ route('seguimento.avance.unidades.formatot.ejecutiva.index') }}">Seguimiento
                            Ejecutivo</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Solicitudes
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('solicitudes.aperturas') }}">Aperturas ARC01 y ARC02</a>
                        <a class="dropdown-item" href="{{ route('solicitudes.aperturas.search') }}">Busqueda Aperturas
                            ARC01</a>
                        <a class="dropdown-item" href="{{ route('solicitudes.exoneracion') }}">Exoneración y/o Reducción
                            de Cuotas</a>
                        <a class="dropdown-item" href="{{ route('solicitudes.exoneracion.search') }}">Buscar
                            Exoneración</a>
                        <a class="dropdown-item" href="{{ route('solicitudes.folios') }}">Lote de Folios</a>
                        <a class="dropdown-item" href="{{ route('solicitudes.cancelacionfolios') }}">Cancelaci&oacute;n
                            Folios</a>
                        <a class="dropdown-item" href="{{ route('prevalidar-ins') }}">Validación de Instructores</a>
                        <a class="dropdown-item" href="{{ route('aspirante.instructor.index') }}">Prevalidacion de Aspirantes a Instructores</a>
                        <a class="dropdown-item" href="{{ route('pat.buzon.index') }}">Validación de Metas y Avances
                            PAT</a>
                        <a class="dropdown-item" href="{{ route('pat.fechaspat.mostrar') }}">Programación de Fechas de
                            Entrega PAT</a>
                        <a class="dropdown-item" href="{{ route('contrato-inicio') }}">Contratos</a>
                        <a class="dropdown-item" href="{{ route('pago-inicio') }}">Recepci&oacute;n Digital para
                            Pagos</a>
                        <a class="dropdown-item" href="{{ route('solicitudes.transferencia.index') }}">Transferencia
                            BANCARIA</a>
                        <a href="{{ route('administrativo.index') }}" class="dropdown-item">Solicitudes de validación
                            RF001</a>
                        <a href="{{ route('credencial.indice') }}">Credencialización de Funcionarios</a>
                        <a href="{{ route('solicitudes.vb.grupos') }}">VB.- Grupos de Capacitación</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#"
                        id="navbarDropdownMenuLink"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Catálogos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('preinscripcion.alumnos') }}">Alumnos</a>
                        <a class="dropdown-item" href="{{ route('curso-inicio') }}">Cursos</a>
                        <a class="dropdown-item" href="{{ route('instructor-inicio') }}">Instructor</a>
                        <a class="dropdown-item" href="{{ route('organismos.index') }}">Organismos Publicos</a>
                        <a class="dropdown-item" href="{{ route('convenios.index') }}">Convenios</a>
                        <a class="dropdown-item" href="{{ route('cerss.inicio') }}">CERSS</a>
                        <a class="dropdown-item" href="{{ route('areas.inicio') }}">Áreas</a>
                        <a class="dropdown-item" href="{{ route('especialidades.inicio') }}">Especialidades</a>
                        <a class="dropdown-item" href="{{ route('unidades.inicio') }}">Unidades</a>
                        <a class="dropdown-item" href="{{ route('exoneraciones.inicio') }}">Exoneraciones</a>
                        <a class="dropdown-item" href="{{ route('instituto.inicio') }}">Acerca del instituto</a>
                        <a class="dropdown-item" href="{{ route('pat.funciones.mostrar') }}">Funciones y
                            Procedimientos</a>
                        <a class="dropdown-item" href="{{ route('pat.unidadesmedida.mostrar') }}">Unidades de Medida</a>
                        <a class="dropdown-item" href="{{ route('catalogos.funcionarios.inicio') }}">Funcionarios</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reportes
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('reportes.cursos.index') }}">Cursos Autorizados</a>
                        <a class="dropdown-item" href="{{ route('planeacion.reporte') }}">Suficiencias Presupuestales</a>
                        <a class="dropdown-item" href="{{ route('planeacion.reporte.costeo') }}">Costeo Suficiencias Presupuestales</a>
                        {{-- DAR DE BAJA <a class="dropdown-item" href="{{ route('planeacion.reporte-cancelados') }}">FoliosCancelados</a> --}}
                        <a class="dropdown-item" href="{{ route('financieros.reporte') }}">Estado de Contratos y
                            Pagos</a>
                        <a class="dropdown-item" href="{{ route('cursosvinculador.reporte') }}">Alumno por Vinculador</a>
                        <a class="dropdown-item" href="{{ route('reportes.911.showForm') }}">Reporte 911</a>
                        <a class="dropdown-item" href="{{ route('reportes.rdcd08.index') }}">RDCD-08</a>
                        <a class="dropdown-item" href="{{ route('reportes.rcdod11.index') }}">RCDOD-11</a>
                        {{-- <a class="dropdown-item" href="{{ route('reportes.concentradoingresos') }}">RF-001</a> --}}
                        <a class="dropdown-item" href="{{ route('docummentospago.reporte') }}">Trámites Recepcionados</a>
                        <a class="dropdown-item" href="{{ route('financieros-reporte-cursos') }}">Reporte de Cursos</a>
                        <a class="dropdown-item" href="{{ route('reportes.pat') }}">PAT - Concentrado</a>
                        <a class="dropdown-item" href="{{ route('reportes.dpa') }}">DPA - Nómina de Instructores</a>
                        <a class="dropdown-item" href="{{ route('reportes.dv') }}">DV - Operación con Convenios</a>
                        <a class="dropdown-item" href="{{ route('rh.reporte.quincenal') }}">RH - Tarjeta de Tiempo</a>
                        <a class="dropdown-item" href="{{ route('rh.index') }}">RH - Registro de Checado</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Consultas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('consultas.lotes') }}">Actas de Folios</a>
                        <a class="dropdown-item" href="{{ route('consultas.bolsa.index') }}">Incorporación Laboral</a>
                        <a class="dropdown-item" href="{{ route('consultas.cursosaperturados') }}">Cursos Aperturados</a>
                        <a class="dropdown-item" href="{{ route('consultas.contratosfirmados') }}"> DTA - Contratos</a>
                        <a class="dropdown-item" href="{{ route('consultas.cursosefisico') }}">Cursos EFisico</a>
                        <a class="dropdown-item" href="{{ route('cursos_validados.index') }}">Cursos Validados</a>
                        <a class="dropdown-item" href="{{ route('reportes.planeacion.estadisticas') }}">Estadisticas FormatoT</a>
                        <a class="dropdown-item" href="{{ route('consultas.folios') }}">Folios Asignados</a>
                        <a class="dropdown-item" href="{{ route('reportes.planeacion.grupos_vulnerables') }}">Grupos Vulnerables</a>
                        <a class="dropdown-item" href="{{ route('reportes.planeacion.ingresos_propios') }}">Ingresos Propios</a>
                        <a class="dropdown-item" href="{{ route('consultas.instructor') }}">Instructores Asignados</a>
                        <a class="dropdown-item" href="{{ route('consultas.instructores.disponibles') }}">Instructores Disponibles</a>
                        <a class="dropdown-item" href="{{ route('consultas.poa') }}">POA&Autorizados</a>
                    </div>
                </li>
                <li class="nav-item g-mx-5--lg dropdown">
                    <a class="nav-link g-color-2025--hover" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Estadísticas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('estadisticas.ecursos') }}">Cursos Impartidos</a>
                        <a class="dropdown-item" href="{{ route('tablero.metas.index') }}">Tablero de Control</a>
                    </div>
                </li>
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
