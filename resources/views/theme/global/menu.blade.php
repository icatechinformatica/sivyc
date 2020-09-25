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
              <a class="nav-link active" href="#">
                <i class="ni ni-tv-2 text-primary"></i>
                <span class="nav-link-text">TABLERO DE CONTROL</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('tablero.metas.index') }}">
                <i class="fa fa-list-alt text-red"></i>
                <span class="nav-link-text">METAS ANUALES</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('tablero.unidades.index') }}">
                <i class="fa fa-list-alt text-red"></i>
                <span class="nav-link-text">SITACI&Oacute;N ACTUAL</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('tablero.cursos.index') }}">
                <i class="fa fa-list-alt text-red"></i>
                <span class="nav-link-text">CURSOS APERT.</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
