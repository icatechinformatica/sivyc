@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Nuevo CERSS | Sivyc Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />      
@endsection
@section('content')
    <div class="card-header">
        Catálogos / NUEVO CERSS
    </div>
    <div class="card card-body">
        <form action="{{ route('cerss.save') }}" method="post" id="registercerss">
            @csrf            
             <div class="form-row">
                 <div class="form-group col-md-4">
                    <label for="nombre" class="control-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                 </div>
                 <div class="form-group col-md-3">
                    <label for="municipio" class="control-label">Municipio</label>
                    <select name="municipio" id="municipio" class="form-control" required>
                        <option value="sin especificar">SIN ESPECIFICAR</option>
                        @foreach ($muni as $data )
                            <option value="{{$data->id}}">{{$data->muni}}</option>
                        @endforeach
                    </select>
                 </div>
                 <div class="form-group col-md-3">
                    <label for="unidad" class="control-label">Unidad de Capacitación Asignada</label>
                    <select name="unidad" id="unidad" class="form-control" required>
                        <option value="sin especificar">SIN ESPECIFICAR</option>
                        @foreach ($unidad as $data )
                            <option value="{{$data->id}}">{{$data->unidad}}</option>
                        @endforeach
                    </select>
                 </div>
             </div>
             <div class="form-row">
                 <div class="form-group col-md-4">
                    <label for="titular" class="control-label">Titular</label>
                    <input type="text" class="form-control" id="titular" name="titular" placeholder="Nombre del Titular" required>
                 </div>
                 <div class="form-group col-md-4">
                    <label for="direccion" class="control-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" required>
                 </div>
                 <div class="form-group col-md-4">
                    <label for="telefono" class="control-label">Telefono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono" required>
                 </div>
             </div>
             <div class="form-row">
                <div class="form-group col-md-4">
                   <label for="telefono2" class="control-label">Telefono Adicional (opcional)</label>
                   <input type="text" class="form-control" id="telefono2" name="telefono2" placeholder="Telefono">
                </div>
            </div>
            <br>
            <div class="row">
                <a class="btn " href="{{URL::previous()}}">Regresar</a>
                <button type="submit" class="btn btn-danger" >Guardar</button>                    
            </div>
        </form>
        <br>
    </div>
@endsection
