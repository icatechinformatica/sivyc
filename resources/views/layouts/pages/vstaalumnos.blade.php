@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumnos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>CATÁLOGO ASPIRANTES</h2>
                    <label for="">Ingrese nombre, matricula o curp</label>
                    {!! Form::open(['route' => 'alumnos.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        @csrf
                        {{--<select name="busqueda_aspirante" class="form-control mr-sm-2" id="busqueda_aspirante">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="curp_aspirante">CURP</option>
                            <option value="nombre_aspirante">NOMBRE</option>
                            <option value="matricula_aspirante">MATRICULA</option>
                        </select>--}}
                        <div>
                            {!! Form::text('busqueda_aspirantepor', null, ['class' => 'form-control mr-sm-4', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                        </div>
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}
                </div>
                @can('alumnos.inscripcion-paso2')
                    <div class="pull-right">
                        <a class="btn btn-success btn-lg" href="{{route('alumnos.valid')}}" >Agregar Nuevo</a>
                    </div>
                @endcan
            </div>
        </div>
        <hr style="border-color:dimgray">
        @if ($contador > 0)
            <table  id="table-instructor" class="table table-bordered table-responsive-md Datatables">
                <thead>
                    <tr>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">CURP</th>
                        @can('alumnos.inscripcion-paso2')
                            <th scope="col">MODIFICAR</th>
                        @endcan
                        @can('permiso.alu.exo')
                            <th>PERMISO EXONERACION</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retrieveAlumnos as $itemData)
                        <tr>
                            <td scope="row">{{$itemData->apellido_paterno}} {{$itemData->apellido_materno}} {{$itemData->nombre}}</td>
                            <td>{{$itemData->curp}}</td>
                            @can('alumnos.inscripcion-paso2')
                                <td>
                                    <a href="{{route('alumnos.presincripcion-modificar', ['id' => base64_encode($itemData->id)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('permiso.alu.exo')
                                <td>
                                    @if ($itemData->permiso_exoneracion)
                                        <i class="btn btn-success" id="descper" onclick="desactivar('{{$itemData->curp}}');">ACTIVO</i>
                                    @else
                                        <i class="btn btn-danger" onclick="activar('{{$itemData->curp}}');">INACTIVO</i>
                                    @endif
                                </td>
                            @endcan
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
                    <h5 class="modal-title" id="exampleModalLabel">ASIGNAR PERMISO DE EXONERACION</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="modal" enctype="multipart/form-data" action="{{route('activar.permiso.exo')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row d-flex align-items-center">
                            <div class="col-12">
                                <div class="form-row col-md-12">
                                    <div class="form-group col-md-6">
                                        <input type="text" id="curpo" name="curpo" class="form-control" readonly>
                                    </div>
                                    <div class="custom-file form-group col-md-6 text-center">
                                        <input type="file" class="custom-file-input" id="customFile" name="customFile">
                                        <label class="custom-file-label" for="customFile">PDF SOPORTE</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                        <button type="submit" class="btn btn-primary">ACTIVAR</button>
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
            <h5 class="modal-title" id="exampleModalLabel">QUITAR PERMISO DE EXONERACION</h5>
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
            <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
            <button type="button" class="btn btn-primary" id="btnquitar">QUITAR</button>
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
