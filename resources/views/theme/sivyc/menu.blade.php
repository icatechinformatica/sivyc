
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
                        @can('reportes.cursos')
                            <a class="dropdown-item" href="{{route('reportes.cursos.index')}}">CURSOS AUTORIZADOS</a>
                        @endcan
                        <a class="dropdown-item" href="{{route('planeacion.reporte')}}">Planeación</a>
                    </div>
                </li>
                <!--PESTAÑA SOLICITUDES DA-->
                @can('supervision.escolar')
                    <li class="nav-item g-mx-5--lg dropdown">
                        <a class="nav-link g-color-white--hover" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Solicitudes DA
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            {{-- @can('supervision.escolar') --}}
                            <a class="dropdown-item" href="{{route('solicitudesDA.inicio')}}">Modificación de cursos</a>
                            {{-- @endcan --}}
                            <a class="dropdown-item" href="{{route('supervision.escolar')}}">Solicitud de apoyo</a>
                        </div>
                    </li>
                @endcan
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
