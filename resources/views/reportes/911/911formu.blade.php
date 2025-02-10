@extends('theme.sivyc.layout')
@section('title', 'Reportes 911 | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" /> 

    @endsection
@section('content')       
    <div class="card-header">
        Reportes / Formato 911
    </div>
    <div class="card card-body" >        
        <div class="container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{$message}}</p>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-md-5">
                    <br>
                    <form action="{{route('contacto')}}" method="POST" id="cacahuate" target="_blank">
                        <div class="form-group">
                            <label for="unidades">UNIDAD DE CAPACITACIÓN / ACCIÓN MÓVIL</label>
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
                            <label for="turno">TURNO</label>
                            <select name="turno" class="form-control" placeholder=" " id="turno">
                                <option selected disabled="">Selecciona una opción</option>
                                <option>MATUTINO</option>
                                <option>VESPERTINO</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">FECHA DE INICIO</label>
                            <input type="date" name="fecha_inicio" class="form-control" id="fecha_inicio">
                        </div>
                        <div class="form-group">
                            <label for="">FECHA DE TERMINO</label>
                            <input type="date" name="fecha_termino" class="form-control" id="fecha_termino">
                        </div>
                        <div class="form-group" style="text-align: center;">
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
