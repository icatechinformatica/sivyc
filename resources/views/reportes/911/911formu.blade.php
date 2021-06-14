
<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
    <div class="card-header">
        Reporte formato 911

    </div>
    <div class="card card-body" >
        <br />
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <br>
                    <form action="{{route('contacto')}}" method="POST" id="cacahuate" target="_blank">
                        <div class="form-group">
                            <label for="unidades">Unidad</label>
                            <select name="unidades" class="form-control" placeholder=" " id="unidades">
                                <option value=0 selected disabled="">Selecciona una opción</option>
                                @if($tipo=='string')
                                <option>{{$unidades}}</option>
                                @else
                                @foreach($unidades as $unidad)
                                <option>{{$unidad}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="turno">Turno</label>
                            <select name="turno" class="form-control" placeholder=" " id="turno">
                                <option selected disabled="">Selecciona una opción</option>
                                <option>MATUTINO</option>
                                <option>VESPERTINO</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Fecha de inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" id="fecha_inicio">
                        </div>
                        <div class="form-group">
                            <label for="">Fecha de termino</label>
                            <input type="date" name="fecha_termino" class="form-control" id="fecha_termino">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Generar PDF" class="btn">
                        </div>
                        {{csrf_field()}}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
<script language="javascript">
    $( function() {
        $('#cacahuate').validate({
            rules: {
                unidades: { required: true },
                turno: {required: true },
                fecha_inicio: {required: true },
                fecha_termino: {required: true }
            },
            messages: {
                unidades: { required: 'Por favor ingrese la unidad' },
                turno: { required: 'Por favor ingrese el turno' },
                fecha_inicio: { required: 'Por favor ingrese la fecha de inicio' },
                fecha_termino: { required: 'Por favor ingrese la fecha de termino' }
            }
        });
    });
</script>
@endsection
