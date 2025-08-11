{{--  AGC  --}}
@extends('theme.sivyc.layout')
@section('title', 'Solicitud Clave de Apertura | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

    <div class="card-header">
        Búsqueda / Solicitud Clave de Apertura
    </div>
    <div class="card card-body" style=" min-height:450px;">        
        
    {{ Form::open(['route' => 'solicitudes.aperturas.search', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data','class' => 'form-inline']) }}
            @csrf            
            {{ Form::select('ejercicio', $anios, $ejercicio ??'' ,['id'=>'ejercicio','class' => 'form-control mr-sm-2','title' => 'EJERCICIO','placeholder' => 'EJERCICIO']) }}
            {{ Form::text('valor', '', ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. Revisión o No. Memorándum', 'aria-label' => 'CLAVE DEL CURSO', 'size' => 25]) }}            
            {{ Form::submit('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
        <div class="table-responsive mt-4">            
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">UNIDAD DE CAPACITACIÓN</th>
                        <th class="text-center">No. REVISIÓN</th>
                        <th class="text-center">No. MEMORÁNDUM</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">SOLICITUD</th>
                        <th class="text-center">SOPORTE</th>
                        <th class="text-center">VER</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $i = 1; 
                        $currentPage = request()->get('page', 1);
                        $perPage = 50;
                        $startIndex = ($currentPage - 1) * $perPage;                    
                    @endphp

                    @foreach ($aperturas as $index => $item)
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
                            $i = $startIndex + $index + 1;
                        @endphp
                        <tr @if($rojo)class='text-danger' @endif>
                            <td class="text-center">{{$i}}</td>
                            <td class="text-center">{{$item->unidad}}</td>
                            <td class="text-center">{{$item->num_revision}}</td>
                            <td class="text-center">{{$item->munidad}}</td>
                            <td class="text-center">{{$item->status}}</td>
                            <td class="text-center">{{ $item->status_solicitud}}</td>
                            <td class="text-center">
                                @if ($item->pdf_curso OR $item->file_arc01)
                                    <a  class="nav-link"   title="PDF"  href="{{$pdf}}">
                                        <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
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
            <div style="text-align: center; font-weight: bold; margin-top: 10px;">
                {{ $aperturas->links() }}
            </div>
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
