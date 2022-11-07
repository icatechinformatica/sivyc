{{--  AGC  --}}
@extends('theme.sivyc.layout')
@section('title', 'Solicitud Clave de Apertura | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
    
    <div class="card-header">
        Busqueda / Solicitud Clave de Apertura
    </div>
    <div class="card card-body" style=" min-height:450px;">              
    {{ Form::open(['route' => 'solicitudes.aperturas.search', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
        @csrf
        <div class="row">
            <div class="form-group col-md-3">
                {{ Form::text('valor', '', ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. Revisión o No. Memorándum', 'aria-label' => 'CLAVE DEL CURSO', 'size' => 25]) }}
            </div>
            <div class="form-group col-md-2">
                    {{ Form::submit('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
            </div> 
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="padding: 1px;" class="text-center">UNIDAD DE CAPACITACIÓN</th> 
                        <th style="padding: 1px;" class="text-center">No. REVISIÓN</th>
                        <th style="padding: 1px;" class="text-center">No. MEMORÁNDUM</th>
                        <th style="padding: 1px;" class="text-center">STATUS</th>
                        <th style="padding: 1px;" class="text-center">SOLICITUD</th>
                        <th style="padding: 1px;" class="text-center">SOPORTE</th>
                        <th style="padding: 1px;" class="text-center">VER</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($aperturas as $item)
                        @php
                            $pdf = $rojo = null;
                            if ($item->pdf_curso) {
                                $pdf = $item->pdf_curso;
                            } else {
                                $pdf = '/storage/uploadFiles'.$item->file_arc01;
                            }
                            if (($item->status_solicitud=='TURNADO') OR ($item->status_curso=='SOLICITADO') OR ($item->status_curso=='EN FIRMA')) {
                                $rojo = true;
                            }
                        @endphp
                        <tr @if($rojo)class='text-danger' @endif>
                            <td class="text-center">{{$item->unidad}}</td>
                            <td class="text-center">{{$item->num_revision}}</td>
                            <td class="text-center">{{$item->munidad}}</td>
                            <td class="text-center">{{$item->status}}</td>
                            <td class="text-center"> @if($item->status_curso) {{$item->status_curso}} @else {{"PREVALIDACION" }} @endif </td>
                            <td class="text-center">
                                @if ($item->pdf_curso OR $item->file_arc01)
                                <a href="{{$pdf}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"  target="_blank" data-placement="top" title="PDF">
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                </a>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($item->munidad)
                                <a class="nav-link" href="{{route('solicitudes.aperturas', ['memo' => $item->munidad])}}">
                                    <i class="fa fa-search  fa-2x fa-lg text-info" title="Ver"></i>
                                </a>
                                @else
                                <a class="nav-link" href="{{route('solicitudes.aperturas', ['memo' => $item->num_revision])}}">
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
            {{ $aperturas->links() }}
        </div>
    {!! Form::close() !!}    
    </div>
    @section('script_content_js') 
        <script language="javascript">      
            $(document).ready(function(){
            });
        </script>
    @endsection 
@endsection