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
            <div class="modal fade" id="modalDatos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                {{-- color en el modal --}}
                <div class="modal-dialog modal-sm modal-notify modal-danger" id="" role="document">
                <!--Content-->
                <div class="modal-content text-center">
                    <!--Header-->
                    <div class="modal-header d-flex justify-content-center">
                        {{-- Mensaje para el modal --}}
                    <p class="heading font-weight-bold">HOLA</p>
                    </div>
                    <!--Body-->
                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible fade show pl-2 text-left" role="alert">
                           HOLA
                        </div>
                        <div class="form-group">
                            HOLA
                        </div>
                    </div>
                    <!--Footer-->
                    <div class="modal-footer flex-center">                        
                        <a type="button" class="btn btn-outline-danger waves-effect" id="" data-dismiss="modal">
                        <i class="fa fa-times fa-sm text-danger" aria-hidden="true"> </i> &nbsp; CERRAR</a>
                    </div>
                </div>
                <!--/.Content-->
                </div>
            </div>
            {{-- FIN Modal DATOS --}}

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
        });
        
        //$('#modalDatos').modal('show');
        
        </script>
    @endsection
@endsection
