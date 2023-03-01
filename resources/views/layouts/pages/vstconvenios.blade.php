<!--Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Convenios | SIVyC Icatech')
    <!--seccion-->

@section('content')
    <style>
        * {
            box-sizing: border-box;
        }

        #myInput {
            background-image: url('img/search.png');
            background-position: 5px 10px;
            background-repeat: no-repeat;
            background-size: 32px;
            width: 100%;
            font-size: 16px;
            padding: 12px 20px 12px 40px;
            border: 1px solid #ddd;
            margin-bottom: 12px;
        }

    </style>

    <div class="container-fluid px-5 g-pt-30">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h1>Convenios</h1>
                </div>
                @can('convenios.create')
                    <div class="pull-right">
                        <a class="btn btn-success btn-lg" href="{{ route('convenio.create') }}">NUEVO</a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                {!! Form::open(['method' => 'GET', 'id' => 'frm_one', 'class' => 'form-inline']) !!}
                {{ Form::select('busqueda', ['no_convenio'=>'N° DE CONVENIO','institucion'=>'INSTITUCIÓN','tipo_convenio'=>'TIPO DE CONVENIO','sector'=>'SECTOR', 'fechas'=>'FECHA'], $request->busqueda ,['id'=>'busqueda','class' => 'form-control mr-sm-2','title' => 'BUSCAR POR TIPO','placeholder' => 'BUSCAR POR TIPO', 'onchange' => 'selectOp()']) }}
                {!! Form::text('busqueda_conveniopor', $request->busqueda_conveniopor, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR',
                'aria-label' => 'BUSCAR', 'id' => 'busqueda_conveniopor']) !!}
                {{-- cajas para fechas --}}
                {{ Form::date('fecha1', $request->fecha1, ['id'=>'fecha1', 'class' => 'form-control datepicker  mr-sm-3 d-none', 'placeholder' => 'FECHA INICIO', 'title' => 'FECHA INICO']) }}
                {{ Form::date('fecha2', $request->fecha2, ['id'=>'fecha2', 'class' => 'form-control datepicker  mr-sm-3 d-none', 'placeholder' => 'FECHA TERMINO', 'title' => 'FECHA TERMINO']) }}
                {{ Form::button('BUSCAR', ['id' => 'botonBUSCAR', 'name'=> 'boton', 'value' => 'BUSCAR', 'class' => 'btn btn-outline-primary']) }}
                @can('convenios.edit')
                {{ Form::button('EXPORTAR REGISTROS <i class="fa fa-file-excel-o fa-2x fa-lg text-dark ml-1"></i>', ['id' => 'botonGENEXCEL', 'value' => 'EXPORTAR REGISTROS2', 'class' => 'btn btn-warning text-dark']) }}
                @endcan
                {!! Form::close() !!}
            </div>
        </div>

        <table id="table-instructor" class="table table-bordered table-striped mt-5">
            <thead>
                <tr>
                    <th scope="col">NO. DE CONVENIO</th>
                    <th scope="col">INSTITUCIÓN</th>
                    <th width="150px">FECHA DE FIRMA</th>
                    <th width="150px">FECHA DE TERMINO</th>
                    <th width="150px">TIPO DE CONVENIO</th>
                    <th width="150px">SECTOR</th>
                    <th width="150px">STATUS</th>
                    {{-- <th width="150px">FECHA DE ACTUALIZACIÓN</th> --}}
                    <th scope="col">ARCHIVO CONVENIO</th>
                    @can('convenios.edit')
                        <th scope="col">MODIFICAR</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                        <td scope="row">{{ $itemData->no_convenio }}</td>
                        <td>{{ $itemData->institucion }}</td>
                        <td>{{ $itemData->fecha_firma }}</td>
                        <td class="{{$itemData->activo == 'false' ? 'text-danger' : ''}}">{{ $itemData->fecha_vigencia != null ? $itemData->fecha_vigencia : 'INDEFINIDO' }}</td>
                        <td>{{ $itemData->tipo_convenio }}</td>
                        <td>{{ $itemData->sector }}</td>
                        <td>{{ $itemData->activo == 'false' ? 'NO PUBLICADO' : 'PUBLICADO'}}</td>
                        {{-- <td>{{ $itemData->updated_at == '' ? 'SIN FECHA' : $itemData->updated_at->format('d-m-Y')}}</td> --}}
                        <td>
                            <div class="custom-file">
                                @if (isset($itemData->archivo_convenio))
                                    <a href="{{ $itemData->archivo_convenio }}" target="_blank"
                                        rel="{{ $itemData->archivo_convenio }}">
                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="50px"
                                            height="50px">
                                    </a>
                                @else
                                    NO ADJUNTADO
                                @endif
                            </div>
                        </td>
                        @can('convenios.edit')
                            <td>
                                <a class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="EDITAR CONVENIO"
                                    href="{{ route('convenios.edit', ['id' => base64_encode($itemData->id)]) }}">
                                    <i class="fa fa-pencil-square-o fa-2x mt-2" aria-hidden="true"></i>
                                </a>
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row py-4">
        <div class="col d-flex justify-content-center">
            {{$data->appends(request()->query())->links()}}
        </div>
    </div>

        @section('script_content_js')
        <script language="javascript">

            $(document).ready(function(){
                /*Agregamos funciones a los botones buscar y generar excel*/
                $("#botonBUSCAR" ).click(function(){ $('#frm_one').attr('action', "{{route('convenios.index')}}"); $("#frm_one").attr("target", '_self'); $('#frm_one').submit(); });
                $("#botonGENEXCEL" ).click(function(){ $('#frm_one').attr('action', "{{route('convenios.genexcel')}}"); $("#frm_one").attr("target", '_blanck');$('#frm_one').submit();});

                /*Mostramos los campos fechas en caso de mantener el select en fechas*/
                    if($('#busqueda').val() == 'fechas'){
                        $("#busqueda_conveniopor").addClass('d-none');
                        $("#fecha1").removeClass('d-none');
                        $("#fecha2").removeClass('d-none');
                        $('#busqueda_conveniopor').val("");
                    }
            });
            /*Agragamos y quitamos clases 'd-none' de acuerdo al select de opciones*/
            function selectOp() {
                if($('#busqueda').val() == 'fechas'){
                    $("#busqueda_conveniopor").addClass('d-none');
                    $("#fecha1").removeClass('d-none');
                    $("#fecha2").removeClass('d-none');
                    $('#busqueda_conveniopor').val("");

                }else{
                    $("#fecha1").addClass('d-none');
                    $("#fecha2").addClass('d-none');
                    $("#busqueda_conveniopor").removeClass('d-none');
                    $('#fecha1').val("");
                    $('#fecha2').val("");
                }
            }
            /*Funcion Ajax para realizar un autocompletado*/
            $( "#busqueda_conveniopor" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ route('convenios.autocomplete') }}",
                        method: 'POST',
                        dataType: "json",
                        data: {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                            search: request.term,
                            tipoCurso: $('#busqueda').val()
                        },
                        success: function( data ) {
                            response( data );
                        }
                    });
                }
            });
        </script>
        @endsection
@endsection
