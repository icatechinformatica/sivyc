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
                <div class="pull-right">

                        <a class="btn btn-success btn-lg" href="{{route('alumnos.valid')}}" >Agregar Nuevo</a>

                </div>
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
    
@endsection
