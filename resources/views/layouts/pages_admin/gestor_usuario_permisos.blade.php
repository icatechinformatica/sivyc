<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'ASIGNAR PERMISO | Sivyc Icatech')
<!--contenido-->
@section('content')
<div class="container-fluid mt--6">
    <div class="row">

        <div class="col-xl-12 order-xl-1">
            <div class="card">
                <div class="card-header">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br />
                    @endif
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">GESTOR DE PERMISOS A USUARIO - {{ $usuario->id }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $permisosPorRol = $usuario->roles->flatMap(function($rol) {
                            return ($rol->permisos ?? collect())->pluck('id');
                        })->unique()->toArray();
                    @endphp
                    <form method="POST" action="">
                        @csrf
                        <h6 class="heading-small text-muted mb-4">Información del usuario</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                @foreach ($permisos->chunk(ceil($permisos->count() / 2)) as $colPermisos)
                                <div class="col-lg-6">
                                    @foreach ($colPermisos as $itemPermisos)
                                    @php
                                    $checked = $usuario->permissions->contains('id', $itemPermisos->id) ? 'checked' :
                                    '';
                                    $disabled = in_array($itemPermisos->id, $permisosPorRol) ? 'disabled' : '';
                                    @endphp
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input class="custom-control-input" id="permiso_{{ $itemPermisos->id }}"
                                            name="permisos[]" type="checkbox" value="{{ $itemPermisos->id }}" {{
                                            $checked }} {{ $disabled }}>
                                        <label class="custom-control-label" for="permiso_{{ $itemPermisos->id }}">
                                            <span class="text-muted">{{ $itemPermisos->nombre }}
                                                @if($disabled)
                                                <small class="text-info">(asignado por rol)</small>
                                                @endif
                                            </span>
                                        </label>
                                        @if($disabled)
                                        <input type="hidden" name="permisos[]" value="{{ $itemPermisos->id }}">
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <br>
                        <input type="submit" value="Asignar" class="btn btn-sm btn-success">
                        <input type="hidden" name="idusuario" id="idusuario" value="{{ $usuario->id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER PORTAL DE GOBIERNO -->
    @include("theme.sivyc_admin.footer")
    <!-- FOOTER PORTAL DE GOBIERNO END-->
</div>
@endsection


<style>
    .custom-control-input:disabled~.custom-control-label::before {
        background-color: #b3c6f7 !important;
        opacity: 1 !important;
        border-color: #5e72e4 !important;
        filter: grayscale(30%) brightness(1.2);
        cursor: not-allowed !important;
    }

    .custom-control-input:disabled:checked~.custom-control-label::before {
        background-color: #5e72e4 !important;
        border-color: #5e72e4 !important;
        opacity: 0.6 !important;
    }

    .custom-control-input:disabled:checked~.custom-control-label::after {
        opacity: 0.8 !important;
    }
</style>