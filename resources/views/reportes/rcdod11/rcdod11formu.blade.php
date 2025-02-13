
@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection
@section('content')       
    <div class="card-header">
        Reportes / RCDOD-11       
    </div>
    <div class="card card-body">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{$message}}</p>
            </div>
        @endif
        <form method="POST" id="frm" name="frm">
            <div class="row form-inline">
                <div class="form-group">
                    <select name="unidades" id="unidades" class="form-control" placeholder=" ">
                        <option selected disabled="">UNIDAD/ACCION MÓVIL</option>
                        <option value="TODO">TODO</option>
                        @if($tipo=='string')
                        <option value="{{$unidades}}" @if($unidades === $request->unidades) selected @endif>{{$unidades}}</option>
                        @else
                        @foreach($unidades as $unidad)
                        <option value="{{$unidad}}"  @if($unidad === $request->unidades) selected @endif>{{$unidad}}</option>
                        @endforeach
                        @endif  
                    </select>
                </div>
                <div class="form-group ml-2">
                    <select name="mod" id="mod" class="form-control" placeholder=" ">                        
                        <option value="CAE"  @if($request->mod == "CAE") selected @endif>CAE</option>
                        <option value="EXT"  @if($request->mod=="EXT") selected @endif>EXT</option>
                    </select>
                </div>
                <div class="form-group  ml-2">
                    <input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha de inicio" value="{{ $request->fecha_inicio}}">
                </div>
                <div class="form-group ml-2">
                    <input type="date" name="fecha_termino" class="form-control" placeholder="Fecha de termino" value="{{ $request->fecha_termino}}">
                </div>
                
                <div class="form-group ml-2">
                    <input type="submit" value="Filtrar" name="filtrar" id="filtrar" class="btn">
                </div>
                <div class="form-group">
                    <input type="submit" value="PDF" name="pdf" id="pdf" class="btn">
                </div>
                {{csrf_field()}}
            </div>
        </form>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">MATRÍCULA</th>
                            <th class="text-center">ALUMNO</th>
                            <th class="text-center">CONSTANCIA</th>
                            <th class="text-center">DUPLICADO</th> 
                        </tr>
                    </thead>
                    @if(isset($consulta))
                        @php
                            $i = 1;
                        @endphp
                    <tbody>
                        @foreach($consulta as $item)
                        <tr>
                            <td class="text-center">{{$i++}}</td>
                            <td class="text-center">{{$item->matricula}}</td>
                            <td>{{$item->alumno}}</td>
                            <td class="text-center">{{$item->folio}}</td>
                            <td class="text-center">{{$item->duplicado}}</td>                            
                        </tr>
                        @endforeach
                    </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js') 
        <script language="javascript">           
            $(document).ready(function(){ 
                $("#filtrar" ).click(function(){ $("#frm").attr("target", '_self'); $('#frm').attr('action', "{{route('carter')}}"); $('#frm').submit(); });
                $("#pdf" ).click(function(){ $('#frm').attr('action', "{{route('carter.pdf')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});                                
            });
        </script>  
@endsection