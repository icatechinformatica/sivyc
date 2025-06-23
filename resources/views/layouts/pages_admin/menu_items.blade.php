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
            <div>
                <strong class="menu-name">{{ $menu['name'] }}</strong>
                <small class="text-muted">({{ $menu['slug'] }})</small>
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
    <ul>
        <li class="tree-level-{{ $level + 1 }} mb-1">
            <a href="{{ route('permisos.crear', ['parent' => $menu['id']]) }}" class="text-muted small p-0"
                title="Añadir menú">
                <i class="fas fa-plus"></i>
                <span class="text-sm">Añadir</span>
            </a>
        </li>
    </ul>
</li>