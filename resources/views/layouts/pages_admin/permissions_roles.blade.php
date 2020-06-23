<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Pago | SIVyC Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <div style="text-align: right;width:60%">
            <label><h1>Registro de Pago</h1></label>
        </div>
        <br>
        <br>
        <form action="" method="post" id="registerpago" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <h2> Confirmación de Datos </h2>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-6">
                    <button type="submit" class="btn btn-primary" >Guardar</button>
                    <ul class="list-group">
                        <li class="list-group-item disabled">Cras justo odio</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Morbi leo risus</li>
                        <li class="list-group-item">Porta ac consectetur ac</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                    </ul>
                </div>
                <div class="form-gorup col-md-6">
                    <button type="submit" class="btn btn-primary" >Guardar</button>
                    <ul class="list-group">
                        @foreach ($permisos as $itemPermisos)
                            <li class="list-group-item disabled">{{ $itemPermisos->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <br>
@stop
