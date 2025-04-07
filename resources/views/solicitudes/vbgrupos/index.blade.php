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
                {{ Form::text('clave', $clave ?? '', ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'GRUPO / CURSO / INSTRUCTOR / UNIDAD', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 80]) }}                
                @foreach ($estatus as $key => $value)
                    <div class="form-check">
                        <input type="radio" class="form-check-input ml-4" name="estatus" id="estatus{{ $key }}" value="{{ $key }}" {{ $key == $status ? 'checked' : '' }}>
                        <label class="form-check-label" for="estatus{{ $key }}">
                            {{ $value }}
                        </label>
                    </div>
                @endforeach

            </div>
            <div class="row">
                @include('solicitudes.vbgrupos.table')
            </div>
        {!! Form::close() !!}
    </div>
    @section('script_content_js')
        <script language="javascript">    
         $(function(){
            //metodo
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        function cambia_estado(id, status){
            $.ajax({
                    method: "POST",
                    url: "vbgrupos/vistobueno",
                    data: {
                        id: id,
                        estado: status
                    }
            })
            .done(function( msg ) { alert(msg); });
        }

        function actualizadata(){
            var clave = $('#clave').val();
            var estatus = $('#estatus').val();
            if (clave.length >= 3 || clave.length ==0){ //alert(estatus);
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
            $('#clave').on('keyup', function() { actualizadata(); });            
            $('input[name="estatus"]').on('click', function() {  alert("pasa");
                actualizadata();
            });
        });
        </script>
    @endsection
@endsection
