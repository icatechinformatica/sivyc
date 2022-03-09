<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Instructor | SIVyC Icatech')
<!--seccion-->
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
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
    <div class="card-header">
        <h3>REGISTRO DE INSTRUCTORES</h3>
    </div>
    <div class="card card-body">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    {!! Form::open(['route' => 'instructor-inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipo_busqueda_instructor" class="form-control mr-sm-2" id="tipo_busqueda_instructor">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="clave_instructor">CLAVE INSTRUCTOR</option>
                            <option value="nombre_instructor">NOMBRE</option>
                            <option value="telefono_instructor">TELÉFONO</option>
                            <option value="estatus_instructor">ESTATUS</option>
                        </select>
                        <Div id="divcampo" name="divcampo">
                            {!! Form::text('busquedaPorInstructor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                        </Div>
                        <Div id="divstat" name="divstat" class="d-none d-print-none">
                            <select name="tipo_status" class="form-control mr-sm-2" id="tipo_status">
                                <option value="">BUSQUEDA POR STATUS</option>
                                <option value="En Proceso">EN PROCESO</option>
                                <option value="Validado">VALIDADO</option>
                                <option value="Rechazado">RECHAZADO</option>
                            </select>
                        </Div>
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}

                </div>
                <br>
                <div class="pull-right">
                    @can('instructor.create')
                        <a class="btn btn-success btn-lg" href="{{route('instructor-crear')}}"> Nuevo</a>
                    @endcan
                    @can('academico.exportar.instructores')
                        <a class="btn btn-info btn-lg" href="{{route('academico.exportar.instructores')}}">Exportar Instructores</a>
                    @endcan
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered table-responsive-md">
            <caption>Catalogo de Instructrores</caption>
            <thead>
                <tr>
                    <th scope="col">Clave Instructor</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">telefono</th>
                    <th scope="col">Status</th>
                    <th width="160px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->numero_control}}</th>
                        <td>{{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}} {{$itemData->nombre}}</td>
                        <td>{{$itemData->telefono}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>
                            @if ($itemData->status == 'En Proceso')
                                {{-- @can('instructor.validar')
                                    <a class="btn btn-info" href="{{route('instructor-validar', ['id' => itemData->id])}}">Validar</a>
                                @endcan --}}
                                @can('instructor.create')
                                    <a class="btn btn-info btn-md" href="{{route('instructor-crear-p2', ['id' => $itemData->id])}}"><small>Continuar Solicitud</small></a>
                                @endcan
                            @endif
                            @if ($itemData->status == 'Rechazado')
                                @can('instructor.editar_fase1')
                                    <a class="btn btn-info" href="{{route('instructor-editar', ['id' => $itemData->id])}}">Editar</a>
                                @endcan
                            @endif
                            @if ($itemData->status == 'Aprobado' || $itemData->status == 'Baja')
                                    <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
                            @endif
                            @if ($itemData->status == 'Validado')
                                    <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="5">
                        {{ $data->appends(request()->query())->links() }}
                    </td>
                </tr>
            </tfoot>
        </table>
        <br>
    </div>
    <br>
    <!-- Modal -->
        <div class="modal fade" id="confirmsaveins" role="dialog">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('folio-permiso-mod') }}" id="mod_folio">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">¿Esta seguro de ir al siguiente paso?<b></b></h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="form-row">
                        <div class="form-group col-md-3"></div>
                            {{-- <div class="form-group col-md-6">
                                <label for="unidad" class="control-label">Esto Regresara la Validación Suficiencia Presupuestal a planeación para su correción</label>
                            </div> --}}
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2"></div>
                            <div class="form-group col-md-4">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                            <div class="form-group col-md-4">
                                <a id="valsupre_confirm" href="#" class="btn btn-primary" >Confirmar</a>
                            </div>
                            <div class="form-group col-md-1"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <!-- END -->
@endsection
@section('script_content_js')
    <script>
        $(function(){
            //metodo
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            document.getElementById('tipo_busqueda_instructor').onchange = function() {
                var index = this.selectedIndex;
                var inputText = this.children[index].innerHTML.trim();
                if(inputText != 'ESTATUS')
                {
                    $('#divstat').prop("class", "form-row d-none d-print-none")
                    $('#divcampo').prop("class", "")
                }
                else
                {
                    $('#divstat').prop("class", "")
                    $('#divcampo').prop("class", "form-row d-none d-print-none")
                }
            }
        });
    </script>
@endsection
