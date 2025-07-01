<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'ASIGNAR ROL A USUARIO | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">

            <div class="col-xl-12 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">EDITAR PERMISO</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{ route('usuario_permisos.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form action="{{ route('usuarios_permisos.rol.edit', ['id' => base64_encode($usuario->id) ]) }}" id="formUsuarioAsignarRol" name="formUsuarioAsignarRol" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="numero_control_edit" class="control-label">USUARIO</label>
                                <input type="text" name="numero_control_edit" id="numero_control_edit" class="form-control" value="{{ $usuario->name }}" readonly placeholder="NÚMERO DE CONTROL PARA MODIFICAR">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="codigo_verificacion_edit" class="control-label">ROL</label>
                                <select class="form-control" id="inputRol" name="inputRol">
                                    <option value="">--SELECCIONAR--</option>
                                    @foreach ($roles as $itemRol)
                                        <option
                                            @foreach ($usuario->roles as $itemUserRol)
                                                {{ ($itemUserRol->pivot->rol_id == $itemRol->id) ? 'selected' : '' }}
                                            @endforeach
                                            value="{{ $itemRol->id }}">{{ $itemRol->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success" >Asignar</button>
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
@section('scripts_content')
    <script type="text/javascript">
        $(function(){

            $('#formUsuarioAsignarRol').validate({
                rules: {
                    inputRol: {
                        required: true,
                    }
                },
                messages: {
                    inputRol: {
                        required: 'Por favor, seleccione un rol',
                    }
                }
            });

        });
    </script>
@endsection
