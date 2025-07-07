<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Preinscripción | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
     <div class="card-header">
        Preinscripci&oacute;n / Buscar Grupo
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
            {!! html()->form('POST', route('preinscripcion.buscar'))->id('frm')->class('form-inline')->attribute('enctype', 'multipart/form-data')->open() !!}
                {!! html()->select('ejercicio', $anios, $parameters['ejercicio'] ?? '')
                    ->id('ejercicio')
                    ->class('form-control mr-sm-2')
                    ->attribute('title', 'EJERCICIO')
                    ->placeholder('EJERCICIO') !!}
                {!! html()->text('valor_buscar')
                    ->class('form-control')
                    ->attribute('size', 50)
                    ->placeholder('GRUPO / CURSO')
                    ->attribute('aria-label', 'BUSCAR') !!}
                {!! html()->submit('BUSCAR')->class('btn')->type('submit') !!}
                {!! html()->hidden('folio_grupo')->id('folio_grupo') !!}
            {!! html()->form()->close() !!}
        </div>
        <div class="row">
            @include('preinscripcion.buscar.table')
        </div>
    </div>
    <br>

    @section('script_content_js')
         <script language="javascript">
                function  show(id){
                    //if(id>0){
                        $('#folio_grupo').val(id);
                        $('#frm').attr('action', "{{route('preinscripcion.show')}}"); $('#frm').submit();
                    //}alert(id);
                }
                function  show2(id){
                    //if(id>0){
                        $('#folio_grupo').val(id);
                        $('#frm').attr('action', "{{route('preinscripcion.showvb')}}"); $('#frm').submit();
                    //}alert(id);
                }
        </script>
    @endsection
@endsection
