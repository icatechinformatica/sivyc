<div class="g-brd-around  g-brd-1 g-brd-gray-light-v4-top g-brd-gray-light-v4-bottom g-bg-color-menu g-pt-4 g-pb-4" role="alert">
	<div class="container">
		<nav class="navbar navbar-expand-lg navbar-light g-pa-0 g-right-0 g-bg-color-menu g-pt-4 g-pb-4">
			<a class="navbar-brand g-color-morado g-font-weight-600 g-font-size-16 d-none d-sm-none d-md-none d-lg-none d-xl-block g-text-underline--hover" >SIVyC</a>
			<a class="navbar-brand g-color-morado g-font-weight-600 g-font-size-16 d-sm-block d-md-block d-lg-block d-xl-none g-text-underline--hover" ></a>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav g-font-size-15 g-font-weight-100 ml-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Inicio de Sesión') }}</a>
                        </li>
                    @else
                        <li class="nav-item g-mx-5--lg"><a class="nav-link g-color-white--hover g-bg-morado--hover g-rounded-3 g-color-black-opacity-0_9" href="{{ route('cv_inicio') }}">Cursos Validados</a></li>
                        <li class="nav-item g-mx-5--lg"><a class="nav-link g-color-white--hover g-bg-morado--hover g-rounded-3 g-color-black-opacity-0_9" href="{{route('supre-inicio')}}">Suficencia Presupuestal</a></li>

                        <li class="nav-item g-mx-5--lg"><a class="nav-link g-color-white--hover g-bg-morado--hover g-rounded-3 g-color-black-opacity-0_9" href="{{route('contrato-inicio')}}">Contrato</a></li>
                        <!--helper-->
                        <!--ARROBAcan('home.index')-->
                            <li class="nav-item g-mx-5--lg"><a class="nav-link g-color-white--hover g-bg-morado--hover g-rounded-3 g-color-black-opacity-0_9" href="{{route('pago-inicio')}}">Pagos</a></li>
                        <!--ARROBAendcan-->
                        <!--end helper-->
                        <li class="nav-item g-mx-5--lg"><a class="nav-link g-color-white--hover g-bg-morado--hover g-rounded-3 g-color-black-opacity-0_9" >Agenda Vinculador</a></li>
                        <li class="nav-item g-mx-5--lg dropdown">
                            <a class="nav-link g-color-white--hover g-bg-morado--hover g-rounded-3 g-color-black-opacity-0_9 dropdown" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Catálogos
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{route('frm-cursos')}}">Cursos</a>
                            <a class="dropdown-item" href="{{route('instructor-inicio')}}">Instructor</a>
                            @can('home.index')
                            <a class="dropdown-item" href="{{ route('alumnos.index') }}">Alumnos</a>
                            @endcan
                            <a class="dropdown-item" href="{{route('convenio.create')}}">Convenios</a>
                            </div>
                        </li>
                        <li class="nav-item g-mx-5--lg dropdown">
                            <a class="nav-link g-color-white--hover g-bg-morado--hover g-rounded-3 g-color-black-opacity-0_9 dropdown" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Reportes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="#">Reporte 1</a>
                            <a class="dropdown-item" href="#">Reporte 2</a>
                            <a class="dropdown-item" href="#">SReporte 3</a>
                            </div>
                        </li>
                        <li class="nav-item g-mx-5--lg dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
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
                    @endguest
                </ul>
			</div>
		</nav>
	</div>
</div>
