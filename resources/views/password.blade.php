@extends('theme.sivyc.layout')
@section('title', 'Password | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
    <div class="card-header">
        Actualizar Contraseña

    </div>
    <div class="card card-body" >
        <br />
        <div class="container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{$message}}</p>
                </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <br>
                    <form action="{{route('update.password')}}" method="POST" id="cacahuate">
                        <div class="form-group">
                            <label for="contraseña">Contraseña</label>
                            <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="nuevaContraseña">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="nuevaContraseña" name="nuevaContraseña" placeholder="New Password">
                        </div>
                        <div class="form-group">
                            <label for="confi_nuv_contraseña">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confi_nuv_contraseña" name="confi_nuv_contraseña" placeholder="New Password">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="MODIFICAR" class="btn btn-warning">
                        </div>
                        {{csrf_field()}}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
<script type="text/javascript">
    $( function() {
        $('#cacahuate').validate({
            rules:{
                contraseña:{
                    required:true,
                    minlength:6
                },
                nuevaContraseña:{
                    required:true,
                    minlength:6
                },
                confi_nuv_contraseña:{
                    required:true,
                    EQUAL:true,
                    minlength:6
                }
            },
            messages:{
                contraseña:{
                   required:'Por favor Ingresé la contraseña',
                   minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
                },
                nuevaContraseña:{
                    required:'Por favor Ingresé la contraseña',
                    minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
                },
                confi_nuv_contraseña:{
                    required:'Por favor Ingresé la contraseña',
                    EQUAL: 'Sin coincidencia',
                    minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
                }
            }
        });
    });
</script>
@endsection
