<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Supervisión de Instructores | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <div class="card-header">
        Supervisi&oacute;n Escolar {{ $fecha }}
    </div>
    <div class="card card-body">
        @if ($message = Session::get('success'))
            <div class="row">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <div class="row">

                {{ Form::open(['route' => 'supervision.escolar', 'method' => 'post', 'class' => 'form-inline', 'enctype' => 'multipart/form-data' ]) }}
                    {{ Form::date('fecha', null , ['class' => 'form-control datepicker mr-sm-1', 'placeholder' => 'FECHA', 'readonly' =>'readonly']) }}
                    {{ Form::select('tipo_busqueda', array( 'nombre_instructor' => 'INSTRUCTOR','clave_curso' => 'CLAVE CURSO', 'nombre_curso' => 'CURSO' ), null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    {{ Form::text('valor_busqueda', null, ['class' => 'form-control mr-sm-6', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) }}
                    {{ Form::button('FILTRAR', array('class' => 'btn', 'type' => 'submit')) }}
                {!! Form::close() !!}


        </div>
        <div class="row">
            @include('supervision.escolar.table')
        </div>
    </div>
    <br>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset('js/supervisiones/datepicker.js') }}"></script>
    @section('script_content_js')

        <script src="{{asset("js/supervisiones/input-case-enforcer.min.js")}}"></script>
        <script type="text/javascript">
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#textURL').caseEnforcer('lowercase');
            
            
            function generarURL(id, tipo) {  ///GENERAR URL O BORRAR URL CADUCADA              
                if(confirm("Est\u00E1 seguro de ejecutar la acci\u00F3n?")==true){
                    var params = {id : id, tipo: tipo, _token:"{{csrf_token()}}"};
                    $.ajax({
                        url: "{{ route('supervision.escolar.url.generar') }}",
                        data: params,
                        type:  'GET',
                        dataType : 'text',
                        success:  function (response) {
                            if(response == "CADUCADA"){
                                alert("URL caducada")
                            }else{
                                $('#textURL').val(response);
                                $('#modalURL').modal('show');
                            }
                        },
                        statusCode: {
                            404: function() {
                               alert('Página no encontrada');
                            }
                        },
                        error:function(x,xs,xt){
                            //window.open(JSON.stringify(x));
                            alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                        }
                    });
                } else{
                    $('#textURL').val("OPERACI\u00D3N CANCELADA");
                }
            }

            function generarListaAlumnos(id) {
                $.ajax({
                    data: {id : id, _token:"{{csrf_token()}}"},
                    url: "{{route('supervision.alumno.lst')}}",
                    type:  'GET',
                    dataType : 'text',
                    success:  function (response) {
                            $('#group').html(response);
                    },
                    error:function(x,xs,xt){
                        //window.open(JSON.stringify(x));
                        alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                    }
                });
            }
        
        </script>
    @endsection
@endsection
