<!--Creado por Julio Alcaraz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'APERTURAS | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .content {
            padding: 0 20px 20px 17px;
            margin-top: 0;
        }

        @media (min-width: 1200px) {
            .container {
                width: 1400px;
            }
        }

    </style>
@endsection
<!--seccion-->
@section('content')

    <div class="container g-pt-40 content">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        {{-- información sobre la entrega del formato t para unidades --}}
        <div class="alert alert-info" role="alert">
            <b>LA FECHA LÍMITE DEL MES DE PARA EL ENVÍO DEL FORMATO T CORRESPONDIENTE ES EL 
                <strong>{{$fechaEntregaFormatoT}}</strong> FALTAN
                <strong>{{ $diasParaEntrega }}</strong> DÍAS.
            </b>
        </div>
        {{-- información sobre la entrega del formato t para unidades END --}}

        <div class="row">
            <div class="col-lg-8 margin-tb">
                <div>
                    <h3><b>REPORTE DE CURSOS APERTURADOS</b></h3>
                </div>
            </div>
        </div>

        <hr style="border-color:dimgray">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    {{ Form::open(['route' => 'generar.reporte.apertura', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            {{ Form::text('fechainicio', null, ['class' => 'form-control  mr-sm-1', 'placeholder' => 'FECHA DE INICIO', 'id' => 'fecha_ini', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-4">
                            {{ Form::text('fechatermino', null, ['class' => 'form-control  mr-sm-1', 'placeholder' => 'FECHA DE TERMINO', 'id' => 'fecha_termino', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-4">
                            <button input type="submit"
                                class="btn btn-outline-success my-2 my-sm-0 waves-effect waves-light">
                                <i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i>&nbsp;
                                GENERAR REPORTE
                            </button>
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <br>
    </div>

@endsection
@section('script_content_js')
    <script>
        $(function() {
            $("#fecha_ini").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd'
            });

            $('#fecha_termino').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd'
            });
        });

    </script>
@endsection
