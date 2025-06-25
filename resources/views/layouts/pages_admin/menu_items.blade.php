<li class="tree-level-{{$level}} mb-2">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            @if(isset($menu['submenu']) && count($menu['submenu']) > 0)
            <button class="btn btn-sm btn-link p-0 mr-2" data-toggle="collapse"
                data-target="#submenu-{{ $menu['clave_orden'] }}">
                <i class="fas fa-chevron-right"></i>
            </button>
            @else
            <span class="d-inline-block mr-3"></span>
            @endif
            <div data-toggle="collapse" data-target="#submenu-{{ $menu['clave_orden'] }}" style="cursor: pointer;">
                <strong class="menu-name">{{ $menu['nombre'] }}</strong>
                <small class="text-muted">({{ $menu['ruta_corta'] }})</small>
                <small class="text-muted mr-3">({{ $menu['clave_orden'] }})</small>
                {{-- <div><small>{{ $menu['description'] }}</small></div> --}}
            </div>
        </div>
        <div class="d-flex align-items-center">
            <span id="status-{{ $menu['id'] }}" style="cursor: pointer;No"
                class="status badge badge-{{ $menu['activo'] ? 'success' : 'danger' }} mr-2"
                data-id-menu="{{ $menu['id'] }}">
                {{ $menu['activo'] ? 'Activo' : 'Inactivo' }}
            </span>
        </div>
    </div>
    @if(isset($menu['submenu']) && count($menu['submenu']) > 0)
    <ul id="submenu-{{ $menu['clave_orden'] }}" class="collapse">
        @foreach($menu['submenu'] as $child)
        @include('layouts.pages_admin.menu_items', ['menu' => $child, 'level' => $level + 1])
        @endforeach
    </ul>
    @endif
    @if ($level < 2) 
        
    <ul>
        <li class="tree-level-{{ $level + 1 }} mb-1">
            <button class="btn btn-link p-0 text-muted small" data-toggle="collapse"
                data-target="#add-form-{{ $menu['id'] }}">
                <i class="fas fa-plus"></i>
                <span class="text-sm">Añadir</span>
            </button>
            <div id="add-form-{{ $menu['id'] }}" class="collapse mt-2">
                <form method="POST" action="{{ route('menus.store') }}">
                    @csrf
                    <input type="hidden" name="menu" value="1">
                    <input type="hidden" name="activo" value="1">
                    <input type="hidden" name="clave_orden_padre" value="{{ $menu['clave_orden'] }}">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="permisoName" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="permisoSlug" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="permisoDescripcion" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                </form>
            </div>
        </li>
    </ul>
    @endif
</li>