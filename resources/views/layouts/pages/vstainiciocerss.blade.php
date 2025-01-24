<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'CERSS | SIVyC Icatech')
<!--seccion-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />  
    <style>   
        table tr td, table tr th{ font-size: 12px;}
    </style>
@endsection
@section('content')       
    <div class="card-header">
        Catálogos / CERSS
    </div>
    <div class="card card-body">    
        <div class="row">                    
            {!! Form::open(['route' => 'cerss.inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                <select name="tipo_cerss" class="form-control mr-sm-2" id="tipo_suficiencia">
                    <option value="">BUSCAR POR TIPO</option>
                        <option value="nombre">NOMBRE</option>
                        <option value="titular">TITULAR</option>
                </select>
                {!! Form::text('busquedaporCerss', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR', 'value' => 1]) !!}
                <button class="btn" type="submit">BUSCAR</button>
            {!! Form::close() !!}
            @can('cerss.create')            
                <a class="btn" href="{{route('cerss.frm')}}">+ Nuevo</a>            
            @endcan            
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
            <caption>Catalogo de Solcitudes</caption>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Titular</th>
                    <th scope="col">Telefono</th>
                    <th scope="col">Estado</th>
                    <th >Opciones</th>
                </tr>
            </thead>
            <tbody>
                @php $n=1; @endphp
                @foreach ($data as $key=>$itemData)
                    <tr>
                        <th>{{ $n++ }}</th>
                        <th scope="row">{{$itemData->nombre}}</th>
                        <td>{{$muni[$key]->muni}}</td>
                        <td>{{$itemData->titular}}</td>
                        <td>{{$itemData->telefono}}</td>
                        @if ($itemData->activo == TRUE)
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
                        <td>
                            @can('cerss.update')
                                <a class="nav-link pt-0" title="Editar" href="{{route('cerss.update', ['id' => $itemData->id])}}">
                                    <i class="fa fa-edit  fa-2x fa-lg text-success" aria-hidden="true"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                </tr>
            </tfoot>
        </table>
        <br>
    </div>
@endsection
