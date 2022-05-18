@extends('theme.sivyc.layout')  {{--AGC--}}
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
    <div class="card-header">
        Consulta de Instructores Disponibles
    </div>
    <div class="card card-body" >
        <br />
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        <form action="{{route('consultas.instructores.disponibles')}}" method="POST">
            <div class="form-row">
                <div class="form-group col-sx-2">
                {{ Form::select('unidad', $unidades,$request->unidad ,['id'=>'unidad','class' => 'form-control mr-sm-2','title' => 'UNIDAD', 'placeholder' => 'UNIDAD/ACCION MÓVIL']) }}
                </div>
                <div class="form-group col-sx-2">
                    {{ Form::select('tipo', ['PRESENCIAL'=>'PRESENCIAL','A DISTANCIA'=>'A DISTANCIA'], $request->tipo, ['id'=>'tipo', 'class' => 'form-control mr-sm-2', 'placeholder' => 'TIPO'] ) }}
                </div>
                <div class="form-group  col-sx-1">
                    {{ Form::select('modalidad', ['EXT'=>'EXT','CAE'=>'CAE'], $request->modalidad, ['id'=>'modalidad', 'class' => 'form-control mr-sm-2', 'placeholder' => 'MOD'] ) }}
                </div>
                <div class="from-group  col-md-3">
                    {{ Form::select('id_curso', $cursos, $request->id_curso, ['id'=>'id_curso','class' => 'form-control mr-sm-2'] ) }}
                </div>               
                <div class="form-group col-sx-2">
                    <input type="date" name="inicio" class="form-control" id="inicio" placeholder="FECHA INICIO" value="{{$request->inicio}}">
                </div>
                <div class="form-group col-sx-2">
                    <input type="date" name="termino" class="form-control" id="termino" placeholder="FECHA TERMINO" value="{{$request->termino}}">
                </div>
                <div class="form-group col-sx-1">
                    <input type="time" name='hini' id='hini' type="text" class="form-control" aria-required="true" value="{{$request->hini}}"/>
                </div>
                <div class="form-group col-sx-1">                    
                    <input type="time" name='hfin' id='hfin' type="text" class="form-control" aria-required="true" value="{{$request->hfin}}"/>
                </div>
                <div class="form-group col-sx-1">
                    <input type="submit" value="BUSCAR" class="btn btn-green">
                </div>
            </div>
            {{csrf_field()}}
        </form>
        <br>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>#</td>                        
                        <td>INSTRUCTOR</td>
                        <td>CURP</td>
                        <td>N.CONTROL</td>
                        <td>ESPECIALIDAD</td>
                        <td>VALIDO</td>
                        <td>ACTUALIZO</td>
                        <td>VALIDACIÓN</td>
                        <td>MEMORÁNDUM</td>
                        <td>SOPORTE</td>
                    </tr>
                    @isset($consulta)
                        <?php $n=1;?>
                        @foreach ($consulta as $item)
                        <tr>        
                            <td>{{ $n++}}</td>
                            <td>{{$item->instructor}}</td>
                            <td>{{$item->curp}}</td>
                            <td>{{$item->numero_control}}</td>
                            <td>{{$item->nombre}}</td>
                            <td>{{$item->unidad}}</td>
                            <td>{{$item->unidad_solicita}}</td>
                            <td>{{$item->fecha_validacion}}</td>
                            <td>{{$item->memorandum_validacion}}</td>
                            <td>
                                @if ($item->archivo_alta != NULL)                                    
                                    <a class="nav-link" href={{$item->archivo_alta}} target="_blank">
                                        <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                                    </a>

                                @else
                                    {{"NO FOUND"}}
                                @endif
                           </td>
                        </tr>
                        @endforeach                  
                    @endisset
                </table>
            </div>
        </div>
    </div>
    @section('script_content_js') 
        <script src="{{asset('js/preinscripcion/grupo.js')}}"></script>   
        
    @endsection
@endsection
