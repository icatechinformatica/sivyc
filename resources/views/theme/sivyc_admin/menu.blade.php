<nav class="sidenav navbar navbar-vertical  fixed-left  navbar-expand-xs navbar-light bg-white" id="sidenav-main">
    <div class="scrollbar-inner">
      <!-- Brand -->
      <div class="sidenav-header  align-items-center">
        <a class="navbar-brand" href="javascript:void(0)">
          <img alt="sivyc" class="navbar-brand-img" src="{{asset("img/icatech-imagen.png")}}">
        </a>
      </div>
      <div class="navbar-inner">
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
          <!-- Nav items -->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" href="dashboard.html">
                <i class="ni ni-tv-2 text-primary"></i>
                <span class="nav-link-text">INICIO</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="icons.html">
                <i class="fa fa-list-alt text-red"></i>
                <span class="nav-link-text">CURSOS</span>
              </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('usuario_permisos.index') }}" >
                    <i class="fa fa-users text-primary"></i>
                    <span class="nav-link-text">Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('roles.index') }}">
                    <i class="ni ni-ui-04 text-default"></i>
                    <span class="nav-link-text">Roles</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('permisos.index') }}" >
                    <i class="fa fa-braille text-default"></i>
                    <span class="nav-link-text">Permisos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('permisos.roles.index') }}" >
                    <i class="fa fa-link text-info"></i>
                    <span class="nav-link-text">permisos-roles</span>
                </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="map.html">
                <i class="ni ni-pin-3 text-primary"></i>
                <span class="nav-link-text">Google</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="profile.html">
                <i class="ni ni-single-02 text-yellow"></i>
                <span class="nav-link-text">Profile</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="tables.html">
                <i class="ni ni-bullet-list-67 text-default"></i>
                <span class="nav-link-text">Tables</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login.html">
                <i class="ni ni-key-25 text-info"></i>
                <span class="nav-link-text">Login</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
