<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        .form-check-input{
            width:22px;
            height:22px;
        }
        .efirma {
            margin:5px 15px;
            height:22px;
            font-weight: bold;
        }

        /* Estilo del loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
            z-index: 9999; /* Asegura que esté por encima de otros elementos */
            display: none; /* Ocultar inicialmente */
        }

        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 6px solid #fff;
            border-top: 6px solid #621132;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {transform: translate(-50%, -50%) rotate(0deg);}
            100% {transform: translate(-50%, -50%) rotate(360deg);}
        }

        #loader-text {
            color: #fff;
            margin-top: 150px;
            text-align: center;
            font-size: 20px;
        }

        /* Texto loader */
        #loader-text span {
            opacity: 0; /* Inicia los puntos como invisibles */
            font-size: 30px;
            font-weight: bold;
            animation: fadeIn 1s infinite; /* Aplica la animación de aparecer */
        }

        @keyframes fadeIn {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }

        #loader-text span:nth-child(1) {animation-delay: 0.5s; }
        #loader-text span:nth-child(2) {animation-delay: 1s; }
        #loader-text span:nth-child(3) {animation-delay: 1.5s;}
    </style>
@endsection
@section('title', 'Reportes | SIVyC Icatech')
@section('content')
    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
        <div id="loader-text">
            Espere un momento mientras se realiza el proceso<span> . </span><span> . </span><span> . </span>
        </div>
    </div>

    <div class="card-header">
        Asignación de Folios
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if(!empty($messages))
            <div class="row">
                <div class="col-md-12">
                    @foreach($messages as $msg)
                        <div class="alert alert-success">
                            <p>{{ $msg }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @if ($msn = Session::get('msn'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ $msn }}</strong>
            </div>
        @endif
        @php
            if(isset($curso)) $clave = $curso->clave;
            else $clave = null;
        @endphp
        {{ Form::open(['route' => 'grupos.asignarfolios', 'method' => 'post', 'id'=>'frm']) }}

         <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('clave', $clave, ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'CLAVE DEL CURSO', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 30]) }}
            </div>
             <div class="form-group col-md-2">
                    {{ Form::text('matricula', $matricula, ['id'=>'matricula', 'class' => 'form-control', 'placeholder' => 'MATRICULA', 'aria-label' => 'MATRICULA', 'size' => 15]) }}
            </div>
            @can('casilla.efirma.asignacion.folios')
                <div class="form-group col-md-2">
                    {{ Form::checkbox('efirma', true, $efirma, ['id' => 'efirma', 'class' => 'form-control form-check-input']) }}
                    <label for="efirma" class="efirma"> EFIRMA</label>
                </div>
            @endcan
            <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['class' => 'btn', 'type' => 'submit']) }}
            </div>

        </div>
       @if(isset($curso))
            @if(count($acta)>0)
                <h5>{{count($acta)}} ACTA(S) DISPONIBLE(S)</h5>
                @foreach($acta as $a)
                    <div class="row bg-light" style="padding-top:8px; margin-bottom: 2px ;">
                        <div class="form-group col-md-2">ID: <b>{{ str_pad ($a->id, 8, 0, STR_PAD_LEFT)}}</b></div>
                        <div class="form-group col-md-2">MOD: <b>{{ $a->mod }}</b></div>
                        <div class="form-group col-md-2">NUM. ACTA: <b>{{ $a->num_acta }}</b></div>
                        <div class="form-group col-md-2">FECHA ACTA: <b>{{ date('d/m/Y', strtotime($a->facta)) }}</b></div>
                        <div class="form-group col-md-2">FOLIO INICIAL: <b>{{ $a->finicial }}</b></div>
                        <div class="form-group col-md-2">FOLIO FINAL: <b>{{ $a->ffinal }}</b></div>
                        <div class="form-group col-md-2">DISPONIBLE: <b>{{ $a->folio_disponible }}</b></div>
                        <div class="form-group col-md-2">TOTAL DISPONIBLES: <b>{{ $a->total-$a->contador }}</b></div>

                    </div>
                    <?php $actas[$a->id] =  str_pad ($a->id, 8, 0, STR_PAD_LEFT); ?>
                @endforeach
            @endif
        <br />
        <h5>DATOS DEL CURSO</h5>
        <div class="row bg-light" style="padding:8px">
            <div class="form-group col-md-3">
                UNIDAD/ACCIÓN MÓVIL: <b>{{ $curso->unidad }}</b>
            </div>
            <div class="form-group col-md-5">
                CURSO: <b>{{ $curso->id }} {{ $curso->curso }}</b>
            </div>
            <div class="form-group col-md-3">
                INSTRUCTOR: <b>{{ $curso->nombre }}</b>
            </div>
            <div class="form-group col-md-3">
                &Aacute;REA: <b>{{ $curso->area }}</b>
            </div>
            <div class="form-group col-md-5">
                ESPECIALIDAD: <b>{{ $curso->espe }}</b>
            </div>

            <div class="form-group col-md-3">
                FECHAS DEL <b> {{ $curso->inicio }}</b> AL <b>{{ $curso->termino }}</b>
            </div>
            <div class="form-group col-md-3">
                HORARIO: <b>{{ $curso->hini }} A {{ $curso->hfin }}</b>
            </div>
            <div class="form-group col-md-2">
                DURACI&Oacute;N: <b>{{ $curso->dura }} hrs.</b>
            </div>
            <div class="form-group col-md-3">
                CICLO: <b>{{ $curso->ciclo}}</b>
            </div>
            <div class="form-group col-md-2">
                MODALIDAD: <b>{{ $curso->mod}}</b>
            </div>

        </div>
        <h5>ALUMNOS</h5>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" >#</th>
                            <th scope="col" >MATR&Iacute;CULA</th>
                            <th scope="col">ALUMNOS</th>
                            <th scope="col" class="text-center" width="10%">CALIFICACI&Oacute;N</th>
                            <th scope="col" class="text-center" width="10%">FOLIO</th>
                            <th scope="col" class="text-center" width="10%">ESTATUS</th>
                            <th scope="col" class="text-center" width="10%">EXPEDICI&Oacute;N</th>
                            <th scope="col" class="text-center" width="10%">MOTIVO</th>
                        </tr>
                    </thead>
                    @if(isset($alumnos))
                    <tbody>
                        <?php $boton_asignar = false; $n=1;//con una asignaicion se activa ?>
                        @foreach($alumnos as $a)
                            <?php
                            $asignar = false;
                            if(($a->calificacion>5 AND !$a->folio) OR ( $a->movimiento=='CANCELADO'  AND $a->reexpedicion==false)){
                                if(count($alumnos)>0 AND isset($actas)) $boton_asignar = $asignar = true;
                            }
                            ?>
                            <tr>
                                <td> {{ $n++ }}</td>
                                <td> {{ $a->matricula }}</td>
                                <td> {{ $a->alumno }} </td>
                                <td class="text-center"> {{ $a->calificacion }} </td>
                                <td class="text-center"> {{ $a->folio }} </td>
                                 @if($asignar==true)
                                    <td class="text-center text-danger">@if($a->folio){{ "REASIGNAR" }}@else {{ "ASIGNAR" }}@endif </td>
                                 @else
                                    <td class="text-center"> {{ $a->movimiento}} </td>
                                 @endif
                                 <td class="text-center"> @if($a->fecha_expedicion){{ date('d/m/Y', strtotime($a->fecha_expedicion)) }}@endif  </td>
                                <td class="text-center"> {{ $a->motivo}} </td>
                            </tr>

                        @endforeach
                    </tbody>
                    @endif
                </table>
            </div>
        </div>
        {{-- botoneras --}}
        <div class="d-flex justify-content-end">
            <div class="row">
                @if (isset($alumnos))
                    @if($boton_asignar==true)
                        <div class="form-inline">
                            {{ Form::select('id_afolio', $actas, NULL ,['id'=>'id_afolio', 'class' => 'form-control mt-2']) }}
                            {{ Form::button('ASIGNAR FOLIOS', ['id' => 'guardar','class' => 'form-control btn']) }}
                        </div>
                    @endif
                    @if ($btn_genxml == true)
                        {{ Form::button('ENVIAR PARA EFIRMA', ['id' => 'generar_xml','class' => 'form-control btn']) }}
                    @endif
                @endif
            </div>
        </div>
        @endif
        {!! Form::close() !!}
    </div>
    @section('script_content_js')
        <script language="javascript">
             $(document).ready(function(){
                $("#guardar" ).click(function(){
                    if(confirm("Esta seguro de ejecutar la acción?")==true){
                        $('#frm').attr('action', "{{route('grupos.asignarfolios.guardar')}}"); $('#frm').submit();
                    }
                });

                $("#generar_xml" ).click(function(){
                    if(confirm("Esta seguro de ejecutar la acción para efirma?")==true){
                        loader('show');
                        $('#frm').attr('action', "{{route('grupos.asignarfolios.efolios')}}"); $('#frm').submit();
                    }
                });
            });

            function loader(make) {
                if(make == 'hide') make = 'none';
                if(make == 'show') make = 'block';
                document.getElementById('loader-overlay').style.display = make;
            }
        </script>
    @endsection
@endsection
