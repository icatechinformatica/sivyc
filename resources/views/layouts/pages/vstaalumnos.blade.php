
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        table tr td, table tr th{ font-size: 12px; width: 10%;}
        table tr th{ text-align: center;}

    </style>
@endsection
@extends('theme.sivyc.layout')
@section('title', 'Preinscripción - Aspirantes | SIVyC Icatech')
@section('content')
    <div class="card-header">
        Presincripción / Aspirantes
    </div>
    <div class="card card-body  p-5" style=" min-height:450px;">
        @if ($message = Session::get('success'))
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb form-inline">
                {!! Form::open(['route' => 'alumnos.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                    @csrf
                    {!! Form::text('busqueda_aspirantepor', $buscar_aspirante, ['class' => 'form-control mr-sm-4 mb-2', 'size' => '50', 'placeholder' => 'NOMBRE/CURP/MATRICULA', 'aria-label' => 'BUSCAR']) !!}
                    {{ Form::button("<i class='fa fa-search mr-1'></i> BUSCAR", ['type' => 'submit', 'id' => 'buscar','name' => 'BUSCAR', 'class' => 'form-control btn mb-2']) }}
                {!! Form::close() !!}
                @if ($canEditAlumnos)
                    <a class="form-control btn mb-2" href="{{route('alumnos.valid')}}" ><i class='fa fa-plus mr-1'></i> Nuevo</a>
                @endif
            </div>
        </div>
        @if ($contador > 0)
        <div class="p-0 m-0 w-100" >
            <table class="table table-hover table-responsive w-100"  style="table-layout: fixed; width: 100%;" >
                <thead>
                    <tr>
                        <th scope="col" class="w-40 text-left" >NOMBRE DEL ASPIRANTE</th>
                        <th scope="col">CURP</th>
                        <th scope="col">FECHA ACT.</th>
                        <th scope="col">ACTUALIZADO POR</th>
                        <th scope="col">CURP/ESTUDIOS/..</th>
                        @if ($canEditAlumnos)
                            <th scope="col">EDITAR</th>
                        @endif
                        @if ($canManageExtraPermiso)
                            <th>CURSO EXTRA</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retrieveAlumnos as $itemData)
                        <tr>
                            <td scope="row" style="width: 25%">{{$itemData->apellido_paterno}} {{$itemData->apellido_materno}} {{$itemData->nombre}}</td>
                            <td>{{$itemData->curp}}</td>
                            <td  class="text-center">{{date('d/m/Y', strtotime($itemData->updated_at))}}</td>
                            <td class="text-center small" style="width: 20%">{{$itemData->name}}</td>
                            <td class="text-center">
                                @if($itemData->documento)
                                    <a class="nav-link pt-0"  href="{{$itemData->documento}}" target="_blank">
                                          <i class="far fa-file-pdf fa-3x text-danger" title="DESCARGAR RECIBO DE PAGO OFICIALIZADO."></i>
                                    </a>
                                @else
                                    <i  class="far fa-file-pdf  fa-3x text-muted pt-0"  title='ARCHIVO NO DISPONIBLE.'></i>
                                @endif
                            </td>
                            <td  class="text-center">
                                <a href="{{route('alumnos.valid', ['busqueda' => $itemData->curp])}}">
                                    <i class="fa fa-edit  fa-2x fa-lg text-success" aria-hidden="true"></i>
                                </a>
                            </td>
                            @if ($canManageExtraPermiso)
                                <td  class="text-center">
                                    @if ($itemData->curso_extra)
                                        <i class="btn btn-success" id="descper" onclick="desactivar('{{$itemData->curp}}');">&nbsp;&nbsp;ACTIVO&nbsp;&nbsp;</i>
                                    @else
                                        <i class="btn btn-danger" onclick="activar('{{$itemData->curp}}');">INACTIVO</i>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            {{ $retrieveAlumnos->appends(request()->query())->links() }}
                        </td>
                    </tr>
                </tfoot>
            </table>
    </div>
        @else
            <div class="alert alert-warning" role="alert">
                <h2>NO HAY ALUMNOS REGISTRADOS!</h2>
            </div>
        @endif
        <br>
    </div>
    <br>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">AUTORIZAR PERMISO PARA UN CURSO EXTRA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="modal" enctype="multipart/form-data" action="{{route('activar.permiso.exo')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row d-flex align-items-center">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="curp" class="control-label h6">CURP:</label>
                                        <input type="text" id="curpo" name="curpo" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="motivo" class="control-label h6">DESCRIBA LA JUSTIFICACIÓN:</label>
                                    <textarea name="motivo" id="motivo" class="form-control"  rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">ACTIVAR</button>
                        <button type="button" class="btn" data-dismiss="modal">CERRAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal 2 -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">QUITAR PERMISO PARA UN CURSO EXTRA</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <div class="row d-flex align-items-center">
                <div class="col-12">
                    <form id="modal2" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-12">
                                <input type="text" id="curpa" name="curpa" class="form-control" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="btnquitar">QUITAR</button>
            <button type="button" class="btn" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
        </div>
    </div>
    @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                // $("#btnactivar").click(function(){ $('#modal').attr('action', "{{route('activar.permiso.exo')}}",'multipart/form-data'); $('#modal').submit(); });
                $("#btnquitar").click(function(){ $('#modal2').attr('action', "{{route('quitar.permiso.exo')}}"); $('#modal2').submit(); });
            });
            function activar(id){
                var id = id;
                $('#curpo').val(id);
                $('#exampleModal').modal('show')
            }
            function desactivar(id){
                var id = id;
                $('#curpa').val(id);
                $('#exampleModal2').modal('show')
            }
        </script>
    @endsection
@endsection
