<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Incorporación Laboral | SIVyC Icatech')
@section("content_script_css")
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
      
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
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }


    </style>
@endsection
@section('content')
    <div class="card-header">Consultas / Incorporación Laboral de Egresados</div>        
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>
    <div class="card card-body" >        
        @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <strong id="MSG">{{ $message }}</strong>
                </div>
        @endif
        {{ Form::open(['method' => 'post','id' =>'frmBolsaTrabajo', 'enctype' => 'multipart/form-data' ,'accept-charset'=>'UTF-8']) }}
            @csrf
            <div class="row form-inline ml-1">
                {{ Form::select('unidad', $unidades, $old['unidad'] ?? null ,['id'=>'unidad','class' => 'form-control mr-sm-2 mb-2','title' => 'UNIDADES','placeholder' => 'UNIDADES']) }}
                {{ Form::date('fechaIniV', $old['fechaIniV'] ?? null , ['id'=>'fechaIniV', 'class' => 'form-control datepicker mr-sm-2 mb-2 small', 'placeholder' => 'FECHA INICIAL', 'title' => 'FECHA INICIAL', 'required' => 'required']) }}
                {{ Form::date('fechaFinV', $old['fechaFinV'] ?? null , ['id'=>'fechaFinV', 'class' => 'form-control datepicker  mr-sm-2 mb-2', 'placeholder' => 'FECHA FINAL', 'title' => 'FECHA FINAL', 'required' => 'required']) }}
                {{ Form::text('text_buscar_curso', $old['text_buscar_curso'] ?? null, ['id'=>'text_buscar_curso', 'class' => 'form-control mr-sm-2 mb-2 text_buscar_curso', 'placeholder' => 'CURSO / ESPECIALIDAD', 'size' => 25]) }}                
                {{ Form::button('FILTRAR', ['id' => 'btnBuscar', 'name'=> 'boton', 'title' => 'FILTRAR', 'class' => 'btn mr-sm-2 mb-2']) }}
                {{ Form::button('LIMPIAR', ['id' => 'btnLimpiar', 'name'=> 'boton', 'title' => 'LIMPIAR CAJAS DE TEXTO', 'class' => 'btn mr-sm-2 btn-info mb-2']) }}                
                <button class="btn btn-warning text-dark mb-2" title="EXPORTAR EN FORMATO EXCEL" id="btnReporte">EXPORTAR <i class="far fa-file-excel fa-lg text-dark ml-1"></i></button>
            </div>
        {!! Form::close() !!}
        
        {{-- Tabla --}}
        
        @if (count($results) > 0)
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <h5>Total de registros: <span class="badge badge-dark">{{ $total_reg }}</span></h5>
                </div>
            </div>           
            <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center" scope="col">No</th>
                                <th class="text-center" scope="col">EJERCICIO</th>
                                <th class="text-center" scope="col">NOMBRE</th>
                                <th class="text-center" scope="col">EDAD</th>
                                <th class="text-center" scope="col">CURP</th>
                                <th class="text-center" scope="col">MUNICIPIO</th>
                                <th class="text-center" scope="col">NACIONALIDAD</th>
                                <th class="text-center" scope="col">SEXO</th>
                                <th class="text-center" scope="col">CIUDAD</th>
                                <th class="text-center" scope="col">DOMICILIO</th>
                                <th class="text-center" scope="col">EDO.CIVIL</th>
                                <th class="text-center" scope="col">ESCOLARIDAD</th>
                                <th class="text-center" scope="col">TELEFONO</th>
                                <th class="text-center" scope="col">CORREO</th>
                                <th class="text-center" scope="col">ESPECIALIDAD(ES)</th>
                                <th class="text-center" scope="col">CURSO(S)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $key => $item)
                                <tr style="text-center">
                                    <td class="text-center">{{ $key + 1}}</td>
                                    <td class="text-center">{{ $item->ejercicio }}</td>                                    
                                    <td><div style="width: 180px;">
                                        <a title="Incorporación Laboral" onclick="ver_modal('{{ $item->curp}}', '{{ $item->alumno}}' ,'{{$item->datos}}')" >
                                            <i class="fas fa-user-tie fa-lg mr-1 mb-2" aria-hidden="true" style="color:rgb(174, 25, 45);"></i>
                                        </a>    
                                        {{ $item->alumno }}
                                        <div class="mt-2 small"  id='{{$item->curp}}'>{!! $item->incorporadoa!!}</div>
                                    </div></td>
                                    <td class="text-center">{{ $item->edad }} AÑOS</td>
                                    <td><div style="width: 130px;">{{ $item->curp }}</div></td>
                                    <td class="text-center">{{ $item->municipio}}</td>
                                    <td class="text-center">{{ $item->nacionalidad}}</td>
                                    <td class="text-center">{{ $item->sexo}}</td>
                                    <td><div style="width: 150px;">{{ $item->colonia.', '.$item->municipio.', '.$item->estado}}</div></td>
                                    <td><div style="width: 150px;">{{ $item->domicilio.', '.$item->colonia}}</div></td>
                                    <td class="text-center">{{ $item->estado_civil}}</td>
                                    <td class="text-center">{{ $item->ultimo_grado_est}}</td>
                                    <td class="text-center">{{ $item->telefono ?? 'SIN NUMERO' }}</td>
                                    <td class="text-center">{{ $item->correo ?? 'SIN CORREO' }}</td>
                                    <td><div style="width: 300px;">{!!nl2br($item->especialidades)!!}</div></td>
                                    <td><div style="width: 350px;">{!!nl2br($item->grupos)!!}</div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    
                    <div class="row py-4">
                        <div class="col d-flex justify-content-center">
                            {{$results->appends(request()->query())->links()}}
                        </div>
                    </div>
                @else
                    <div class="alert alert-success mt-5 mb-5" role="alert">
                        <h4 class="alert-heading">¡Sin resultados en la busqueda!</h4>
                    </div>
                @endif

                </div>
                <div class="modal fade" id="modalIncorporar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lm modal-notify modal-danger" id="" role="document">
                    <div class="modal-content text-center">
                        <!--Header-->
                        <div class="modal-header d-flex justify-content-center" style="background-color:rgb(201, 1, 102);" >
                            <p class="heading">EGRESADO: <block id="head_title" class="font-weight-bold"> </block></p>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="text-light">&times;</span></button>
                        </div>
                        <!--Body-->
                        <div class="modal_body">
                                <div class="alert alert-dismissible fade show p-4 text-left" role="alert">
                                    {{ Form::hidden('curp', null,['id'=>'curp']) }}                                                                
                                    <h6 class="font-weight-bold">FECHA DE INCORPORACIÓN:</h6> {{ Form::date('fecha', null, ['id'=>'fecha', 'class' => 'form-control datepicker  mr-sm-4 mt-3 mb-3', 'placeholder' => 'FECHA']) }}
                                    <h6 class="font-weight-bold">EMPRESA:</h6>                                    
                                    {{ Form::textarea('empresa', null, ['id'=>'empresa', 'class' => 'form-control mt-2', 'placeholder' => 'NOMBRE DE LA EMPRESA', 'title' => 'NOMBRE DE LA EMPRESA','rows' => 3]) }}
                                    <h6 class="font-weight-bold">DIRECCIÓN:</h6>
                                    {{ Form::textarea('direccion', null, ['id'=>'direccion', 'class' => 'form-control mt-2 mb-3', 'placeholder' => 'DIRECCIÓN DE LA EMPRESA', 'rows' => 2]) }}
                                    <h6 class="font-weight-bold">PUESTO:</h6>
                                    {{ Form::textarea('puesto', null, ['id'=>'puesto', 'class' => 'form-control mt-2', 'placeholder' => 'Puesto.', 'rows' => 1]) }} 
                                    <div class="modal-footer flex-center">
                                        <button class="btn btn-danger" id="guardar" onclick="enviar()">GUARDAR</button>
                                        <a type="button" class="btn btn-outline-danger waves-effect" id="" data-dismiss="modal">
                                        <i class="fa fa-times fa-sm text-danger" aria-hidden="true"> </i>CANCELAR</a>
                                    </div>
                                </div>                            
                        </div>
                    </div>
                </div>
                </div>
        
    
