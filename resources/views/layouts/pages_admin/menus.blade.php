<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Menus | Sivyc Icatech')
<!--contenido-->
@section('content')
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">MENUS</h3>
                        </div>
                        {{-- <div class="col-4 text-right">
                            <a href="{{ route('permisos.crear') }}" class="btn btn-sm btn-success">Nuevo permiso</a>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="tree-menu">
                        <h4>Árbol de Menús</h4>
                        <div class="tree-container">
                            <ul class="menu-tree">
                                @if(isset($menuTree) && count($menuTree) > 0)
                                @foreach($menuTree as $menuItems)
                                @include('layouts.pages_admin.menu_items', ['menu' => $menuItems, 'level' => 0])
                                @endforeach
                                @else
                                <li>No hay menús disponibles</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER PORTAL DE GOBIERNO -->
    @include("theme.sivyc_admin.footer")
    <!-- FOOTER PORTAL DE GOBIERNO END-->
</div>
@endsection

@section('styles')
<style>
    .tree-level-0 {
        font-weight: bold;
    }

    .tree-level-1 {
        padding-left: 20px;
    }

    .tree-level-2 {
        padding-left: 40px;
    }

    .tree-level-3 {
        padding-left: 60px;
    }

    .tree-level-4 {
        padding-left: 80px;
    }

    .tree-level-5 {
        padding-left: 100px;
    }
</style>
@endsection

@section('scripts_content')
<script>
    $(document).ready(function() {
        document.querySelectorAll('.status').forEach(function(statusElement) {
            statusElement.addEventListener('click', function() {
                const menuId = this.getAttribute('data-id-menu');
                const currentStatus = this.classList.contains('badge-success') ? 'Activo' : 'Inactivo';
                const newStatus = currentStatus === 'Activo' ? 'Inactivo' : 'Activo';

                // Verificar que no se active un submenu si el padre está desactivado
                if (newStatus === 'Activo') {
                    // Recorrer ancestros y comprobar estado
                    var currentLi = this.closest('li');
                    var parentLi = currentLi.parentElement.closest('li');
                    while (parentLi) {
                        var parentStatus = parentLi.querySelector('.status');
                        if (parentStatus && parentStatus.classList.contains('badge-danger')) {
                            var parentName = parentLi.querySelector('.menu-name') ? parentLi.querySelector('.menu-name').textContent.trim() : 'el menú padre';
                            // Obtener el nombre del menú clicado
                            var clickedName = currentLi.querySelector('.menu-name').textContent.trim();
                            alert('No se puede activar "' + clickedName + '" porque "' + parentName + '" está inactivo.');
                            return;
                        }
                        parentLi = parentLi.parentElement.closest('li');
                    }
                }
                // Aquí puedes hacer una llamada AJAX para actualizar el estado en el servidor
                // Por ejemplo:
                $.ajax({
                    url: `/menus/${menuId}/status-update`,
                    method: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Actualizar estado de todos los elementos afectados (padre y submenus)
                            (response.updatedIds || []).forEach(function(id) {
                                var el = document.getElementById(`status-${id}`);
                                if (el) {
                                    el.classList.toggle('badge-success');
                                    el.classList.toggle('badge-danger');
                                    el.textContent = newStatus;
                                }
                            });
                        } else {
                            alert('Error al cambiar el estado del menú.');
                        }
                    }
                });
                // console.log('Status toggle clicked for menu ID:', this.getAttribute('data-id-menu'));
            });
        });
        // Solo un formulario open a la vez
        $(document).on('show.bs.collapse', '[id^="add-form-"]', function() {
            $('[id^="add-form-"]').not(this).collapse('hide');
        });
    });
</script>
@endsection