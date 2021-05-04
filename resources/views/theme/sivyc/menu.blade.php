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
                        @can('cursos.index')
                             <a class="dropdown-item" href="{{route('curso-inicio')}}">Cursos</a>
                        @endcan
                        <a class="dropdown-item" href="{{route('instructor-inicio')}}">Instructor</a>
                        <!--alumnos.index-->
                        @can('alumnos.index')
                            <a class="dropdown-item" href="{{ route('alumnos.index') }}">Aspirantes</a>
                        @endcan
                        @can('alumnos.inscritos.index')
                            <a class="dropdown-item" href="{{ route('alumnos.inscritos') }}">Alumnos</a>
                        @endcan
                        <a class="dropdown-item" href="{{route('convenios.index')}}">Convenios</a>
                        <a class="dropdown-item" href="{{route('cerss.inicio')}}">CERSS</a>
                        @can('areas.inicio')
                            <a class="dropdown-item" href="{{route('areas.inicio')}}">Áreas</a>
                        @endcan
                        @can('especialidades.inicio')
                            <a class="dropdown-item" href="{{route('especialidades.inicio')}}">Especialidades</a>
                        @endcan
                        @can('unidades.index')
                            <a class="dropdown-item" href="{{route('unidades.inicio')}}">Unidades</a>
                        @endcan
                        <a class="dropdown-item" href="{{route('exoneraciones.inicio')}}">Exoneraciones</a>
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
                        {{-- <a class="dropdown-item" href="{{route('reportes.formatoT')}}">REPORTE DE FORMATO T</a>                         --}}
                        @can('academicos.arc')
                            <a class="dropdown-item" href="{{route('reportes.vista_arc')}}">APERTURA</a>
                        @endcan
                        @can('reportes.cursos')
                            <a class="dropdown-item" href="{{route('reportes.cursos.index')}}">CURSOS AUTORIZADOS</a>
                        @endcan
                        @can('planeacion.reporte')
                            <a class="dropdown-item" href="{{route('planeacion.reporte')}}">PLANEACIÓN</a>
                        @endcan
                        @can('financieros.reporte')
                            <a class="dropdown-item" href="{{route('financieros.reporte')}}">ESTADO DE CONTRATOS Y PAGOS</a>
                        @endcan
                        {{-- <a class="dropdown-item" href="{{route('vista_formatot')}}">FORMATOT</a> --}}
                    </div>
                </li>

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
                                <a class="dropdown-item" href="{{route('grupos.consultas')}}">Consultas</a>                        
                            @endcan
                        </div>
                    </li> 
                @endcan
                {{-- grupo calificaciones end --}}
                
                @can('formatot.menu.indice')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a href="#" class="nav-link g-color-white--hover" id="navbarDropdownMenuLinkValidacion" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Formatos t
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkValidacion">
                            @can('vista.formatot.unidades.indice')
                                <a class="dropdown-item" href="{{route('vista_formatot')}}">Generación Formato T por Unidades</a> 
                            @endcan
                            @can('vista.validacion.enlaces.dta')
                                <a class="dropdown-item" href="{{ route('validacion.cursos.enviados.dta') }}">Revisión de Cursos Formato t</a>  
                            @endcan
                            @can('vista.validacion.direccion.dta')
                                <a class="dropdown-item" href="{{ route('validacion.dta.revision.cursos.indice') }}">Validación de Cursos Formato t DTA</a>
                            @endcan
                            @can('vista.validacion.direccion.dta')
                                <a class="dropdown-item" href="{{ route('indice.dta.aperturado.indice') }}" >Formato T Aperturado</a>
                            @endcan
                            @can('vista.revision.validacion.planeacion.indice')
                                <a class="dropdown-item" href="{{ route('planeacion.formatot.index') }}">Revisión y Validación Final Formato t</a>
                            @endcan
                        </div>
                    </li>
                @endcan
                
                {{-- modificaciones en el curso del menu --}}
                {{-- consultas folios asignados --}}
                @can('solicitudes.folios')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitudes
                        </a>                     
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
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
                        @can('consultas.cursosfinalizados')
                            <a class="dropdown-item" href="{{route('consultas.cursosfinalizados')}}">Cursos Finalizados</a>
                        @endcan
                    </div>                    
                </li> 
                {{-- consultas folios asignados END --}}
                
            </ul>
            <ul class="navbar-nav ml-auto nav-flex-icons">
                <li class="nav-item g-mx-5--lg">
                    <a class="nav-link">
                        Notificaciones <span class="badge badge-pill badge-primary ml-2">1</span>
                    </a>
                </li>
                <li class="nav-item avatar dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-55" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg-right dropdown-secondary" aria-labelledby="navbarDropdownMenuLink-55">
                        <a class="dropdown-item" href="#">
                            {{ Auth::user()->name }}
                        </a>
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