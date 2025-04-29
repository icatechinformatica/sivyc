<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Solicitudes -VB Grupos | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        .form-check-input{
            width:22px;
            height:22px;
        }
        #result_body ul { padding: 5px; list-style-type: none}
        #result_body ul li { font-size: 12x; margin-bottom:10px; width: 232px;}
        #result_body ul li p{ font-size: 11px; margin-bottom:12px; display: block;  }
        #result_body ul li b { font-size: 14px; padding: 3px;}
        .modal-header p {font-size: 10px;}
        .fas { cursor: pointer; color: red;}
    </style>
@endsection
@section('content')
    <div class="card-header">
        Solicitudes / V.B. de Grupos de Capacitación
    </div>
    <div class="card card-body">
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <?php
            if(isset($curso)) $clave = $curso->clave;
            else $clave = null;
        ?>
        {{ Form::open(['route' => 'solicitudes.vb.grupos', 'method' => 'post', 'id'=>'frm']) }}
            <div class="row form-inline">                  
                <div class="d-flex flex-lg-row flex-column col-12 col-md-6 col-sm-12 justify-content-left">
                    {{ Form::text('clave', $clave ?? '', ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'CURSO / INSTRUCTOR / UNIDAD', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 60]) }}
                </div>    
                <div class="d-flex flex-lg-row flex-column col-12 col-md-6 col-sm-12 justify-content-end">
                    @foreach ($estatus as $key => $value)
                        <div class="form-check d-flex mt-2">
                            <input type="radio" class="form-check-input col-md-6" name="estatus" id="estatus{{ $key }}" value="{{ $key }}" {{ $key == $status ? 'checked' : '' }}>
                            <label class="form-check-label col-md-6 mt-1" for="estatus{{ $key }}">
                                {{ $value }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row">
                @include('solicitudes.vbgrupos.table')
            </div>

            {{-- Modal DATOS --}}
            <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">                
                <div class="modal-dialog modal-sm modal-notify modal-danger" id="" role="document">                
                    <div class="modal-content text-center">
                        <!--Header-->
                        <div class="modal-header d-flex justify-content-center" style="background-color:rgb(201, 1, 102);" >                            
                            <p class="heading font-weight-bold" id="result_head">DATOS</p>  
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="text-light">&times;</span></button>                      
                        </div>
                        <!--Body-->
                        <div class="modal-body">
                            <div class="alert alert-danger alert-dismissible fade show pl-2 text-left" role="alert"  id="result_body" ></div>                            
                        </div>
                    </div>                
                </div>
            </div>
            {{-- FIN Modal DATOS --}} 
            
            {{-- Modal RECHAZAR --}}
            <div class="modal fade" id="modalMotivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">                
                <div class="modal-dialog modal-sm modal-notify modal-danger" id="" role="document">                
                    <div class="modal-content text-center">
                        <!--Header-->
                        <div class="modal-header d-flex justify-content-center" style="background-color:rgb(201, 1, 102);" >                            
                            <p class="heading font-weight-bold">Curso: <block id="body_motivo"> </block></p>  
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="text-light">&times;</span></button>                      
                        </div>
                        <!--Body-->
                        <div class="modal_body">                            
                            <div class="alert alert-dismissible fade show p-2 text-left" role="alert">                                
                                {{ Form::hidden('id_curso', '',['id'=>'id_curso']) }}
                                <h6>MOTIVO:</h6>
                                {{ Form::textarea('motivo',old('motivo'), ['id'=>'motivo', 'class' => 'form-control mt-2', 'placeholder' => 'Describir el motivo del rechazo.', 'rows' => 3]) }}
                                <div class="modal-footer flex-center">
                                    <button class="btn btn-danger" id="rechazar" onclick="rechazar()">RECHAZAR</button>                                    
                                </div>
                            </div>
                        </div>
                    </div>                
                </div>
            </div>
            {{-- FIN Modal RECHAZAR --}}

        {!! Form::close() !!}
    </div>
    @section('script_content_js')
        <script language="javascript">    
            $(function(){            
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
            function cambia_estado(id, status){
                estado = status.prop('checked');
                $.ajax({
                        method: "POST",
                        url: "vbgrupos/vistobueno",
                        data: {
                            id: id,
                            estado: estado
                        }
                })
                .done(function( msg ) { alert(msg); });
            }

            function actualiza_data(){
                var clave = $('#clave').val();
                var estatus = $('input[name="estatus"]:checked').val();
                if (clave.length >= 3 || clave.length ==0 || estatus.length>0){
                    $.ajax({
                        url: "vbgrupos/buscar",
                        method: 'POST',
                        data: {
                            clave: clave,
                            estatus: estatus
                        },
                        success: function(data) {
                            $('#result_table').html(data);
                        }
                    });
                }
            }

            $(document).ready(function(){
                $('#clave').on('keyup', function() { actualiza_data(); });            
                $('input[name="estatus"]').on('click', function() { 
                    actualiza_data();
                });

                $("#rechazar" ).click(function(){
                    if(confirm("Esta seguro de GUARDAR?")==true){
                        $('#frm').attr('target', '_self');
                        $('#frm').attr('action', "{{route('solicitudes.vb.grupos.rechazar')}}"); $('#frm').submit();
                    }
                });
            
            });
            
            function ver_modal(tipo, folio_grupo){
                $('#result_head').html("");
                $('#result_body').html("");
                if (folio_grupo.length >0){
                    $.ajax({
                        url: "vbgrupos/getinfo",
                        method: 'POST',
                        data: {
                            tipo: tipo,
                            folio_grupo : folio_grupo
                        },
                        success: function(data) {                                                        
                            $('#result_head').html(data[0]);
                            $('#result_body').html(data[1]);
                            $("#modalDetalles").modal("show");
                        }
                    });
                    
                }
            }

            function modal_motivo(curso,id_curso){
                $('#id_curso').val(id_curso);
                $('#body_motivo').html(curso);
                $("#modalMotivo").modal("show");
            }
        </script>
    @endsection
@endsection