@endsection
@section('script_content_js')    
    <script>
         $(function(){            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
         });

        function ver_modal(curp, nombre){            
            $('#curp').val(curp);     
            $('#head_title').html(nombre);
            $.ajax({
                method: "POST",
                url: "{{ route('consultas.bolsa.ver') }}",                    
                data: {                               
                    curp: curp, 
                 }
                })
                .done(function( response ){ 
                    actualiza_modal(response);                    
                });  
            $('#modalIncorporar').modal('show');
        }

        function actualiza_modal(data){
            
            let datos = data ? JSON.parse(data) : null;
            if(datos){
                $('#fecha').val(datos['fecha']);
                $('#empresa').val(datos['empresa']);
                $('#direccion').val(datos['direccion']);
                $('#puesto').val(datos['puesto']);
            }else{
                $('#fecha').val('');
                $('#empresa').val('');
                $('#direccion').val('');
                $('#puesto').val('');
            }
        }
        
        function enviar(){            
            if(confirm("ESTA SEGURO DE GUARDAR LA INFORMACIÓN?")==true){
                var curp = $('#curp').val();
                var fecha = $('#fecha').val();
                var empresa = $('#empresa').val();
                var direccion = $('#direccion').val();
                var puesto = $('#puesto').val();                        
                    $.ajax({
                            method: "POST",
                            url: "{{ route('consultas.bolsa.guardar') }}",                    
                            data: {                               
                                curp: curp, 
                                fecha: fecha, 
                                empresa: empresa , 
                                direccion: direccion, 
                                puesto: puesto
                            }
                    })
                    .done(function( response ){ 
                        alert(response.mensaje);                        
                        $('#'+curp).html('INCORPORADO A: ' +response.empresa);
                    });                    
                    
                    $('body').focus();
                    $('#modalIncorporar').modal('hide');
            }            
        }

        $(document).ready(function(){
                $("#btnBuscar").click(function(){
                    loader('show');
                     $('#frmBolsaTrabajo').attr('action', "{{ route('consultas.bolsa.index')}}");
                    $("#frmBolsaTrabajo").attr("target", '_self');
                    $('#frmBolsaTrabajo').submit();
                });
                $("#btnLimpiar").click(function(){
                    loader('show');
                    $("#text_buscar_curso").val("");
                    $("#sel_nacionalidad").val("");
                    $("#fechaIniV").val("");
                    $("#fechaFinV").val("");
                    $('#frmBolsaTrabajo').attr('action', "{{ route('consultas.bolsa.index')}}");
                    $("#frmBolsaTrabajo").attr("target", '_self');
                    $('#frmBolsaTrabajo').submit();
                    
                });

                //Generar reporte excel
                $('#btnReporte').click(function() {
                    $('#frmBolsaTrabajo').attr('action', "{{ route('consultas.bolsa.reporte')}}");
                    $("#frmBolsaTrabajo").attr("target", '_blanck');
                    $('#frmBolsaTrabajo').submit();
                });
            });

            // paginacion
            $(document).on('click', '.pagination a', function(e) {
                loader('show');
            });

            function loader(make) {
                if(make == 'hide') make = 'none';
                if(make == 'show') make = 'block';
                document.getElementById('loader-overlay').style.display = make;
            }
            
            $( ".text_buscar_curso" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ route('consulta.bolsa.autocomp') }}",
                        method: 'POST',
                        dataType: "json",
                        data: {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                            search: request.term,                            
                        },
                        success: function( data ) {
                            response( data );                            
                        }
                    });
                }
            });
        </script>    
@endsection
