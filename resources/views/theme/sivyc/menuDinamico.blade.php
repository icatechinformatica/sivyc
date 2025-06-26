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
            @foreach($menuDinamico as $main)
            @if($main['permiso'])
            <li class="nav-item g-mx-5--lg dropdown">
                <a class="nav-link g-color-2025--hover" href="#" id="menu{{$main['permiso']->id}}"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $main['permiso']->nombre }}
                </a>
                @if(count($main['submenus']))
                <div class="dropdown-menu" aria-labelledby="menu{{$main['permiso']->id}}">
                    @foreach($main['submenus'] as $sub)
                    @if($sub['permiso'])
                    @if(count($sub['submenus']))
                    <div class="dropdown-submenu">
                        <a class="dropdown-item dropdown-toggle" href="#" id="submenu{{$sub['permiso']->id}}">
                            {{ $sub['permiso']->nombre }}
                        </a>
                        <ul class="dropdown-menu">
                            @foreach($sub['submenus'] as $subsub)
                            @if($subsub['permiso'])
                            <li>
                                <a class="dropdown-item"
                                    href="{{ $subsub['permiso']->ruta_corta ? route($subsub['permiso']->ruta_corta) : '#' }}">
                                    {{ $subsub['permiso']->nombre }}
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <a class="dropdown-item"
                        href="{{ $sub['permiso']->ruta_corta ? route($sub['permiso']->ruta_corta) : '#' }}">
                        {{ $sub['permiso']->nombre }}
                    </a>
                    @endif
                    @endif
                    @endforeach
                </div>
                @endif
            </li>
            @endif
            @endforeach
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
                        <br><span class="float-right text-muted text-sm">{{ $cadwell->created_at->diffForHumans()
                            }}</span>
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
                        {{ Auth::user()->nombre }}
                    </a>
                    <!--can('password.update')-->
                    <a class="dropdown-item" href="{{ route('password.view') }}">Cambiar Contraseña</a>
                    <!--endcan-->
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

<style>
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu>.dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -1px;
    }

    .dropdown-submenu>a:after {
        content: "▶";
        float: right;
        margin-left: 5px;
        font-size: 0.7em;
    }
</style>