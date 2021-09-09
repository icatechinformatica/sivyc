<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'DTA Folios | SIVyC Icatech')
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
                    {{ Form::select('mod', ['EXT'=>'EXT','CAE'=>'CAE','GRAL'=>'GENERAL'], '' ,array('id'=>'mod','class' => 'form-control  mr-sm-4 mt-3','title' => 'MODALIDAD')) }}                    
                    {{ Form::text('finicial',NULL, ['id'=>'finicial', 'class' => 'form-control mr-sm-4 mt-3 numero', 'placeholder' => ' FOLIO INICIAL', 'title' => ' FOLIO INICIAL','required' => 'required', 'size' => 20]) }}
                    {{ Form::text('ffinal',NULL, ['id'=>'ffinal', 'class' => 'form-control mr-sm-4 mt-3 numero', 'placeholder' => 'FOLIO FINAL', 'title' => 'FOLIO FINAL', 'required' => 'required', 'size' => 20]) }}                    
                    {{ Form::text('num_acta',NULL, ['id'=>'num_acta', 'class' => 'form-control mr-sm-4 mt-3', 'placeholder' => 'NUM. ACTA', 'title' => 'NUM. ACTA', 'required' => 'required', 'size' => 30]) }}                    
                    {{ Form::date('facta', NULL , ['id'=>'facta', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA ACTA', 'title' => 'FECHA ACTA', 'required' => 'required']) }}                                        
                    <div class="input-group mr-sm-4 mt-3">
                        <div class="custom-file">
                            <input type="file" id="file_acta" name="file_acta" accept="application/pdf" class="custom-file-input">
                            <label for="file_acta" class="custom-file-label">PDF ACTA</label>
                        </div>
                    </div> 
                    <div class="input-group mr-sm-4 mt-3">                        
                        <input type="checkbox" id="publicar" name="publicar" data-toggle="toggle" data-on="PUBLICAR" data-off="NO PUBLICAR" data-onstyle="primary" data-offstyle="danger" data-width="140" data-height="38" >
                    </div>                   
                    {{ Form::button('AGREGAR', ['id' => 'boton', 'name'=> 'boton', 'value' => 'AGREGAR', 'class' => 'btn mr-sm-4 mt-3', 'type' => 'submit']) }}
                    {{ Form::button('CANCELAR', ['id' => 'cancelar','class' => 'btn mr-sm-4 mt-3 hide ']) }}
                    {{ Form::hidden('id',NULL, ['id'=>'id']) }}
                    {{ Form::hidden('valor',$valor, ['id'=>'valor']) }} 
            </div>
        {!! Form::close() !!}
        <br/>        
        <hr/>
        {{ Form::open(['route' => 'solicitudes.folios', 'method' => 'post','id'=>'frmbuscar', 'enctype' => 'multipart/form-data' ]) }}              

            <div class="form-row">
                <div class="form-group col-md-3">           
                    {{ Form::text('num_acta', $valor, ['id'=>'num_acta', 'class' => 'form-control mr-sm-2', 'placeholder' => 'FECHA / NUM. ACTA / UNIDAD']) }}
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
@endsection
@section('script_content_js')        
        <script src="{{ asset('js/bootstrap4-toggle.min.js') }}"></script>                
        <script language="javascript">
            $('#cancelar').click(function (){  
                $("#id" ).val('');
                $('#boton').text('AGREGAR');                
                $('#cancelar').hide();
                //$('#boton').css('background-color','#12322b');                
            });
             $("#boton" ).click(function(){ 
                    if(confirm("Esta seguro de ejecutar la acci\u00F3n?")==true)$('#frm').submit();
             }); 
                         
            function editar(id,asignados) {
                $.ajax({
                    data: {id : id, _token:"{{csrf_token()}}"},
                    url: "{{route('solicitudes.folios.edit')}}",
                    type:  'GET',
                    dataType : 'json',
                    success:  function (data) {
                        
                        if(data['id'])$("#id" ).val(data['id']);
                        if(data['id_unidad'])$("#id_unidad option[value="+ data['id_unidad'] +"]").attr("selected",true);
                        if(data['mod'])$("#mod option[value="+ data['mod'] +"]").attr("selected",true);
                        if(data['num_inicio'])$("#finicial" ).val(data['num_inicio']);
                        if(data['num_fin'])$("#ffinal" ).val(data['num_fin']);
                        if(data['num_acta'])$("#num_acta" ).val(data['num_acta']);
                        if(data['facta'])$("#facta" ).val(data['facta']);
                        if(data['activo']==true)$('#publicar').bootstrapToggle('on'); 
                        else $('#publicar').bootstrapToggle('off'); 
                          
                        $('#cancelar').css('background-color','#EBA801');
                        $('#boton').text('GUARDAR CAMBIOS');
                        $('#boton').val('GUARDAR CAMBIOS');                        
                        $('#cancelar').show();
                        $("#ffinal").focus();
                        //console.log(data);                       
                    },
                    error:function(x,xs,xt){                        
                        alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                    }
                });
            }

       
            $(function() {
                $( ".datepicker" ).datepicker({
                    dateFormat: "yy-mm-dd"
                });            
            });         
            
            $('.numero').keyup(function (){                    
                    this.value = (this.value + '').replace(/[^0-9NP]/g, '');
            });
        </script>  
@endsection
