@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
    <div class="card-header">
        Reporte Formato RF-001

    </div>
    <div class="card card-body" >
        <br />
        <div class="container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{$message}}</p>
                </div>
            @endif
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <br>
                    <form action="{{route('reportes.concentradoingresospdf')}}" method="POST" id="cacahuate" target="_blank">
                        <div class="form-group">
                            <label for="unidades">Unidad</label>
                            @if($roluser->name == 'Delegado Administrativo')
                                <input type="text" class="form-control" name="unidades" id="unidades" readonly value="{{$unidaduser->ubicacion}}">
                            @else
                                <select name="unidades" class="form-control" placeholder=" " id="unidades">
                                    <option value=0 selected disabled="">Selecciona una opción</option>
                                    @foreach($unidades as $unidad)
                                        <option value='{{$unidad->ubicacion}}'>{{$unidad->ubicacion}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        {{-- <div class="form-group">
                            <label for="turno">Tipo de Reporte</label>
                            <select name="tipo" class="form-control" placeholder=" " id="tipo">
                                <option selected disabled="">Selecciona una opción</option>
                                <option value="pago_curso">PAGO DE CURSO</option>
                                <option value="factura">FACTURA</option>
                            </select>
                        </div> --}}
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
                tipo: {required: true },
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
