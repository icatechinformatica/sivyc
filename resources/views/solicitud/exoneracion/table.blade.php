{{--  AGC  --}}
@extends('theme.sivyc.layout')
@section('title', 'Busqueda Exoneración y/o Reducción de Cuota | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

    <div class="card-header">
        Busqueda / Exoneración y/o Reducción de Cuota
    </div>
    <div class="card card-body" style=" min-height:450px;">
        {!! Form::open(['route' => 'solicitud.exoneracion.search', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) !!}
            @csrf
            <div class="row">
                <div class="form-group col-md-3">
                    {{ Form::text('valor', '', ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. Revisión o No. Memorándum', 'aria-label' => 'CLAVE DEL CURSO', 'size' => 25]) }}
                </div>
                <div class="form-group col-md-2">
                        {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
                </div> 
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="padding: 1px;" class="text-center">UNIDAD DE CAPACITACIÓN</th> 
                            <th style="padding: 1px;" class="text-center">No. REVISIÓN</th>
                            <th style="padding: 1px;" class="text-center">No. MEMORÁNDUM</th>
                            <th style="padding: 1px;" class="text-center">TURNADO</th>
                            <th style="padding: 1px;" class="text-center">STATUS</th>
                            <th style="padding: 1px;" class="text-center">SOPORTE</th>
                            <th style="padding: 1px;" class="text-center">VER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exoneraciones as $item)
                            <tr>
                                @php
                                    $pdf = null;
                                    if ($item->nrevision) {
                                        $pdf = '/storage/uploadFiles'.$item->memo_soporte_dependencia;
                                    } else {
                                        $pdf = $item->memo_soporte_dependencia;
                                    }
                                @endphp
                                <td class="text-center">{{$item->unidad}}</td>
                                <td class="text-center">{{$item->nrevision}}</td>
                                <td class="text-center">{{$item->no_memorandum}}</td>
                                <td class="text-center">{{$item->turnado}}</td>
                                <td class="text-center"> @if($item->status) {{$item->status}} @else {{"EN CAPTURA" }} @endif </td>
                                <td class="text-center">
                                    @if ($item->memo_soporte_dependencia)
                                    <a href="{{$pdf}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"  target="_blank" data-placement="top" title="PDF">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->no_memorandum)
                                    <a class="nav-link" href="{{route('solicitud.exoneracion', ['valor' => $item->no_memorandum])}}">
                                        <i class="fa fa-search  fa-2x fa-lg text-info" title="Ver"></i>
                                    </a>
                                    @else
                                    <a class="nav-link" href="{{route('solicitud.exoneracion', ['valor' => $item->nrevision])}}">
                                        <i class="fa fa-search  fa-2x fa-lg text-info" title="Ver"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                {{ $exoneraciones->links() }}
            </div>
        {!! Form::close() !!}
    </div>
    @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.exoneracion.search')}}"); $('#frm').attr('target', '_self').submit();});
            }); 
        </script>
    @endsection
@endsection