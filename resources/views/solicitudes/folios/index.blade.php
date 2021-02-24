<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content_script_css')    
        <link rel="stylesheet" href="{{asset('css/bootstrap4-toggle.min.css') }}"/>
        <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection
@section('content')   
    <div class="card-header">
        Solicitudes DTA / Registro de Lote de Folios       
    </div>
    <div class="card card-body" style=" min-height:450px;">
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
        <h4>DATOS DE REGISTRO</h4>
        <hr/>
        {{ Form::open(['route' => 'solicitudes.folios.guardar', 'method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data' ]) }} 
            <div class="row form-inline">
                    {{ Form::select('id_unidad', $unidades, NULL ,['id'=>'id_unidad','class' => 'form-control  mr-sm-5 mt-3','title' => 'UNIDAD']) }}
                    {{ Form::select('mod', ['EXT'=>'EXT','CAE'=>'CAE'], '' ,array('id'=>'mod','class' => 'form-control  mr-sm-4 mt-3','title' => 'MODALIDAD')) }}                    
                    {{ Form::text('finicial',NULL, ['id'=>'finicial', 'class' => 'form-control mr-sm-4 mt-3', 'placeholder' => ' FOLIO INICIAL', 'required' => 'required', 'size' => 20]) }}
                    {{ Form::text('ffinal',NULL, ['id'=>'ffinal', 'class' => 'form-control mr-sm-4 mt-3', 'placeholder' => 'FOLIO FINAL', 'required' => 'required', 'size' => 20]) }}
                    {{ Form::text('total',NULL, ['id'=>'total', 'class' => 'form-control mr-sm-4 mt-3', 'placeholder' => 'CANTIDAD', 'required' => 'required', 'size' => 15]) }}                    
                    {{ Form::text('num_acta',NULL, ['id'=>'num_acta', 'class' => 'form-control mr-sm-4 mt-3', 'placeholder' => 'NUM.ACTA', 'required' => 'required', 'size' => 30]) }}                    
                    {{ Form::date('facta', NULL , ['id'=>'facta', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA ACTA', 'required' => 'required']) }}                                        
                    <div class="input-group mr-sm-4 mt-3">
                        <div class="custom-file">
                            <input type="file" id="file_acta" name="file_acta" accept="application/pdf" class="custom-file-input">
                            <label for="file_acta" class="custom-file-label">PDF ACTA</label>
                        </div>
                    </div> 
                    <div class="input-group mr-sm-4 mt-3">                        
                        <input type="checkbox" id="publicar" name="publicar" checked data-toggle="toggle" data-on="PUBLICAR" data-off="No Publicar" data-onstyle="primary" data-offstyle="danger" data-width="140" data-height="38">
                    </div>                   
                    {{ Form::button('AGREGAR', ['class' => 'btn mr-sm-4 mt-3', 'type' => 'submit']) }}                                    
            </div>
        {!! Form::close() !!}
        <br/>
        
        <hr/>
        {{ Form::open(['route' => 'solicitudes.folios', 'method' => 'post','id'=>'frmbuscar', 'enctype' => 'multipart/form-data' ]) }}              

            <div class="form-row">
                <div class="form-group col-md-2">           
                    {{ Form::text('num_acta', NULL, ['id'=>'num_acta', 'class' => 'form-control mr-sm-2', 'placeholder' => 'NUM. ACTA', 'aria-label' => 'CLAVE DEL CURSO', 'size' => 20]) }}
                </div>
                <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['class' => 'btn', 'type' => 'submit']) }}                
                </div>
            </div>
        {!! Form::close() !!}
        <div class="row">
            @include('solicitudes.folios.table')
        </div>
    </div>    
    @section('script_content_js')        
        <script src="{{ asset('js/bootstrap4-toggle.min.js') }}"></script>
                
        <script language="javascript">        
             $('#chkToggle2').bootstrapToggle();             

            function editar(clave){
                $("#clave").val(clave);
                $('#frm').attr('action', "{{route('grupos.consultas.calificaciones')}}"); $('#frm').submit();                 
            }
       
        $(function() {
            $( ".datepicker" ).datepicker({
                dateFormat: "yy-mm-dd"
            });            
         });         
 
    </script>  
    @endsection
@endsection
